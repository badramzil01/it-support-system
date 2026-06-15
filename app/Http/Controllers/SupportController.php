<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupportWebhookRequest;
use App\Mail\SupportAlertMail;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Solution;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SupportController extends Controller
{
    private const VALID_STATUSES   = ['pending', 'resolved', 'waiting_support', 'closed', 'error'];
    private const VALID_PRIORITIES = ['low', 'medium', 'high', 'critical'];

    private function getWebhookSupportUrl(): string
    {
        return $this->n8nUrl('webhook_support', 'N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/support');
    }

    private function getWebhookJiraUrl(): string
    {
        return $this->n8nUrl('webhook_ticket', 'N8N_WEBHOOK_TICKET', 'http://localhost:5678/webhook/jira-ticket');
    }

    private function getRuntimeConfigForN8n(): array
    {
        $cfg = fn(string $svc, string $key, ?string $env = null) =>
            \App\Models\IntegrationConfig::getValue($svc, $key, $env);

        $laravelBaseUrl = \App\Models\IntegrationConfig::getValue('laravel', 'base_url') 
            ?? env('APP_URL') 
            ?? request()->getSchemeAndHttpHost();

        if (str_contains($laravelBaseUrl, 'localhost') || str_contains($laravelBaseUrl, '127.0.0.1')) {
            $laravelBaseUrl = request()->getSchemeAndHttpHost();
        }

        return [
            'laravel_api_url'     => rtrim($laravelBaseUrl, '/'),
            'n8n_webhook_support' => $this->getWebhookSupportUrl(),
            'n8n_webhook_ticket'  => $this->getWebhookJiraUrl(),
            'gemini_api_key'      => $cfg('gemini', 'api_key',   'GEMINI_API_KEY'),
            'gemini_model'        => $cfg('gemini', 'model',     'GEMINI_MODEL') ?? 'gemini-1.5-flash',
            'openrouter_api_key'  => $cfg('openrouter', 'api_key', 'OPENROUTER_API_KEY'),
            'openrouter_model'    => $cfg('openrouter', 'model',   'OPENROUTER_MODEL') ?? 'nvidia/nemotron-3-nano-30b-a3b:free',
            'jira_url'            => $cfg('jira', 'url',         'JIRA_URL'),
            'jira_token'          => $cfg('jira', 'token',       'JIRA_TOKEN'),
            'jira_project'        => $cfg('jira', 'project_key', 'JIRA_PROJECT_KEY') ?? 'SUP',
            'jira_email'          => $cfg('gmail', 'address',    'MAIL_FROM_ADDRESS'),
            'gmail_from'          => $cfg('gmail', 'address',   'MAIL_FROM_ADDRESS'),
            'gmail_to'            => $cfg('gmail', 'to_address', 'MAIL_TO_ADDRESS') ?? $cfg('gmail', 'address', 'MAIL_FROM_ADDRESS'),
            'support_email'       => $cfg('gmail', 'address',   'MAIL_FROM_ADDRESS'),
        ];
    }

    // n8n webhook URLs are now read dynamically from IntegrationConfig.
    // Fallback: host.docker.internal works when Laravel runs on host and n8n runs in Docker (Windows/Mac).
    private function n8nUrl(string $key, string $envFallback, string $defaultUrl): string
    {
        return \App\Models\IntegrationConfig::getValue('n8n', $key) 
            ?? env($envFallback) 
            ?? $defaultUrl;
    }

    // =========================================================================
    // PUBLIC ENDPOINTS
    // =========================================================================

    public function handle(SupportWebhookRequest $request)
    {
        set_time_limit(120);
        $startedAt = microtime(true);
        $traceId   = (string) Str::uuid();

        try {
            if (!$request->filled('message') && !$request->hasFile('image')) {
                return $this->apiError('Message ou image requis.', 422, [
                    'trace_id' => $traceId,
                    'status'   => 'error',
                ]);
            }

            $userId = $request->input('user_id') ?? auth()->id() ?? 1;
            // NOTE: $customerEmail is computed later (after conversation is loaded)
            // so we can pull it from the conversation->user->email if needed.
            $customerEmail = null;

            $originalMessage = trim((string) $request->input('message', ''));
            $detectMessage   = $this->normalizeForDetection($originalMessage);
            $language        = $this->detectLanguage($detectMessage);
            $image           = $this->handleImageUpload($request->file('image'), $traceId);
            $hasImage        = $image['has_image'];

            $conversation = $this->resolveConversation($request, $userId, $detectMessage, $hasImage);

            // Now that the conversation is loaded, we can resolve the customer email
            // from multiple sources (see resolveCustomerEmail()).
            $customerEmail = $this->resolveCustomerEmail($request, $userId, $conversation);

            Log::info('support.customer_email.resolved', [
                'trace_id'       => $traceId,
                'user_id'        => $userId,
                'customer_email' => $customerEmail,
            ]);

            // Save the user message — user_message = original message typed by user
            $userMessage = Message::create([
                'user_id'         => $userId,
                'conversation_id' => $conversation->id,
                'sender'          => 'user',
                'content'         => $originalMessage !== '' ? $originalMessage : 'Screenshot envoye',
                'response'        => null,
                'channel'         => 'web',
                'source'          => 'user',
                'status'          => 'sent',
                'image_path'      => $image['image_url'],
                'mime_type'       => $image['mime_type'] ?? null,
                'user_message'    => $originalMessage !== '' ? $originalMessage : null,
            ]);

            Log::info('support.user_message.saved', [
                'trace_id'     => $traceId,
                'context'      => 'user_row',
                'user_message' => $userMessage->user_message,
            ]);

            $isUrgent     = (bool) $request->boolean('is_urgent');
            $isEscalated  = (bool) $request->boolean('is_escalated');
            $createTicket = (bool) $request->boolean('create_ticket');

            $priority = $this->normalizePriority(
                $request->input('priority') ?: $this->detectPriority($detectMessage, $isUrgent, $isEscalated)
            );

            $category        = $this->detectCategory($detectMessage, $hasImage);
            $isSmallTalk     = $this->isSmallTalk($detectMessage);
            $problemDetected = $this->isProblemDetected($detectMessage, $hasImage);
            $isFrustrated    = $this->isFrustrated($detectMessage);

            $source         = 'ai';
            $status         = 'pending';
            $solution       = null;
            $confidence     = 0;
            $showTicket     = false;
            $n8nUserMessage = null;

            Log::info('support.handle.start', [
                'trace_id'         => $traceId,
                'user_id'          => $userId,
                'conversation_id'  => $conversation->id,
                'has_image'        => $hasImage,
                'is_urgent'        => $isUrgent,
                'is_escalated'     => $isEscalated,
                'create_ticket'    => $createTicket,
                'priority'         => $priority,
                'category'         => $category,
                'problem_detected' => $problemDetected,
                'is_small_talk'    => $isSmallTalk,
            ]);

            if ($isUrgent) {
                $priority   = 'high';
                $showTicket = true;
            }

            if ($isEscalated) {
                $priority   = 'high';
                $source     = 'support';
                $status     = 'waiting_support';
                $showTicket = true;
            }

            $reason = null;

            if ($isFrustrated) {
                $solution       = 'Votre demande a ete transferee a notre equipe support.';
                $source         = 'support';
                $status         = 'waiting_support';
                $priority       = 'high';
                $confidence     = 100;
                $showTicket     = true;
                $n8nUserMessage = null;
            } elseif ($hasImage) {
                $messages = $this->buildOpenRouterContext($conversation->id);
                $msgText = $originalMessage !== '' ? $originalMessage : 'Screenshot envoye';
                $aiResult = $this->callSupportWorkflow([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $userId,
                    'customer_email'  => $customerEmail,
                    'user_message'    => $msgText,
                    'message'         => $msgText,
                    'priority'        => $priority,
                    'category'        => $category,
                    'source'          => $request->input('source') ?? 'web',
                    'is_urgent'       => $isUrgent,
                    'is_escalated'    => $isEscalated,
                    'create_ticket'   => $createTicket,
                    'image_url'       => $image['image_url'] ?? '',
                    'image_base64'    => $image['image_base64'] ?? '',
                    'mime_type'       => $image['mime_type'] ?? '',
                    'has_image'       => true,
                    'messages'        => $messages,
                ], $traceId, 45);

                $solution       = $aiResult['message'];
                $n8nUserMessage = $aiResult['user_message'] ?? null;
                $source         = $aiResult['ok'] ? 'vision_ai' : 'system';
                $status         = $aiResult['ok'] ? 'resolved' : 'error';
                $confidence     = $aiResult['ok'] ? 92 : 0;
                $showTicket     = true;

                if ($aiResult['ok']) {
                    $isUrgent     = $aiResult['is_urgent'];
                    $isEscalated  = $aiResult['is_escalated'];
                    $createTicket = $aiResult['create_ticket'];
                    $priority     = $aiResult['priority'];
                    $category     = $aiResult['category'];
                    $reason       = $aiResult['reason'];
                }
            } else {
                $dbSolution = $this->findDatabaseSolution($detectMessage);

                if ($dbSolution) {
                    $solution       = $dbSolution['solution'];
                    $source         = 'db';
                    $status         = 'resolved';
                    $confidence     = $dbSolution['confidence'];
                    $showTicket     = true;
                    $n8nUserMessage = null;

                    $this->fireAndLogSupportWebhook([
                        'user_id'         => $userId,
                        'conversation_id' => $conversation->id,
                        'customer_email'  => $customerEmail,
                        'message'         => $originalMessage,
                        'message_clean'   => $detectMessage,
                        'user_message'    => $originalMessage,
                        'response'        => [
                            'message'         => $solution,
                            'is_urgent'       => $isUrgent,
                            'is_escalated'    => $isEscalated,
                            'create_ticket'   => $createTicket,
                            'priority'        => $priority,
                            'category'        => $category,
                            'customer_email'  => $customerEmail,
                        ],
                        'source'        => 'db',
                        'priority'      => $priority,
                        'category'      => $category,
                        'status'        => $status,
                        'confidence'    => $confidence,
                        'is_urgent'     => $isUrgent,
                        'is_escalated'  => $isEscalated,
                        'create_ticket' => $createTicket,
                        'has_image'     => false,
                        'image_url'     => '',
                        'image_base64'  => '',
                        'mime_type'     => '',
                    ], $traceId);
                } else {
                    $messages = $this->buildOpenRouterContext($conversation->id);
                    $aiResult = $this->callSupportWorkflow([
                        'conversation_id' => $conversation->id,
                        'user_id'         => $userId,
                        'customer_email'  => $customerEmail,
                        'user_message'    => $originalMessage,
                        'message'         => $originalMessage,
                        'priority'        => $priority,
                        'category'        => $category,
                        'source'          => $request->input('source') ?? 'web',
                        'is_urgent'       => $isUrgent,
                        'is_escalated'    => $isEscalated,
                        'create_ticket'   => $createTicket,
                        'image_url'       => '',
                        'image_base64'    => '',
                        'mime_type'       => '',
                        'has_image'       => false,
                        'messages'        => $messages,
                    ], $traceId, 30);

                    $solution       = $aiResult['message'];
                    $n8nUserMessage = $aiResult['user_message'] ?? null;
                    $source         = $aiResult['ok'] ? 'ai' : 'system';
                    $status         = $aiResult['ok'] ? 'resolved' : 'error';
                    $confidence     = $aiResult['ok'] ? 85 : 0;
                    $showTicket     = $aiResult['ok'];

                    if ($aiResult['ok']) {
                        $isUrgent     = $aiResult['is_urgent'];
                        $isEscalated  = $aiResult['is_escalated'];
                        $createTicket = $aiResult['create_ticket'];
                        $priority     = $aiResult['priority'];
                        $category     = $aiResult['category'];
                        $reason       = $aiResult['reason'];

                        if ($detectMessage !== '') {
                            Solution::updateOrCreate(
                                ['question' => $detectMessage],
                                ['solution' => $solution]
                            );
                        }
                    }
                }
            }

            // Save AI response to DB if AI was used successfully
            if (in_array($source, ['ai', 'vision_ai']) && $status !== 'error') {
                \App\Models\AIResponse::create([
                    'conversation_id' => $conversation->id,
                    'user_id'         => $userId,
                    'message'         => $solution,
                    'user_message'    => $originalMessage !== '' ? $originalMessage : 'Screenshot envoye',
                    'ai_response'     => $solution,
                    'source'          => $source,
                    'priority'        => $priority,
                    'category'        => $category,
                    'reason'          => $reason,
                    'is_urgent'       => $isUrgent,
                    'is_escalated'    => $isEscalated,
                    'create_ticket'   => $createTicket,
                    'has_image'       => $hasImage,
                    'image_url'       => $image['image_url'],
                    'mime_type'       => $image['mime_type'] ?? null,
                ]);
            }

            Log::info('support.n8n.user_message_extracted', [
                'trace_id'     => $traceId,
                'user_message' => $n8nUserMessage,
            ]);

            if (!$solution || trim($solution) === '') {
                $solution   = 'Je suis desole, je n ai pas recu de reponse IA exploitable. Merci de reessayer.';
                $source     = 'system';
                $status     = 'error';
                $confidence = 0;
            }

            if (!in_array($status, self::VALID_STATUSES, true)) {
                $status = 'error';
            }

            if ($conversation->current_category
                && $conversation->current_category !== $category
                && $problemDetected
            ) {
                $conversation->update([
                    'ticket_created'    => false,
                    'current_ticket_id' => null,
                    'current_problem'   => null,
                    'current_category'  => null,
                ]);
                $conversation->refresh();
            }

            $botRowUserMessage = $n8nUserMessage ?? ($originalMessage !== '' ? $originalMessage : null);

            Log::info('support.user_message.before_save', [
                'trace_id'         => $traceId,
                'n8n_user_message' => $n8nUserMessage,
                'original_message' => $originalMessage,
                'bot_row_user_msg' => $botRowUserMessage,
            ]);

            $botMessage = Message::create([
                'user_id'         => $userId,
                'conversation_id' => $conversation->id,
                'sender'          => 'bot',
                'content'         => $solution,
                'response'        => $solution,
                'channel'         => 'web',
                'source'          => $source,
                'status'          => $status,
                'image_path'      => null,
                'user_message'    => $botRowUserMessage,
            ]);

            Log::info('support.user_message.saved', [
                'trace_id'     => $traceId,
                'context'      => 'bot_row',
                'message_id'   => $botMessage->id,
                'user_message' => $botMessage->user_message,
            ]);

            $ticket             = null;
            $ticketId           = null;
            $shouldCreateTicket = $this->shouldCreateTicket(
                $conversation,
                $isSmallTalk,
                $problemDetected,
                $isUrgent,
                $isEscalated,
                $createTicket
            );

            Log::info('support.ticket.decision', [
                'trace_id'          => $traceId,
                'should_create'     => $shouldCreateTicket,
                'is_small_talk'     => $isSmallTalk,
                'problem_detected'  => $problemDetected,
                'is_urgent'         => $isUrgent,
                'is_escalated'      => $isEscalated,
                'create_ticket'     => $createTicket,
                'ticket_created'    => $conversation->ticket_created,
                'current_ticket_id' => $conversation->current_ticket_id,
            ]);

            if ($shouldCreateTicket) {
                $ticketTitle = $this->buildTicketTitle(
                    $originalMessage,
                    $detectMessage,
                    $category,
                    $hasImage
                );

                $ticketDescription = $originalMessage !== ''
                    ? $originalMessage
                    : ($hasImage ? 'Analyse de screenshot demandee.' : $detectMessage);

                // Log payload just before creating the ticket (help debug external callers)
                Log::info('BEFORE TICKET CREATE', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'is_escalated' => $isEscalated,
                    'is_urgent' => $isUrgent,
                    'priority' => $priority,
                    'category' => $category,
                    'title' => $ticketTitle,
                    'description' => $ticketDescription,
                ]);

                // FIX: pass user_id, conversation_id, is_escalated, is_urgent explicitly
                $ticket = $this->createSupportTicket([
                    'user_id'         => $userId,
                    'conversation_id' => $conversation->id,
                    'message_id'      => $userMessage->id,
                    'trigger_message_id' => $userMessage->id,
                    'title'           => $ticketTitle,
                    'description'     => $ticketDescription,
                    'solution'        => $solution,
                    'source'          => $source,
                    'priority'        => $priority,
                    'category'        => $category,
                    'confidence'      => $confidence,
                    'is_urgent'       => $isUrgent,
                    'is_escalated'    => $isEscalated,
                    'has_image'       => $hasImage,
                    'image_url'       => $image['image_url'] ?? null,
                    'mime_type'       => $image['mime_type'] ?? null,
                ], $traceId);

                $conversation->update([
                    'ticket_created'   => true,
                    'current_problem'  => $detectMessage ?: $originalMessage,
                    'current_category' => $category,
                ]);

                $jiraKey = $this->sendJiraWebhookAndGetKey([
                    'laravel_ticket_id' => $ticket->id,
                    'customer_email'    => $customerEmail,
                    'user_message'      => $originalMessage,
                    'message'           => $originalMessage,
                    'message_clean'     => $detectMessage,
                    'priority'          => $priority,
                    'category'          => $category,
                    'status'            => $status,
                    'source'            => $source,
                    'confidence'        => $confidence,
                    'solution'          => $solution,
                    'is_urgent'         => $isUrgent,
                    'is_escalated'      => $isEscalated,
                    'create_ticket'     => $createTicket,
                    'has_image'         => $hasImage,
                    'conversation_id'   => $conversation->id,
                    'user_id'           => $userId,
                    'language'          => $language,
                    'title'             => $ticketTitle,
                    'description'       => $ticketDescription,
                ], $traceId);

                if ($jiraKey) {
                    $ticket->update(['jira_ticket_id' => $jiraKey]);
                    $ticketId = $jiraKey;
                    $conversation->update(['current_ticket_id' => $jiraKey]);

                    Log::info('support.jira.key.saved', [
                        'trace_id'       => $traceId,
                        'ticket_id'      => $ticket->id,
                        'jira_ticket_id' => $jiraKey,
                    ]);
                }
            } else {
                $ticketId = $conversation->current_ticket_id;
            }

            if ($ticket && ($priority === 'high' || $priority === 'critical' || $status === 'waiting_support')) {
                $this->sendMailSafe($ticket, $traceId);
            }

            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

            Log::info('support.handle.done', [
                'trace_id'        => $traceId,
                'conversation_id' => $conversation->id,
                'status'          => $status,
                'source'          => $source,
                'ticket_id'       => $ticketId,
                'user_message'    => $botRowUserMessage,
                'elapsed_ms'      => $elapsedMs,
            ]);

            return $this->apiSuccess([
                'message'         => 'Reponse IA recue.',
                'solution'        => $solution,
                'user_message'    => $botRowUserMessage,
                'ticket_id'       => $ticketId ?? $conversation->current_ticket_id,
                'jira_ticket_id'  => $ticketId ?? $conversation->current_ticket_id,
                'show_ticket'     => $showTicket || $shouldCreateTicket,
                'source'          => $source,
                'priority'        => $priority,
                'category'        => $category,
                'confidence'      => $confidence,
                'conversation_id' => $conversation->id,
                'image_url'       => $image['image_url'],
                'is_urgent'       => $isUrgent,
                'is_escalated'    => $isEscalated,
                'create_ticket'   => $createTicket || $shouldCreateTicket,
                'language'        => $language,
                'status'          => $status,
                'trace_id'        => $traceId,
                'elapsed_ms'      => $elapsedMs,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'trace_id' => $traceId,
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            return $this->apiError('Erreur connexion IA.', 500, [
                'trace_id' => $traceId,
                'status'   => 'error',
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public function updateJiraKey(Request $request, int $id)
    {
        try {
            $request->validate([
                'jira_ticket_id' => 'required|string|max:80',
            ]);

            $ticket  = Ticket::findOrFail($id);
            $jiraKey = trim($request->input('jira_ticket_id'));

            if (!empty($ticket->jira_ticket_id)) {
                return response()->json([
                    'success'        => true,
                    'message'        => 'Jira key already exists.',
                    'ticket_id'      => $ticket->id,
                    'jira_ticket_id' => $ticket->jira_ticket_id,
                ]);
            }

            $ticket->update(['jira_ticket_id' => $jiraKey]);

            if ($ticket->message_id) {
                $conversationId = Message::where('id', $ticket->message_id)
                    ->value('conversation_id');

                if ($conversationId) {
                    Conversation::where('id', $conversationId)
                        ->update(['current_ticket_id' => $jiraKey]);
                }
            }

            Log::info('support.jira.key.saved', [
                'ticket_id'      => $ticket->id,
                'jira_ticket_id' => $jiraKey,
            ]);

            return response()->json([
                'success'        => true,
                'message'        => 'Jira key saved.',
                'ticket_id'      => $ticket->id,
                'jira_ticket_id' => $jiraKey,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'updateJiraKey',
                'message' => $e->getMessage(),
            ]);

            return $this->apiError('Erreur mise a jour jira_ticket_id.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function history($conversationId)
    {
        try {
            if (!ctype_digit((string) $conversationId)) {
                return $this->apiError('conversation_id invalide.', 422);
            }

            $messages = Message::where('conversation_id', (int) $conversationId)
                ->orderBy('created_at', 'asc')
                ->get([
                    'id', 'user_id', 'conversation_id', 'sender',
                    'content', 'response', 'image_path', 'source',
                    'status', 'user_message', 'created_at',
                ]);

            return response()->json([
                'success'  => true,
                'messages' => $messages,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'history',
                'message' => $e->getMessage(),
            ]);
            return $this->apiError('Erreur chargement historique.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getMessages($conversationId)
    {
        return $this->history($conversationId);
    }

    public function getConversations($userId)
    {
        try {
            if (!ctype_digit((string) $userId)) {
                return $this->apiError('user_id invalide.', 422);
            }

            if (auth()->check() && !auth()->user()->isSupport() && (int) $userId !== auth()->id()) {
                return $this->apiError('Acces refuse.', 403);
            }

            $conversations = Conversation::query()
                ->where('user_id', (int) $userId)
                ->with('latestMessage')
                ->withCount('messages')
                ->latest('updated_at')
                ->get()
                ->map(fn ($conversation) => [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?: 'Conversation #'.$conversation->id,
                    'updated_at' => $conversation->updated_at,
                    'messages_count' => $conversation->messages_count,
                    'last_message' => $conversation->latestMessage?->content,
                    'ticket_created' => (bool) $conversation->ticket_created,
                    'current_ticket_id' => $conversation->current_ticket_id,
                    'category' => $conversation->current_category,
                ]);

            return response()->json([
                'success' => true,
                'conversations' => $conversations,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'getConversations',
                'message' => $e->getMessage(),
            ]);

            return $this->apiError('Erreur chargement conversations.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Return a compact JSON payload for the UI user panel (conversations + stats + last ticket)
     */
    public function userPanel($userId)
    {
        try {
            if (!ctype_digit((string) $userId)) {
                return $this->apiError('user_id invalide.', 422);
            }

            $user = \App\Models\User::find((int) $userId);
            if (!$user) return $this->apiError('Utilisateur introuvable.', 404);

            $conversations = Conversation::query()
                ->where('user_id', (int) $userId)
                ->with('latestMessage')
                ->withCount('messages')
                ->latest('updated_at')
                ->get()
                ->map(fn ($conversation) => [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?: 'Conversation #'.$conversation->id,
                    'updated_at' => $conversation->updated_at,
                    'messages_count' => $conversation->messages_count,
                    'latest_message' => $conversation->latestMessage?->content,
                    'has_ticket' => (bool) $conversation->ticket_created,
                ]);

            $lastTicket = Ticket::where('user_id', (int) $userId)->latest()->first();

            $stats = [
                'conversations' => Conversation::where('user_id', (int) $userId)->count(),
                'tickets' => Ticket::where('user_id', (int) $userId)->count(),
                'urgent' => Ticket::where('user_id', (int) $userId)->where('is_urgent', 1)->count(),
                'escalated' => Ticket::where('user_id', (int) $userId)->where('is_escalated', 1)->count(),
            ];

            return response()->json([
                'success' => true,
                'user' => [ 'id' => $user->id, 'name' => $user->name, 'email' => $user->email ],
                'conversations' => $conversations,
                'stats' => $stats,
                'last_ticket' => $lastTicket ? [ 'id' => $lastTicket->id, 'title' => $lastTicket->title ] : null,
            ]);

        } catch (\Throwable $e) {
            Log::error('support.error', ['context' => 'userPanel', 'message' => $e->getMessage()]);
            return $this->apiError('Erreur chargement panneau utilisateur.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Return conversation + messages payload for the UI
     */
    public function conversationPanel($convId)
    {
        try {
            if (!ctype_digit((string) $convId)) {
                return $this->apiError('conversation_id invalide.', 422);
            }

            $conversation = Conversation::with('user')->find((int) $convId);
            if (!$conversation) return $this->apiError('Conversation introuvable.', 404);

            $messages = Message::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'asc')
                ->get([
                    'id','user_id','conversation_id','sender','content','response','image_path','source','status','user_message','created_at'
                ]);

            return response()->json([
                'success' => true,
                'conversation' => [ 'id' => $conversation->id, 'title' => $conversation->title, 'updated_at' => $conversation->updated_at ],
                'messages' => $messages,
                'selected_user' => [ 'id' => $conversation->user->id, 'name' => $conversation->user->name ]
            ]);

        } catch (\Throwable $e) {
            Log::error('support.error', ['context' => 'conversationPanel', 'message' => $e->getMessage()]);
            return $this->apiError('Erreur chargement conversation.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function feedback(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|integer',
                'feedback'  => 'required|string|in:resolved,unresolved',
            ]);

            $ticket = Ticket::findOrFail((int) $request->ticket_id);

            if ($request->feedback === 'resolved') {
                $ticket->update(['feedback' => 'resolved', 'status' => 'closed']);

                return response()->json([
                    'success'   => true,
                    'message'   => 'Merci pour votre retour.',
                    'status'    => 'closed',
                    'ticket_id' => $ticket->id,
                ]);
            }

            $ticket->update(['feedback' => 'unresolved', 'status' => 'waiting_support']);
            $this->sendMailSafe($ticket, (string) Str::uuid());

            return response()->json([
                'success'   => true,
                'message'   => 'Votre ticket a ete transfere au support.',
                'status'    => 'waiting_support',
                'ticket_id' => $ticket->id,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'feedback',
                'message' => $e->getMessage(),
            ]);
            return $this->apiError('Erreur feedback.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function ticketStatus($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail((int) $ticketId);

            return response()->json([
                'success'        => true,
                'ticket_id'      => $ticket->id,
                'jira_ticket_id' => $ticket->jira_ticket_id,
                'status'         => $ticket->status,
                'priority'       => $ticket->priority,
                'category'       => $ticket->category,
                'source'         => $ticket->source,
                'solution'       => $ticket->solution,
                'confidence'     => $ticket->confidence,
                'feedback'       => $ticket->feedback,
                'created_at'     => $ticket->created_at,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'ticketStatus',
                'message' => $e->getMessage(),
            ]);
            return $this->apiError('Erreur statut ticket.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function receiveAiResponse(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|integer',
                'user_id'         => 'required|integer',
                'solution'        => 'nullable|string',
                'message'         => 'nullable|string',
                'ai_response'     => 'nullable|string',
                'user_message'    => 'nullable|string',
                'source'          => 'nullable|string|max:40',
                'confidence'      => 'nullable|integer|min:0|max:100',
                'ticket_id'       => 'nullable|integer',
            ]);

            $aiMessage = trim((string) (
                $request->input('solution')
                ?: $request->input('message')
                ?: $request->input('ai_response')
            ));

            if ($aiMessage === '') {
                return $this->apiError('AI message required.', 422);
            }

            $n8nUserMessage = $request->input('user_message');

            Log::info('support.n8n.user_message_extracted', [
                'context'         => 'receiveAiResponse',
                'conversation_id' => $request->conversation_id,
                'user_message'    => $n8nUserMessage,
            ]);

            $msg = Message::create([
                'user_id'         => $request->user_id,
                'conversation_id' => $request->conversation_id,
                'sender'          => 'bot',
                'content'         => $aiMessage,
                'response'        => $aiMessage,
                'channel'         => 'n8n',
                'source'          => $request->input('source', 'ai'),
                'status'          => 'resolved',
                'image_path'      => null,
                'user_message'    => $n8nUserMessage,
            ]);

            Log::info('support.user_message.saved', [
                'context'         => 'receiveAiResponse',
                'conversation_id' => $request->conversation_id,
                'message_id'      => $msg->id,
                'user_message'    => $msg->user_message,
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'AI response saved.',
                'user_message' => $msg->user_message,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'receiveAiResponse',
                'message' => $e->getMessage(),
            ]);
            return $this->apiError('Erreur sauvegarde reponse IA.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function createTicket(Request $request)
    {
        try {
            $request->validate([
                'message'  => 'required|string|max:3000',
                'priority' => 'nullable|string|in:low,medium,high,critical',
                'category' => 'nullable|string|max:80',
                'user_id'  => 'nullable|integer',
            ]);

            $traceId  = (string) Str::uuid();
            $priority = $this->normalizePriority($request->input('priority', 'medium'));
            $category = $request->input('category', 'general');
            $userId   = $request->input('user_id', auth()->id() ?? 1);

            Log::info('BEFORE TICKET CREATE', [
                'conversation_id' => null,
                'user_id' => $userId,
                'is_escalated' => false,
                'is_urgent' => false,
                'priority' => $priority,
                'category' => $category,
                'title' => $this->buildTicketTitle($request->message, '', $category, false),
                'description' => $request->message,
            ]);

            $ticket = $this->createSupportTicket([
                'user_id'         => $userId,
                'conversation_id' => null,
                'message_id'      => null,
                'title'           => $this->buildTicketTitle($request->message, '', $category, false),
                'description'     => $request->message,
                'solution'        => null,
                'source'          => 'manual',
                'priority'        => $priority,
                'category'        => $category,
                'confidence'      => 0,
                'is_urgent'       => false,
                'is_escalated'    => false,
                'has_image'       => false,
                'image_url'       => null,
                'mime_type'       => null,
                'ticket_status'   => 'pending',
            ], $traceId);

            $jiraKey = $this->sendJiraWebhookAndGetKey([
                'laravel_ticket_id' => $ticket->id,
                'message'           => $request->message,
                'priority'          => $ticket->priority,
                'category'          => $ticket->category,
                'status'            => $ticket->status,
                'source'            => $ticket->source,
                'user_id'           => $userId,
                'title'             => $ticket->title,
                'description'       => $ticket->description,
            ], $traceId);

            if ($jiraKey) {
                $ticket->update(['jira_ticket_id' => $jiraKey]);

                Log::info('support.jira.key.saved', [
                    'trace_id'       => $traceId,
                    'ticket_id'      => $ticket->id,
                    'jira_ticket_id' => $jiraKey,
                ]);
            }

            return response()->json([
                'success'        => true,
                'message'        => 'Ticket cree.',
                'ticket_id'      => $ticket->id,
                'jira_ticket_id' => $ticket->jira_ticket_id,
                'status'         => $ticket->status,
                'priority'       => $ticket->priority,
                'category'       => $ticket->category,
            ]);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context' => 'createTicket',
                'message' => $e->getMessage(),
            ]);
            return $this->apiError('Erreur creation ticket.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // PRIVATE — conversation
    // =========================================================================

    /**
     * Resolve the customer email from up to 5 sources (in priority order).
     * GUARANTEES that a non-empty string is always returned.
     *
     * Source 1: $request->input('customer_email')   — explicit front-end field
     * Source 2: Auth::user()->email                 — currently authenticated user
     * Source 3: User::find($userId)->email          — user referenced by user_id
     * Source 4: $conversation->user->email          — owner of the conversation
     * Source 5: MAIL_FROM_ADDRESS env               — support mailbox as ultimate fallback
     */
    private function resolveCustomerEmail(Request $request, int $userId, ?Conversation $conversation = null): string
    {
        $fallback = (string) (env('MAIL_FROM_ADDRESS') ?: 'client@example.com');

        // 1) Direct field from request
        $email = trim((string) $request->input('customer_email', ''));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        // 2) Authenticated user
        try {
            if (Auth::check()) {
                $authEmail = trim((string) Auth::user()->email);
                if ($authEmail !== '' && filter_var($authEmail, FILTER_VALIDATE_EMAIL)) {
                    return $authEmail;
                }
            }
        } catch (Throwable $e) {
            // ignore
        }

        // 3) User by id
        try {
            if ($userId > 0) {
                $user = User::find($userId);
                if ($user) {
                    $uEmail = trim((string) $user->email);
                    if ($uEmail !== '' && filter_var($uEmail, FILTER_VALIDATE_EMAIL)) {
                        return $uEmail;
                    }
                }
            }
        } catch (Throwable $e) {
            // ignore
        }

        // 4) Conversation owner
        try {
            if ($conversation && $conversation->user_id) {
                // Use the user relation if not yet loaded
                $ownerEmail = trim((string) ($conversation->user?->email ?? ''));
                if ($ownerEmail !== '' && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
                    return $ownerEmail;
                }
                // Fallback: load explicitly
                $owner = User::find($conversation->user_id);
                if ($owner) {
                    $ownerEmail = trim((string) $owner->email);
                    if ($ownerEmail !== '' && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
                        return $ownerEmail;
                    }
                }
            }
        } catch (Throwable $e) {
            // ignore
        }

        // 5) Ultimate fallback
        return $fallback;
    }

    private function resolveConversation(Request $request, int $userId, string $detectMessage, bool $hasImage): Conversation
    {
        $conversationId = $request->input('conversation_id');

        if (ctype_digit((string) $conversationId)) {
            $conversation = Conversation::where('id', (int) $conversationId)
                ->where('user_id', $userId)
                ->first();

            if ($conversation) {
                return $conversation;
            }
        }

        return Conversation::create([
            'user_id' => $userId,
            'title'   => Str::limit(
                $detectMessage ?: ($hasImage ? 'Screenshot Support' : 'Nouvelle conversation'),
                50,
                ''
            ),
        ]);
    }

    // =========================================================================
    // PRIVATE — ticket
    // =========================================================================

    private function shouldCreateTicket(
        Conversation $conversation,
        bool $isSmallTalk,
        bool $problemDetected,
        bool $isUrgent,
        bool $isEscalated,
        bool $createTicket
    ): bool {
        if ($isSmallTalk) {
            return false;
        }

        if ($conversation->ticket_created) {
            return false;
        }

        if ($isUrgent || $isEscalated || $createTicket) {
            return true;
        }

        return $problemDetected;
    }

    /**
     * Create a single ticket row.
     *
     * Required keys in $data:
     *   user_id         — the authenticated user's ID (never null)
     *   conversation_id — the conversation this ticket belongs to
     *   is_escalated    — boolean, passed explicitly from handle()
     *   is_urgent       — boolean, passed explicitly from handle()
     *
     * All other keys are optional with safe defaults.
     */
    private function createSupportTicket(array $data, string $traceId): Ticket
    {
        $title = trim($data['title'] ?? '');
        if ($title === '' || strtolower($title) === 'support ticket') {
            $title = 'Probleme signale — ' . ucfirst($data['category'] ?? 'general');
        }

        $description = trim($data['description'] ?? '');
        if ($description === '' || strtolower($description) === 'no description') {
            $description = 'Aucune description fournie par l utilisateur.';
        }

        // FIX: user_id and conversation_id are now taken directly from $data,
        // which is always populated by the callers (handle() and createTicket()).
        // The old version used $conversation->id / $conversation->user_id
        // from a variable that did not exist in this scope → always null.
        $userId         = $data['user_id']         ?? null;
        $conversationId = $data['conversation_id'] ?? null;
        $isEscalated    = (bool) ($data['is_escalated'] ?? false);
        $isUrgent       = (bool) ($data['is_urgent']    ?? false);

        Log::info('support.ticket.before_create', [
            'trace_id'        => $traceId,
            'user_id'         => $userId,
            'conversation_id' => $conversationId,
            'is_escalated'    => $isEscalated,
            'is_urgent'       => $isUrgent,
            'priority'        => $data['priority'] ?? 'medium',
            'category'        => $data['category'] ?? 'general',
        ]);
        Log::info('SUPPORT TICKET CREATE', [
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'is_escalated' => $isEscalated,
        ]);
        $ticket = Ticket::create([
            'user_id'         => $userId,
            'conversation_id' => $conversationId,
            'message_id'      => $data['message_id']      ?? null,
            'trigger_message_id' => $data['trigger_message_id'] ?? $data['message_id'] ?? null,
            'jira_ticket_id'  => null,
            'title'           => Str::limit($title, 120, ''),
            'description'     => $description,
            'solution'        => $data['solution']        ?? null,
            'source'          => $data['source']          ?? 'ai',
            'priority'        => $this->normalizePriority($data['priority'] ?? 'medium'),
            'category'        => $data['category']        ?: 'general',
            'confidence'      => (int) ($data['confidence'] ?? 0),
            'status'          => 'open',
            'feedback'        => 'pending',
            'is_urgent'       => $isUrgent,
            'is_escalated'    => $isEscalated,
            'has_image'       => (bool) ($data['has_image'] ?? false),
            'image_url'       => $data['image_url']       ?? null,
            'mime_type'       => $data['mime_type']       ?? null,
            'ticket_status'   => 'pending',
        ]);

        Log::info('support.ticket.created', [
            'trace_id'        => $traceId,
            'id'              => $ticket->id,
            'user_id'         => $ticket->user_id,
            'conversation_id' => $ticket->conversation_id,
            'is_escalated'    => $ticket->is_escalated,
            'is_urgent'       => $ticket->is_urgent,
            'title'           => $ticket->title,
            'priority'        => $ticket->priority,
            'category'        => $ticket->category,
            'status'          => $ticket->status,
        ]);

        return $ticket;
    }

    private function sendJiraWebhookAndGetKey(array $payload, string $traceId): ?string
    {
        $startedAt = microtime(true);

        try {
            $payload['runtime_config'] = $this->getRuntimeConfigForN8n();
            Log::info('support.jira.webhook.request', [
                'trace_id'          => $traceId,
                'url'               => $this->getWebhookJiraUrl(),
                'laravel_ticket_id' => $payload['laravel_ticket_id'] ?? null,
                'category'          => $payload['category']          ?? null,
                'priority'          => $payload['priority']          ?? null,
            ]);

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->getWebhookJiraUrl(), $payload);

            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

            Log::info('support.jira.webhook.response', [
                'trace_id'    => $traceId,
                'http_status' => $response->status(),
                'successful'  => $response->successful(),
                'elapsed_ms'  => $elapsedMs,
                'body'        => Str::limit($response->body(), 4000),
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data    = $response->json();
            $jiraKey = data_get($data, 'jira_ticket_id')
                    ?? data_get($data, 'key')
                    ?? data_get($data, 'ticket_id')
                    ?? data_get($data, 'id');

            if (is_string($jiraKey) && preg_match('/^[A-Z]+-\d+$/', $jiraKey)) {
                return $jiraKey;
            }

            return null;

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context'  => 'sendJiraWebhookAndGetKey',
                'trace_id' => $traceId,
                'message'  => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function buildTicketTitle(string $originalMessage, string $detectMessage, string $category, bool $hasImage): string
    {
        if ($hasImage) {
            return 'Analyse de screenshot — ' . ucfirst($category);
        }

        $base = $originalMessage !== '' ? $originalMessage : $detectMessage;

        if ($base !== '') {
            return Str::limit(ucfirst($base), 100, '');
        }

        return 'Probleme signale — ' . ucfirst($category);
    }

    // =========================================================================
    // PRIVATE — n8n / AI
    // =========================================================================

    private function callSupportWorkflow(array $payload, string $traceId, int $timeoutSeconds): array
    {
        $startedAt = microtime(true);

        // Guarantee the 3 boolean flags are present and explicitly cast to true/false
        // in the payload sent to n8n (in case some caller forgot to pass them).
        $payload['is_urgent']     = (bool) ($payload['is_urgent']     ?? false);
        $payload['is_escalated']  = (bool) ($payload['is_escalated']  ?? false);
        $payload['create_ticket'] = (bool) ($payload['create_ticket'] ?? false);

        Log::info('support.n8n.outgoing_payload', [
            'trace_id'      => $traceId,
            'is_urgent'     => $payload['is_urgent'],
            'is_escalated'  => $payload['is_escalated'],
            'create_ticket' => $payload['create_ticket'],
            'has_image'     => (bool) ($payload['has_image'] ?? false),
            'priority'      => $payload['priority'] ?? null,
            'category'      => $payload['category'] ?? null,
        ]);

        try {
            $result = \App\Services\N8nService::sendSupportWebhook($payload, $timeoutSeconds);

            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

            if (!$result['ok']) {
                return [
                    'ok'            => false,
                    'message'       => $result['message'] ?? 'Service IA indisponible actuellement. Merci de reessayer.',
                    'user_message'  => null,
                    'is_urgent'     => false,
                    'is_escalated'  => false,
                    'create_ticket' => false,
                    'priority'      => 'medium',
                    'category'      => 'general',
                    'reason'        => null,
                    'status'        => $result['status'] ?? 500,
                ];
            }

            $parsed = $this->parseFullN8nResponse($result['body'], $traceId);

            Log::info('support.n8n.workflow_result', [
                'trace_id'     => $traceId,
                'message'      => $parsed['message'] ? Str::limit($parsed['message'], 200) : null,
                'user_message' => $parsed['user_message'],
            ]);

            if (!$parsed['message']) {
                return [
                    'ok'            => false,
                    'message'       => 'Je suis desole, la reponse IA etait vide ou invalide. Merci de reessayer.',
                    'user_message'  => null,
                    'is_urgent'     => false,
                    'is_escalated'  => false,
                    'create_ticket' => false,
                    'priority'      => 'medium',
                    'category'      => 'general',
                    'reason'        => null,
                    'status'        => $result['status'],
                ];
            }

            return array_merge([
                'ok'     => true,
                'status' => $result['status'],
            ], $parsed);

        } catch (Throwable $e) {
            Log::error('support.error', [
                'context'  => 'callSupportWorkflow',
                'trace_id' => $traceId,
                'message'  => $e->getMessage(),
            ]);

            return [
                'ok'            => false,
                'message'       => 'Erreur connexion IA. Merci de reessayer dans quelques instants.',
                'user_message'  => null,
                'is_urgent'     => false,
                'is_escalated'  => false,
                'create_ticket' => false,
                'priority'      => 'medium',
                'category'      => 'general',
                'reason'        => null,
                'status'        => 0,
            ];
        }
    }

    /**
     * Parse the raw n8n response body.
     *
     * Supports all three shapes n8n may return:
     *   - { "message": "...", "user_message": "..." }
     *   - { "data": { "message": "...", "user_message": "..." } }
     *   - [ { "message": "...", "user_message": "..." } ]
     */
    private function parseFullN8nResponse(string $responseBody, string $traceId): array
    {
        $default = [
            'message'       => null,
            'user_message'  => null,
            'is_urgent'     => false,
            'is_escalated'  => false,
            'create_ticket' => false,
            'priority'      => 'medium',
            'category'      => 'general',
            'reason'        => null,
        ];

        $body = trim($responseBody);

        if ($body === '') {
            Log::warning('support.extract.empty_response', ['trace_id' => $traceId]);
            return $default;
        }

        if (Str::contains(strtolower($body), ['<!doctype', '<html'])) {
            Log::warning('support.extract.html_response', [
                'trace_id' => $traceId,
                'body'     => Str::limit($body, 1000),
            ]);
            return $default;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('support.extract.invalid_json', [
                'trace_id'   => $traceId,
                'json_error' => json_last_error_msg(),
                'body'       => Str::limit($body, 2000),
            ]);
            return $default;
        }

        $root = $data;
        if (is_array($data) && array_is_list($data) && count($data) > 0) {
            $root = $data[0];
        }

        Log::info('support.n8n.full_root', [
            'trace_id' => $traceId,
            'root'     => is_array($root) ? $root : ['raw' => $root],
        ]);

        $responseNode = null;
        if (is_array($root)) {
            if (isset($root['response']) && is_array($root['response'])) {
                $responseNode = $root['response'];
            } elseif (isset($root['data']) && is_array($root['data'])) {
                $responseNode = $root['data'];
            }
        }

        $target = $responseNode ?? $root;

        if (!is_array($target)) {
            return $default;
        }

        $aiMessage = $target['message'] ?? $target['ai_response'] ?? $target['solution'] ?? null;
        if (is_array($aiMessage)) {
            $aiMessage = $this->extractMessageFromMixed($aiMessage);
        }

        $userMessage = $target['user_message'] ?? null;
        if (!$userMessage && is_array($root)) {
            $userMessage = $root['user_message'] ?? null;
        }

        Log::info('support.n8n.parsed_response', [
            'trace_id'     => $traceId,
            'shape'        => is_array($target) ? array_keys($target) : gettype($target),
            'user_message' => $userMessage,
        ]);

        return [
            'message'       => $aiMessage ? trim((string)$aiMessage) : null,
            'user_message'  => $userMessage ? trim((string)$userMessage) : null,
            'is_urgent'     => (bool)($target['is_urgent'] ?? false),
            'is_escalated'  => (bool)($target['is_escalated'] ?? false),
            'create_ticket' => (bool)($target['create_ticket'] ?? false),
            'priority'      => $target['priority'] ?? 'medium',
            'category'      => $target['category'] ?? 'general',
            'reason'        => $target['reason'] ?? null,
        ];
    }

    private function fireAndLogSupportWebhook(array $payload, string $traceId): void
    {
        try {
            $payload['runtime_config'] = $this->getRuntimeConfigForN8n();
            \App\Services\N8nService::sendSupportWebhook($payload, 5);
        } catch (Throwable $e) {
            Log::error('support.error', [
                'context'  => 'fireAndLogSupportWebhook',
                'trace_id' => $traceId,
                'message'  => $e->getMessage(),
            ]);
        }
    }

    private function sendMailSafe(Ticket $ticket, string $traceId): void
    {
        try {
            Mail::to(env('SUPPORT_EMAIL', 'support@company.com'))
                ->send(new SupportAlertMail($ticket));

            Log::info('support.mail.sent', [
                'trace_id'  => $traceId,
                'ticket_id' => $ticket->id,
            ]);
        } catch (Throwable $e) {
            Log::error('support.error', [
                'context'   => 'sendMailSafe',
                'trace_id'  => $traceId,
                'ticket_id' => $ticket->id ?? null,
                'message'   => $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // PRIVATE — solution extraction
    // =========================================================================

    private function buildConversationContext(int $conversationId): string
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get(['sender', 'content']);

        return $messages
            ->map(fn($m) => ($m->sender === 'bot' ? 'BOT' : 'USER') . ': ' . $m->content)
            ->implode("\n");
    }

    private function buildOpenRouterContext(int $conversationId): array
    {
        $dbMessages = Message::where('conversation_id', $conversationId)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        $messages = [];
        foreach ($dbMessages as $msg) {
            $role = $msg->sender === 'bot' ? 'assistant' : 'user';

            if ($msg->image_path && $role === 'user') {
                $imageUrl = $msg->image_path;
                if (!str_starts_with($imageUrl, 'http')) {
                    $imageUrl = url($imageUrl);
                }

                $messages[] = [
                    'role' => $role,
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $msg->content ?? 'Screenshot envoye',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl,
                            ],
                        ],
                    ],
                ];
            } else {
                $messages[] = [
                    'role' => $role,
                    'content' => $msg->content ?? '',
                ];
            }
        }

        return $messages;
    }

    private function findDatabaseSolution(string $detectMessage): ?array
    {
        $detectMessage = strtolower(trim($detectMessage));

        if ($detectMessage === '') {
            return null;
        }

        if (str_word_count($detectMessage) < 3) {
            return null;
        }

        $solutions = Cache::remember('support_solutions_all', 60, function () {
            return Solution::query()->get(['question', 'solution']);
        });

        $best      = null;
        $bestScore = 0;

        foreach ($solutions as $solution) {
            $question = strtolower(trim($solution->question));
            similar_text($question, $detectMessage, $percent);

            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best      = $solution;
            }
        }

        Log::info('support.db.match', [
            'message' => $detectMessage,
            'score'   => $bestScore,
            'match'   => $best?->question,
        ]);

        if ($best && $bestScore >= 90) {
            return [
                'solution'   => $best->solution,
                'confidence' => (int) round($bestScore),
            ];
        }

        return null;
    }

    private function extractSolution(string $responseBody): ?string
    {
        $parsed = $this->parseFullN8nResponse($responseBody, 'extract_only');
        return $parsed['message'];
    }

    private function extractMessageFromMixed(mixed $value): ?string
    {
        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? $value : null;
        }

        if (!is_array($value)) {
            return null;
        }

        if (array_is_list($value)) {
            foreach ($value as $item) {
                $message = $this->extractMessageFromMixed($item);
                if ($message) {
                    return $message;
                }
            }
            return null;
        }

        $paths = [
            'response', 'response.message', 'data.message', 'message', 'solution',
            'ai_response', 'response.ai_response', 'data.ai_response',
            'response.solution', 'data.solution', 'output.message',
            'output', 'text', 'answer', 'result.message', 'result',
            'vision_response', 'vision_analysis', 'analysis', 'response.analysis',
            'data.analysis', 'description'
        ];

        foreach ($paths as $path) {
            $candidate = data_get($value, $path);
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        foreach (['response', 'data'] as $key) {
            if (isset($value[$key])) {
                $message = $this->extractMessageFromMixed($value[$key]);
                if ($message) {
                    return $message;
                }
            }
        }

        return null;
    }

    // =========================================================================
    // PRIVATE — detection helpers
    // =========================================================================

    private function normalizeForDetection(string $message): string
    {
        $message = strtolower(trim($message));
        $message = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $message) ?: $message;
        $message = preg_replace('/[^a-z0-9\s]/i', ' ', $message) ?? '';
        $message = preg_replace('/\s+/', ' ', $message) ?? '';

        return trim($message);
    }

    private function normalizePriority(?string $priority): string
    {
        $priority = strtolower((string) $priority);
        return in_array($priority, self::VALID_PRIORITIES, true) ? $priority : 'medium';
    }

    private function detectPriority(string $message, bool $isUrgent, bool $isEscalated): string
    {
        if ($isUrgent || $isEscalated) {
            return 'high';
        }

        foreach (['critical', 'critique', 'production', 'down', 'security', 'hack', 'piratage', 'serveur'] as $word) {
            if (str_contains($message, $word)) {
                return 'high';
            }
        }

        return 'medium';
    }

    private function detectCategory(string $message, bool $hasImage): string
    {
        if ($hasImage) {
            return 'screenshot_analysis';
        }

        $map = [
            'network'        => ['wifi', 'internet', 'reseau', 'network', 'vpn', 'connexion'],
            'authentication' => ['login', 'password', 'mot de passe', 'compte', 'auth'],
            'performance'    => ['lent', 'slow', 'freeze', 'bloque'],
            'hardware'       => ['printer', 'imprimante', 'ecran', 'pc', 'ordinateur', 'disque'],
            'email'          => ['mail', 'email', 'outlook', 'gmail', 'smtp'],
            'security'       => ['security', 'hack', 'piratage', 'virus', 'malware'],
            'audio'          => ['audio', 'son', 'micro', 'speaker', 'haut parleur'],
        ];

        foreach ($map as $category => $words) {
            foreach ($words as $word) {
                if (str_contains($message, $word)) {
                    return $category;
                }
            }
        }

        return 'general';
    }

    private function detectLanguage(string $message): string
    {
        $frWords = [
            'bonjour', 'merci', 'probleme', 'erreur', 'wifi', 'internet',
            'lent', 'aide', 'oui', 'non', 'comment', 'pourquoi',
            'salam', 'bonsoir', 'salut',
        ];

        foreach ($frWords as $word) {
            if (str_contains($message, $word)) {
                return 'fr';
            }
        }

        return 'en';
    }

    private function isSmallTalk(string $message): bool
    {
        $trimmed   = trim($message);
        $smallTalk = ['bonjour', 'salut', 'hello', 'hi', 'bonsoir', 'merci', 'ok', 'cc', 'salam', 'bye', 'thanks'];

        foreach ($smallTalk as $word) {
            if ($trimmed === $word || str_starts_with($trimmed, $word . ' ')) {
                return true;
            }
        }

        return false;
    }

    private function isProblemDetected(string $message, bool $hasImage): bool
    {
        if ($hasImage) {
            return true;
        }

        $keywords = [
            'wifi', 'internet', 'reseau', 'printer', 'imprimante', 'erreur',
            'bug', 'login', 'password', 'mot de passe', 'lent', 'freeze',
            'server', 'serveur', 'pc', 'ordinateur', 'disque', 'blue screen',
            'windows', 'crash', 'email', 'vpn', 'connexion', 'offline',
            'audio', 'son', 'micro', 'outlook', 'office',
        ];

        foreach ($keywords as $word) {
            if (str_contains($message, $word)) {
                return true;
            }
        }

        return false;
    }

    private function isFrustrated(string $message): bool
    {
        $keywords = [
            'marche pas', 'ca marche pas', 'encore', 'toujours', 'erreur',
            'bug', 'non resolu', 'pas resolu', 'impossible', 'comprends pas',
            'toujours probleme', 'encore erreur',
        ];

        foreach ($keywords as $word) {
            if (str_contains($message, $word)) {
                return true;
            }
        }

        return false;
    }

    private function redactPayloadForLog(array $payload): array
    {
        $copy = $payload;

        if (isset($copy['image_base64']) && is_string($copy['image_base64'])) {
            $copy['image_base64'] = '[base64:' . strlen($copy['image_base64']) . ' bytes]';
        }

        return $copy;
    }

    // =========================================================================
    // PRIVATE — response helpers
    // =========================================================================

    private function apiSuccess(array $payload)
    {
        $solution    = (string) ($payload['solution'] ?? $payload['message'] ?? '');
        $userMessage = $payload['user_message'] ?? null;

        return response()->json([
            'success'         => true,
            'conversation_id' => $payload['conversation_id'] ?? null,
            'message'         => $payload['message'] ?? $solution,
            'is_urgent'       => (bool) ($payload['is_urgent']     ?? false),
            'is_escalated'    => (bool) ($payload['is_escalated']  ?? false),
            'create_ticket'   => (bool) ($payload['create_ticket'] ?? false),
            'priority'        => $payload['priority']        ?? 'medium',
            'category'        => $payload['category']        ?? 'general',
            'solution'        => $solution,
            'user_message'    => $userMessage,
            'ticket_id'       => $payload['ticket_id']       ?? null,
            'jira_ticket_id'  => $payload['jira_ticket_id']  ?? null,
            'show_ticket'     => (bool) ($payload['show_ticket'] ?? false),
            'source'          => $payload['source']          ?? 'system',
            'confidence'      => $payload['confidence']      ?? 0,
            'image_url'       => $payload['image_url']       ?? null,
            'language'        => $payload['language']        ?? 'en',
            'status'          => $payload['status']          ?? 'pending',
            'trace_id'        => $payload['trace_id']        ?? null,
            'elapsed_ms'      => $payload['elapsed_ms']      ?? null,
            'response'        => [
                'message'         => $solution,
                'user_message'    => $userMessage,
                'is_urgent'       => (bool) ($payload['is_urgent']     ?? false),
                'is_escalated'    => (bool) ($payload['is_escalated']  ?? false),
                'create_ticket'   => (bool) ($payload['create_ticket'] ?? false),
                'priority'        => $payload['priority']        ?? 'medium',
                'category'        => $payload['category']        ?? 'general',
                'ticket_id'       => $payload['ticket_id']       ?? null,
                'jira_ticket_id'  => $payload['jira_ticket_id']  ?? null,
                'conversation_id' => $payload['conversation_id'] ?? null,
            ],
            'data' => [
                'message'         => $solution,
                'user_message'    => $userMessage,
                'is_urgent'       => (bool) ($payload['is_urgent']     ?? false),
                'is_escalated'    => (bool) ($payload['is_escalated']  ?? false),
                'create_ticket'   => (bool) ($payload['create_ticket'] ?? false),
                'priority'        => $payload['priority']        ?? 'medium',
                'category'        => $payload['category']        ?? 'general',
                'ticket_id'       => $payload['ticket_id']       ?? null,
                'jira_ticket_id'  => $payload['jira_ticket_id']  ?? null,
                'conversation_id' => $payload['conversation_id'] ?? null,
            ]
        ]);
    }

    private function apiError(string $message, int $status = 500, array $extra = [])
    {
        return response()->json(array_merge([
            'success'  => false,
            'message'  => $message,
            'solution' => $message,
            'response' => [
                'message'       => $message,
                'is_urgent'     => false,
                'is_escalated'  => false,
                'create_ticket' => false,
                'priority'      => 'medium',
                'category'      => 'general',
            ],
            'status' => 'error',
        ], $extra), $status);
    }

    // =========================================================================
    // PRIVATE — image upload
    // =========================================================================

    private function handleImageUpload(?UploadedFile $file, string $traceId): array
    {
        if (!$file) {
            return [
                'has_image'    => false,
                'image_url'    => '',
                'image_base64' => '',
                'mime_type'    => '',
            ];
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $mime         = $file->getMimeType();

        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Type image non autorise.');
        }

        $extension   = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $safeName    = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $extension;
        $imagePath   = $file->storeAs('support-images', $safeName, 'public');
        
        $urlPath     = Storage::disk('public')->url($imagePath);
        if (!str_starts_with($urlPath, 'http')) {
            $imageUrl = url($urlPath);
        } else {
            $imageUrl = $urlPath;
        }
        $imageUrl    = str_replace(['127.0.0.1', 'localhost'], 'host.docker.internal', $imageUrl);
        
        $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));

        Log::info('support.image.uploaded', [
            'trace_id'  => $traceId,
            'path'      => $imagePath,
            'url'       => $imageUrl,
            'mime_type' => $mime,
            'size_kb'   => round($file->getSize() / 1024, 1),
            'base64_kb' => round(strlen($imageBase64) / 1024, 1),
        ]);

        return [
            'has_image'    => true,
            'image_url'    => $imageUrl,
            'image_base64' => $imageBase64,
            'mime_type'    => $mime,
        ];
    }
}

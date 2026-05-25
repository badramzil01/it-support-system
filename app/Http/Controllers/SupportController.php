<?php

namespace App\Http\Controllers;

use App\Mail\SupportAlertMail;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Solution;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SupportController extends Controller
{
    private const VALID_STATUSES = ['pending', 'resolved', 'waiting_support', 'closed', 'error'];
    private const VALID_PRIORITIES = ['low', 'medium', 'high', 'critical'];

    private string $n8nWebhookSupport = 'http://localhost:5678/webhook-test/support';
    private string $n8nWebhookJira = 'http://localhost:5678/webhook/jira-ticket';

    public function handle(Request $request)
    {
        $startedAt = microtime(true);
        $traceId = (string) Str::uuid();

        try {
            $validated = $request->validate([
                'message' => 'nullable|string|max:3000',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'is_urgent' => 'nullable|boolean',
                'is_escalated' => 'nullable|boolean',
                'create_ticket' => 'nullable|boolean',
                'priority' => 'nullable|string|in:low,medium,high,critical',
                'source' => 'nullable|string|max:40',
                'session_id' => 'nullable|string|max:120',
                'conversation_id' => 'nullable',
            ]);

            if (!$request->filled('message') && !$request->hasFile('image')) {
                return $this->apiError('Message ou image requis.', 422, [
                    'trace_id' => $traceId,
                    'status' => 'error',
                ]);
            }

            $userId = auth()->id() ?? 1;
            $originalMessage = trim((string) $request->input('message', ''));
            $detectMessage = $this->normalizeForDetection($originalMessage);
            $language = $this->detectLanguage($detectMessage);
            $image = $this->handleImageUpload($request->file('image'), $traceId);
            $hasImage = $image['has_image'];

            $conversation = $this->resolveConversation($request, $userId, $detectMessage, $hasImage);

            $userMessage = Message::create([
                'user_id' => $userId,
                'conversation_id' => $conversation->id,
                'sender' => 'user',
                'content' => $originalMessage !== '' ? $originalMessage : 'Screenshot envoye',
                'response' => null,
                'channel' => 'web',
                'source' => 'user',
                'status' => 'sent',
                'image_path' => $image['image_url'],
            ]);

            $isUrgent = $request->boolean('is_urgent');
            $isEscalated = $request->boolean('is_escalated');
            $createTicket = $request->boolean('create_ticket');

            $priority = $this->normalizePriority(
                $request->input('priority') ?: $this->detectPriority($detectMessage, $isUrgent, $isEscalated)
            );
            $category = $this->detectCategory($detectMessage, $hasImage);
            $isSmallTalk = $this->isSmallTalk($detectMessage);
            $problemDetected = $this->isProblemDetected($detectMessage, $hasImage);
            $isFrustrated = $this->isFrustrated($detectMessage);

            $source = 'ai';
            $status = 'pending';
            $solution = null;
            $confidence = 0;
            $showTicket = false;

            Log::info('support.handle.start', [
                'trace_id' => $traceId,
                'user_id' => $userId,
                'conversation_id' => $conversation->id,
                'has_image' => $hasImage,
                'is_urgent' => $isUrgent,
                'is_escalated' => $isEscalated,
                'create_ticket' => $createTicket,
                'priority' => $priority,
                'category' => $category,
                'problem_detected' => $problemDetected,
                'is_small_talk' => $isSmallTalk,
            ]);

            if ($isUrgent) {
                $priority = 'high';
                $showTicket = true;
            }

            if ($isEscalated) {
                $priority = 'high';
                $source = 'support';
                $status = 'waiting_support';
                $showTicket = true;
            }

            if ($isFrustrated) {
                $solution = 'Votre demande a ete transferee a notre equipe support.';
                $source = 'support';
                $status = 'waiting_support';
                $priority = 'high';
                $confidence = 100;
                $showTicket = true;
            } elseif ($hasImage) {
                $aiResult = $this->callSupportWorkflow([
                    'user_id' => $userId,
                    'conversation_id' => $conversation->id,
                    'message' => $originalMessage !== '' ? $originalMessage : 'Screenshot envoye',
                    'message_clean' => $detectMessage,
                    'context' => $this->buildConversationContext($conversation->id),
                    'source' => 'vision_ai',
                    'priority' => $priority,
                    'category' => $category,
                    'status' => 'pending',
                    'session_id' => $request->input('session_id'),
                    'is_urgent' => $isUrgent,
                    'is_escalated' => $isEscalated,
                    'create_ticket' => $createTicket,
                    'has_image' => true,
                    'image_url' => $image['image_url'],
                    'image_base64' => $image['image_base64'],
                    'mime_type' => $image['mime_type'],
                    'language' => $language,
                ], $traceId, 180);

                $solution = $aiResult['message'];
                $source = $aiResult['ok'] ? 'vision_ai' : 'system';
                $status = $aiResult['ok'] ? 'resolved' : 'error';
                $confidence = $aiResult['ok'] ? 92 : 0;
                $showTicket = true;
            } else {
                $dbSolution = $this->findDatabaseSolution($detectMessage);

                if ($dbSolution) {
                    $solution = $dbSolution['solution'];
                    $source = 'db';
                    $status = 'resolved';
                    $confidence = $dbSolution['confidence'];
                    $showTicket = true;

                    $this->fireAndLogSupportWebhook([
                        'user_id' => $userId,
                        'conversation_id' => $conversation->id,
                        'message' => $originalMessage,
                        'message_clean' => $detectMessage,
                        'response' => [
                            'message' => $solution,
                            'is_urgent' => $isUrgent,
                            'is_escalated' => $isEscalated,
                            'create_ticket' => $createTicket,
                            'priority' => $priority,
                            'category' => $category,
                        ],
                        'source' => 'db',
                        'priority' => $priority,
                        'category' => $category,
                        'status' => $status,
                        'confidence' => $confidence,
                        'is_urgent' => $isUrgent,
                        'is_escalated' => $isEscalated,
                        'create_ticket' => $createTicket,
                        'has_image' => false,
                    ], $traceId);
                } else {
                    $aiResult = $this->callSupportWorkflow([
                        'user_id' => $userId,
                        'conversation_id' => $conversation->id,
                        'message' => $originalMessage,
                        'message_clean' => $detectMessage,
                        'context' => $this->buildConversationContext($conversation->id),
                        'source' => 'ai',
                        'priority' => $priority,
                        'category' => $category,
                        'status' => 'pending',
                        'session_id' => $request->input('session_id'),
                        'is_urgent' => $isUrgent,
                        'is_escalated' => $isEscalated,
                        'create_ticket' => $createTicket,
                        'has_image' => false,
                        'image_url' => null,
                        'image_base64' => null,
                        'mime_type' => null,
                        'language' => $language,
                    ], $traceId, 75);

                    $solution = $aiResult['message'];
                    $source = $aiResult['ok'] ? 'ai' : 'system';
                    $status = $aiResult['ok'] ? 'resolved' : 'error';
                    $confidence = $aiResult['ok'] ? 85 : 0;
                    $showTicket = $aiResult['ok'];

                    if ($aiResult['ok'] && $detectMessage !== '') {
                        Solution::updateOrCreate(
                            ['question' => $detectMessage],
                            ['solution' => $solution]
                        );
                    }
                }
            }

            if (!$solution || trim($solution) === '') {
                $solution = 'Je suis desole, je n ai pas recu de reponse IA exploitable. Merci de reessayer.';
                $source = 'system';
                $status = 'error';
                $confidence = 0;
            }

            if (!in_array($status, self::VALID_STATUSES, true)) {
                $status = 'error';
            }

            if ($conversation->current_category && $conversation->current_category !== $category && $problemDetected) {
                $conversation->update([
                    'ticket_created' => false,
                    'current_ticket_id' => null,
                    'current_problem' => null,
                    'current_category' => null,
                ]);
            }

            Message::create([
                'user_id' => $userId,
                'conversation_id' => $conversation->id,
                'sender' => 'bot',
                'content' => $solution,
                'response' => $solution,
                'channel' => 'web',
                'source' => $source,
                'status' => $status,
                'image_path' => null,
            ]);

            $ticket = null;
            $ticketId = null;
            $shouldCreateTicket = $this->shouldCreateTicket(
                $conversation,
                $isSmallTalk,
                $problemDetected,
                $isUrgent,
                $isEscalated,
                $createTicket
            );

            if ($shouldCreateTicket) {
                $ticket = $this->createSupportTicket([
                    'message_id' => $userMessage->id,
                    'solution' => $solution,
                    'source' => $source,
                    'priority' => $priority,
                    'category' => $category,
                    'confidence' => $confidence,
                    'status' => $status === 'error' ? 'pending' : $status,
                    'is_urgent' => $isUrgent,
                    'is_escalated' => $isEscalated,
                    'has_image' => $hasImage,
                    'description' => $originalMessage,
                ], $traceId);

                $ticketId = $ticket->jira_ticket_id;

                $conversation->update([
                    'ticket_created' => true,
                    'current_ticket_id' => $ticketId,
                    'current_problem' => $detectMessage ?: $originalMessage,
                    'current_category' => $category,
                ]);
            }

            $this->sendJiraWebhookSafe([
                'ticket_id' => $ticketId,
                'message' => $originalMessage,
                'message_clean' => $detectMessage,
                'priority' => $priority,
                'category' => $category,
                'status' => $status,
                'source' => $source,
                'confidence' => $confidence,
                'solution' => $solution,
                'is_urgent' => $isUrgent,
                'is_escalated' => $isEscalated,
                'create_ticket' => $createTicket,
                'has_image' => $hasImage,
                'conversation_id' => $conversation->id,
                'user_id' => $userId,
                'language' => $language,
            ], $traceId);

            if ($ticket && ($priority === 'high' || $priority === 'critical' || $status === 'waiting_support')) {
                $this->sendMailSafe($ticket, $traceId);
            }

            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);
            Log::info('support.handle.done', [
                'trace_id' => $traceId,
                'conversation_id' => $conversation->id,
                'status' => $status,
                'source' => $source,
                'ticket_id' => $ticketId,
                'elapsed_ms' => $elapsedMs,
            ]);

            return $this->apiSuccess([
                'message' => 'Reponse IA recue.',
                'solution' => $solution,
                'ticket_id' => $ticketId ?? $conversation->current_ticket_id,
                'jira_ticket_id' => $ticketId ?? $conversation->current_ticket_id,
                'show_ticket' => $showTicket || $shouldCreateTicket,
                'source' => $source,
                'priority' => $priority,
                'category' => $category,
                'confidence' => $confidence,
                'conversation_id' => $conversation->id,
                'image_url' => $image['image_url'],
                'is_urgent' => $isUrgent,
                'is_escalated' => $isEscalated,
                'create_ticket' => $createTicket || $shouldCreateTicket,
                'language' => $language,
                'status' => $status,
                'trace_id' => $traceId,
                'elapsed_ms' => $elapsedMs,
            ]);
        } catch (Throwable $e) {
            Log::error('support.handle.exception', [
                'trace_id' => $traceId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->apiError('Erreur connexion IA.', 500, [
                'trace_id' => $traceId,
                'status' => 'error',
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
                    'id',
                    'user_id',
                    'conversation_id',
                    'sender',
                    'content',
                    'response',
                    'image_path',
                    'source',
                    'status',
                    'created_at',
                ]);

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);
        } catch (Throwable $e) {
            Log::error('support.history.error', ['message' => $e->getMessage()]);
            return $this->apiError('Erreur chargement historique.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function feedback(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|string|max:80',
                'feedback' => 'required|string|in:resolved,unresolved',
            ]);

            $ticket = Ticket::where('jira_ticket_id', $request->ticket_id)->first();

            if (!$ticket) {
                return $this->apiError('Ticket introuvable.', 404);
            }

            if ($request->feedback === 'resolved') {
                $ticket->update(['feedback' => 'resolved', 'status' => 'closed']);

                return response()->json([
                    'success' => true,
                    'message' => 'Merci pour votre retour.',
                    'status' => 'closed',
                    'ticket_id' => $ticket->jira_ticket_id,
                ]);
            }

            $ticket->update(['feedback' => 'unresolved', 'status' => 'waiting_support']);
            $this->sendMailSafe($ticket, (string) Str::uuid());

            return response()->json([
                'success' => true,
                'message' => 'Votre ticket a ete transfere au support.',
                'status' => 'waiting_support',
                'ticket_id' => $ticket->jira_ticket_id,
            ]);
        } catch (Throwable $e) {
            Log::error('support.feedback.error', ['message' => $e->getMessage()]);
            return $this->apiError('Erreur feedback.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function ticketStatus($ticketId)
    {
        try {
            $ticket = Ticket::where('jira_ticket_id', $ticketId)->first();

            if (!$ticket) {
                return $this->apiError('Ticket introuvable.', 404);
            }

            return response()->json([
                'success' => true,
                'ticket_id' => $ticket->jira_ticket_id,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'source' => $ticket->source,
                'solution' => $ticket->solution,
                'confidence' => $ticket->confidence,
                'feedback' => $ticket->feedback,
                'created_at' => $ticket->created_at,
            ]);
        } catch (Throwable $e) {
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
                'user_id' => 'required|integer',
                'solution' => 'nullable|string',
                'message' => 'nullable|string',
                'ai_response' => 'nullable|string',
                'source' => 'nullable|string|max:40',
                'confidence' => 'nullable|integer|min:0|max:100',
                'ticket_id' => 'nullable|string|max:80',
            ]);

            $aiMessage = trim((string) (
                $request->input('solution')
                ?: $request->input('message')
                ?: $request->input('ai_response')
            ));

            if ($aiMessage === '') {
                return $this->apiError('AI message required.', 422);
            }

            Message::create([
                'user_id' => $request->user_id,
                'conversation_id' => $request->conversation_id,
                'sender' => 'bot',
                'content' => $aiMessage,
                'response' => $aiMessage,
                'channel' => 'n8n',
                'source' => $request->input('source', 'ai'),
                'status' => 'resolved',
                'image_path' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AI response saved.',
            ]);
        } catch (Throwable $e) {
            Log::error('support.receive_ai_response.error', ['message' => $e->getMessage()]);
            return $this->apiError('Erreur sauvegarde reponse IA.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function createTicket(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:3000',
                'priority' => 'nullable|string|in:low,medium,high,critical',
                'category' => 'nullable|string|max:80',
                'user_id' => 'nullable|integer',
            ]);

            $traceId = (string) Str::uuid();
            $priority = $this->normalizePriority($request->input('priority', 'medium'));
            $ticket = $this->createSupportTicket([
                'message_id' => null,
                'solution' => null,
                'source' => 'manual',
                'priority' => $priority,
                'category' => $request->input('category', 'general'),
                'confidence' => 0,
                'status' => 'pending',
                'is_urgent' => false,
                'is_escalated' => false,
                'has_image' => false,
                'description' => $request->message,
            ], $traceId);

            $this->sendJiraWebhookSafe([
                'ticket_id' => $ticket->jira_ticket_id,
                'message' => $request->message,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'source' => $ticket->source,
                'user_id' => $request->input('user_id', auth()->id() ?? 1),
            ], $traceId);

            return response()->json([
                'success' => true,
                'message' => 'Ticket cree.',
                'ticket_id' => $ticket->jira_ticket_id,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
            ]);
        } catch (Throwable $e) {
            Log::error('support.create_ticket.error', ['message' => $e->getMessage()]);
            return $this->apiError('Erreur creation ticket.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
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
            'title' => Str::limit($detectMessage ?: ($hasImage ? 'Screenshot Support' : 'Nouvelle conversation'), 50, ''),
        ]);
    }

    private function handleImageUpload(?UploadedFile $file, string $traceId): array
    {
        if (!$file) {
            return [
                'has_image' => false,
                'image_url' => null,
                'image_base64' => null,
                'mime_type' => null,
            ];
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = $file->getMimeType();

        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Type image non autorise.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $safeName = now()->format('Ymd_His') . '_' . Str::uuid() . '.' . $extension;
        $imagePath = $file->storeAs('support-images', $safeName, 'public');
        $imageUrl = Storage::disk('public')->url($imagePath);
        $imageUrl = str_replace(['127.0.0.1', 'localhost'], 'host.docker.internal', url($imageUrl));
        $imageBase64 = base64_encode(file_get_contents($file->getRealPath()));

        Log::info('support.image.uploaded', [
            'trace_id' => $traceId,
            'path' => $imagePath,
            'url' => $imageUrl,
            'mime_type' => $mime,
            'size_kb' => round($file->getSize() / 1024, 1),
            'base64_kb' => round(strlen($imageBase64) / 1024, 1),
        ]);

        return [
            'has_image' => true,
            'image_url' => $imageUrl,
            'image_base64' => $imageBase64,
            'mime_type' => $mime,
        ];
    }

    private function callSupportWorkflow(array $payload, string $traceId, int $timeoutSeconds): array
    {
        $startedAt = microtime(true);

        Log::info('support.n8n.request', [
            'trace_id' => $traceId,
            'url' => $this->n8nWebhookSupport,
            'payload' => $this->redactPayloadForLog($payload),
        ]);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->retry(3, 1000)
                ->timeout($timeoutSeconds)
                ->post($this->n8nWebhookSupport, $payload);

            $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);

            Log::info('support.n8n.response', [
                'trace_id' => $traceId,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'elapsed_ms' => $elapsedMs,
                'body' => Str::limit($response->body(), 8000),
            ]);

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'Service IA indisponible actuellement. Merci de reessayer.',
                    'status' => $response->status(),
                ];
            }

            $message = $this->extractSolution($response->body());

            if (!$message) {
                return [
                    'ok' => false,
                    'message' => 'Je suis desole, la reponse IA etait vide ou invalide. Merci de reessayer.',
                    'status' => $response->status(),
                ];
            }

            return [
                'ok' => true,
                'message' => $message,
                'status' => $response->status(),
            ];
        } catch (Throwable $e) {
            Log::error('support.n8n.exception', [
                'trace_id' => $traceId,
                'message' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'Erreur connexion IA. Merci de reessayer dans quelques instants.',
                'status' => 0,
            ];
        }
    }

    private function fireAndLogSupportWebhook(array $payload, string $traceId): void
    {
        try {
            Http::acceptJson()
                ->asJson()
                ->retry(2, 500)
                ->timeout(30)
                ->post($this->n8nWebhookSupport, $payload);
        } catch (Throwable $e) {
            Log::warning('support.n8n.fire_and_forget_failed', [
                'trace_id' => $traceId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function sendJiraWebhookSafe(array $payload, string $traceId): void
    {
        if (empty($payload['ticket_id'])) {
            return;
        }

        $startedAt = microtime(true);

        try {
            Log::info('support.jira.webhook.request', [
                'trace_id' => $traceId,
                'url' => $this->n8nWebhookJira,
                'payload' => $payload,
            ]);

            $response = Http::acceptJson()
                ->asJson()
                ->retry(2, 500)
                ->timeout(30)
                ->post($this->n8nWebhookJira, $payload);

            Log::info('support.jira.webhook.response', [
                'trace_id' => $traceId,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'elapsed_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'body' => Str::limit($response->body(), 4000),
            ]);
        } catch (Throwable $e) {
            Log::error('support.jira.webhook.error', [
                'trace_id' => $traceId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function sendMailSafe(Ticket $ticket, string $traceId): void
    {
        try {
            Mail::to(env('SUPPORT_EMAIL', 'support@company.com'))
                ->send(new SupportAlertMail($ticket));

            Log::info('support.mail.sent', [
                'trace_id' => $traceId,
                'ticket_id' => $ticket->jira_ticket_id,
            ]);
        } catch (Throwable $e) {
            Log::error('support.mail.error', [
                'trace_id' => $traceId,
                'ticket_id' => $ticket->jira_ticket_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function buildConversationContext(int $conversationId): string
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get(['sender', 'content']);

        return $messages
            ->map(function ($message) {
                $role = $message->sender === 'bot' ? 'BOT' : 'USER';
                return $role . ': ' . $message->content;
            })
            ->implode("\n");
    }

    private function findDatabaseSolution(string $detectMessage): ?array
    {
        if ($detectMessage === '') {
            return null;
        }

        $solutions = Cache::remember('support_solutions_all', 60, function () {
            return Solution::query()->get(['question', 'solution']);
        });

        $best = null;
        $bestScore = 0;

        foreach ($solutions as $solution) {
            similar_text(strtolower($solution->question), $detectMessage, $percent);

            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $solution;
            }
        }

        if ($best && $bestScore >= 70) {
            return [
                'solution' => $best->solution,
                'confidence' => (int) round($bestScore),
            ];
        }

        return null;
    }

    private function createSupportTicket(array $data, string $traceId): Ticket
    {
        $ticketId = 'KAN-' . random_int(10000, 99999);

        $ticket = Ticket::create([
            'message_id' => $data['message_id'],
            'jira_ticket_id' => $ticketId,
            'title' => Str::limit($data['description'] ?: 'Support ticket', 120, ''),
            'description' => $data['description'],
            'solution' => $data['solution'],
            'source' => $data['source'],
            'priority' => $this->normalizePriority($data['priority']),
            'category' => $data['category'] ?: 'general',
            'confidence' => (int) ($data['confidence'] ?? 0),
            'status' => in_array($data['status'], self::VALID_STATUSES, true) ? $data['status'] : 'pending',
            'feedback' => 'pending',
            'is_urgent' => (bool) $data['is_urgent'],
            'is_escalated' => (bool) $data['is_escalated'],
            'has_image' => (bool) $data['has_image'],
        ]);

        Log::info('support.ticket.created', [
            'trace_id' => $traceId,
            'ticket_id' => $ticketId,
            'priority' => $ticket->priority,
            'category' => $ticket->category,
            'status' => $ticket->status,
        ]);

        return $ticket;
    }

    private function shouldCreateTicket(
        Conversation $conversation,
        bool $isSmallTalk,
        bool $problemDetected,
        bool $isUrgent,
        bool $isEscalated,
        bool $createTicket
    ): bool {
        if ($isUrgent || $isEscalated || $createTicket) {
            return true;
        }

        if ($isSmallTalk) {
            return false;
        }

        return $problemDetected && !$conversation->ticket_created;
    }

    private function extractSolution(string $responseBody): ?string
    {
        $body = trim($responseBody);

        if ($body === '') {
            Log::warning('support.extract.empty_response');
            return null;
        }

        if (Str::contains(strtolower($body), ['<!doctype', '<html'])) {
            Log::warning('support.extract.html_response', ['body' => Str::limit($body, 1000)]);
            return null;
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('support.extract.invalid_json', [
                'json_error' => json_last_error_msg(),
                'body' => Str::limit($body, 2000),
            ]);
            return null;
        }

        Log::info('support.extract.parsed', [
            'shape' => is_array($data) ? array_keys($data) : gettype($data),
        ]);

        return $this->extractMessageFromMixed($data);
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
            'response.message',
            'data.message',
            'message',
            'solution',
            'ai_response',
            'response.ai_response',
            'data.ai_response',
            'response.solution',
            'data.solution',
            'output.message',
            'output',
            'text',
            'answer',
            'result.message',
            'result',
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

    private function apiSuccess(array $payload)
    {
        $solution = (string) ($payload['solution'] ?? $payload['message'] ?? '');

        return response()->json([
            'success' => true,
            'message' => $payload['message'] ?? 'OK',
            'solution' => $solution,
            'response' => [
                'message' => $solution,
                'is_urgent' => (bool) ($payload['is_urgent'] ?? false),
                'is_escalated' => (bool) ($payload['is_escalated'] ?? false),
                'create_ticket' => (bool) ($payload['create_ticket'] ?? false),
                'priority' => $payload['priority'] ?? 'medium',
                'category' => $payload['category'] ?? 'general',
                'ticket_id' => $payload['ticket_id'] ?? null,
                'jira_ticket_id' => $payload['jira_ticket_id'] ?? null,
                'conversation_id' => $payload['conversation_id'] ?? null,
            ],
            'data' => [
                'message' => $solution,
                'is_urgent' => (bool) ($payload['is_urgent'] ?? false),
                'is_escalated' => (bool) ($payload['is_escalated'] ?? false),
                'create_ticket' => (bool) ($payload['create_ticket'] ?? false),
                'priority' => $payload['priority'] ?? 'medium',
                'category' => $payload['category'] ?? 'general',
                'ticket_id' => $payload['ticket_id'] ?? null,
                'jira_ticket_id' => $payload['jira_ticket_id'] ?? null,
                'conversation_id' => $payload['conversation_id'] ?? null,
            ],
            'ticket_id' => $payload['ticket_id'] ?? null,
            'jira_ticket_id' => $payload['jira_ticket_id'] ?? null,
            'show_ticket' => (bool) ($payload['show_ticket'] ?? false),
            'source' => $payload['source'] ?? 'system',
            'priority' => $payload['priority'] ?? 'medium',
            'category' => $payload['category'] ?? 'general',
            'confidence' => $payload['confidence'] ?? 0,
            'conversation_id' => $payload['conversation_id'] ?? null,
            'image_url' => $payload['image_url'] ?? null,
            'is_urgent' => (bool) ($payload['is_urgent'] ?? false),
            'is_escalated' => (bool) ($payload['is_escalated'] ?? false),
            'language' => $payload['language'] ?? 'en',
            'status' => $payload['status'] ?? 'pending',
            'trace_id' => $payload['trace_id'] ?? null,
            'elapsed_ms' => $payload['elapsed_ms'] ?? null,
        ]);
    }

    private function apiError(string $message, int $status = 500, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
            'solution' => $message,
            'response' => [
                'message' => $message,
                'is_urgent' => false,
                'is_escalated' => false,
                'create_ticket' => false,
                'priority' => 'medium',
                'category' => 'general',
            ],
            'status' => 'error',
        ], $extra), $status);
    }

    private function normalizeForDetection(string $message): string
    {
        $message = strtolower(trim($message));
        $message = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $message) ?: $message;
        $message = preg_replace('/[^a-z0-9\s]/i', ' ', $message);
        $message = preg_replace('/\s+/', ' ', $message);

        return trim($message ?? '');
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
            'network' => ['wifi', 'internet', 'reseau', 'network', 'vpn', 'connexion'],
            'authentication' => ['login', 'password', 'mot de passe', 'compte', 'auth'],
            'performance' => ['lent', 'slow', 'freeze', 'bloque'],
            'hardware' => ['printer', 'imprimante', 'ecran', 'pc', 'ordinateur', 'disque'],
            'email' => ['mail', 'email', 'outlook', 'gmail', 'smtp'],
            'security' => ['security', 'hack', 'piratage', 'virus', 'malware'],
            'audio' => ['audio', 'son', 'micro', 'speaker', 'haut parleur'],
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
        foreach (['bonjour', 'merci', 'probleme', 'erreur', 'wifi', 'internet', 'lent', 'aide', 'oui', 'non', 'comment', 'pourquoi', 'salam', 'bonsoir', 'salut'] as $word) {
            if (str_contains($message, $word)) {
                return 'fr';
            }
        }

        return 'en';
    }

    private function isSmallTalk(string $message): bool
    {
        $trimmed = trim($message);
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

        foreach ([
            'wifi', 'internet', 'reseau', 'printer', 'imprimante', 'erreur',
            'bug', 'login', 'password', 'mot de passe', 'lent', 'freeze',
            'server', 'serveur', 'pc', 'ordinateur', 'disque', 'blue screen',
            'windows', 'crash', 'email', 'vpn', 'connexion', 'offline',
            'audio', 'son', 'micro', 'outlook', 'office',
        ] as $word) {
            if (str_contains($message, $word)) {
                return true;
            }
        }

        return false;
    }

    private function isFrustrated(string $message): bool
    {
        foreach ([
            'marche pas', 'ca marche pas', 'encore', 'toujours', 'erreur',
            'bug', 'non resolu', 'pas resolu', 'impossible', 'comprends pas',
            'toujours probleme', 'encore erreur',
        ] as $word) {
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
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\JiraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JiraController extends Controller
{
    /**
     * POST /api/jira/create (also accepts GET/OPTIONS for compatibility).
     *
     * Creates a Jira issue and links it to an existing Laravel ticket.
     * If a Laravel ticket already exists without a jira_ticket_id,
     * creates the Jira issue, saves the key, and returns it.
     *
     * Payload attendu par n8n :
     *   conversation_id  (int|required)
     *   user_id          (int|required)
     *   summary          (string|required)
     *   description      (string|required)
     *   priority         (string|in:low,medium,high,critical)
     *   category         (string|nullable)
     *   jira_key         (string|nullable) — if provided, links to existing ticket
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // ── 1. Validation ──────────────────────────────────────────
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required|integer',
                'user_id'         => 'required|integer',
                'summary'         => 'required|string|max:255',
                'description'     => 'required|string|max:10000',
                'priority'        => 'nullable|string|in:low,medium,high,critical',
                'category'        => 'nullable|string|max:255',
                'jira_key'        => 'nullable|string|max:80',
            ], [
                'conversation_id.required' => 'Le champ conversation_id est obligatoire.',
                'user_id.required'         => 'Le champ user_id est obligatoire.',
                'summary.required'         => 'Le champ summary est obligatoire.',
                'description.required'     => 'Le champ description est obligatoire.',
                'priority.in'              => 'La priorité doit être low, medium, high ou critical.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data    = $validator->validated();
            $jiraKey = $data['jira_key'] ?? null;

            // ── 2. If jira_key provided, find existing ticket and update it ──
            if ($jiraKey) {
                $existingTicket = Ticket::where('jira_ticket_id', $jiraKey)->first();

                if ($existingTicket) {
                    $existingTicket->update([
                        'title'       => $data['summary'],
                        'description' => $data['description'],
                        'priority'    => $data['priority'] ?? $existingTicket->priority,
                        'category'    => $data['category'] ?? $existingTicket->category,
                    ]);

                    Log::info('jira.create.updated_existing', [
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => $jiraKey,
                    ]);

                    return response()->json([
                        'success'   => true,
                        'message'   => 'Existing ticket updated with Jira data.',
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => $jiraKey,
                    ], 200);
                }
            }

            // ── 3. Find existing Laravel ticket by conversation_id + title ──
            $existingTicket = Ticket::where('conversation_id', $data['conversation_id'])
                ->where('title', $data['summary'])
                ->first();

            if ($existingTicket) {
                // ♻️ If a Jira key was explicitly provided, just link it
                if ($jiraKey && empty($existingTicket->jira_ticket_id)) {
                    $existingTicket->update(['jira_ticket_id' => $jiraKey]);

                    Log::info('jira.create.linked_existing', [
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => $jiraKey,
                    ]);

                    return response()->json([
                        'success'   => true,
                        'message'   => 'Jira key linked to existing ticket.',
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => $jiraKey,
                    ], 200);
                }

                // ♻️ If the existing ticket ALREADY has a Jira key → return it
                if (!empty($existingTicket->jira_ticket_id)) {
                    Log::info('jira.create.duplicate_skipped', [
                        'existing_ticket_id' => $existingTicket->id,
                        'jira_key'           => $existingTicket->jira_ticket_id,
                        'summary'            => $data['summary'],
                    ]);

                    return response()->json([
                        'success'   => true,
                        'message'   => 'Ticket already exists with Jira key.',
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => $existingTicket->jira_ticket_id,
                    ], 200);
                }

                // ⚠️ The existing ticket has NO Jira key yet → create Jira issue NOW
                Log::info('jira.create.existing_without_key', [
                    'ticket_id'             => $existingTicket->id,
                    'summary'               => $data['summary'],
                    'creating_jira_issue'   => true,
                ]);

                try {
                    $jiraService = app(JiraService::class);
                    $jiraResponse = $jiraService->createTicket(
                        $data['summary'],
                        $data['description'],
                        $data['category'] ?? null,
                        $data['priority'] ?? null
                    );

                    $jiraKey = $jiraResponse['key'] ?? null;

                    if ($jiraKey && preg_match('/^[A-Z]+-\d+$/', $jiraKey)) {
                        $existingTicket->update(['jira_ticket_id' => $jiraKey]);

                        Log::info('jira.create.existing_key_saved', [
                            'ticket_id' => $existingTicket->id,
                            'jira_key'  => $jiraKey,
                        ]);

                        return response()->json([
                            'success'   => true,
                            'message'   => 'Jira issue created and linked to existing ticket.',
                            'ticket_id' => $existingTicket->id,
                            'jira_key'  => $jiraKey,
                        ], 201);
                    }

                    Log::error('jira.create.existing_no_key_returned', [
                        'ticket_id' => $existingTicket->id,
                        'response'  => $jiraResponse,
                    ]);

                    return response()->json([
                        'success'   => false,
                        'message'   => 'Jira issue created but no key returned.',
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => null,
                    ], 500);

                } catch (\Throwable $e) {
                    Log::error('jira.create.existing_jira_error', [
                        'ticket_id' => $existingTicket->id,
                        'error'     => $e->getMessage(),
                    ]);

                    return response()->json([
                        'success'   => false,
                        'message'   => 'Failed to create Jira issue for existing ticket: ' . $e->getMessage(),
                        'ticket_id' => $existingTicket->id,
                        'jira_key'  => null,
                    ], 500);
                }
            }

            // ── 4. Create new Laravel ticket + Jira issue ──────────────
            $ticket = Ticket::create([
                'conversation_id' => $data['conversation_id'],
                'user_id'         => $data['user_id'],
                'title'           => $data['summary'],
                'description'     => $data['description'],
                'priority'        => $data['priority'] ?? 'medium',
                'category'        => $data['category'] ?? null,
                'jira_ticket_id'  => $jiraKey,
                'status'          => 'open',
                'ticket_status'   => 'open',
                'source'          => 'n8n',
                // Routage automatique N1
                'support_level'   => 'N1',
                'assigned_team'   => 'support_n1',
                'escalation_level'=> 0,
                'is_escalated'    => false,
            ]);

            // If no jira_key was provided, create the Jira issue now
            if (empty($jiraKey)) {
                try {
                    $jiraService = app(JiraService::class);
                    $jiraResponse = $jiraService->createTicket(
                        $data['summary'],
                        $data['description'],
                        $data['category'] ?? null,
                        $data['priority'] ?? null
                    );

                    $jiraKey = $jiraResponse['key'] ?? null;

                    if ($jiraKey && preg_match('/^[A-Z]+-\d+$/', $jiraKey)) {
                        $ticket->update(['jira_ticket_id' => $jiraKey]);

                        Log::info('jira.create.new_key_saved', [
                            'ticket_id' => $ticket->id,
                            'jira_key'  => $jiraKey,
                        ]);
                    } else {
                        Log::warning('jira.create.new_no_key', [
                            'ticket_id' => $ticket->id,
                            'response'  => $jiraResponse,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('jira.create.new_jira_error', [
                        'ticket_id' => $ticket->id,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            Log::info('jira.create.ticket', [
                'ticket_id' => $ticket->id,
                'jira_key'  => $jiraKey,
                'summary'   => $data['summary'],
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Ticket created.',
                'ticket_id' => $ticket->id,
                'jira_key'  => $jiraKey,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('jira.create.exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la création du ticket.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
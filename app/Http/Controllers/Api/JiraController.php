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
     * POST /api/jira/create
     *
     * Updates an existing Laravel ticket with Jira key, or creates a new one.
     * One Jira issue = One Laravel ticket (unique constraint on jira_ticket_id).
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

            $data   = $validator->validated();
            $jiraKey = $data['jira_key'] ?? null;

            // ── 2. If jira_key provided, find existing ticket and update it ──
            if ($jiraKey) {
                $existingTicket = Ticket::where('jira_ticket_id', $jiraKey)->first();

                if ($existingTicket) {
                    // Update the existing ticket with latest data from Jira
                    $existingTicket->update([
                        'title'           => $data['summary'],
                        'description'     => $data['description'],
                        'priority'        => $data['priority'] ?? $existingTicket->priority,
                        'category'        => $data['category'] ?? $existingTicket->category,
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

            // ── 3. Check for duplicate by conversation_id + title ──────
            $existingTicket = Ticket::where('conversation_id', $data['conversation_id'])
                ->where('title', $data['summary'])
                ->first();

            if ($existingTicket) {
                // Link the Jira key to the existing ticket
                if ($jiraKey && empty($existingTicket->jira_ticket_id)) {
                    $existingTicket->update(['jira_ticket_id' => $jiraKey]);
                }

                Log::info('jira.create.duplicate_skipped', [
                    'existing_ticket_id' => $existingTicket->id,
                    'summary'            => $data['summary'],
                ]);

                return response()->json([
                    'success'   => true,
                    'message'   => 'Ticket already exists.',
                    'ticket_id' => $existingTicket->id,
                    'jira_key'  => $existingTicket->jira_ticket_id ?? $jiraKey,
                ], 200);
            }

            // ── 4. Create new Laravel ticket ───────────────────────────
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
            ]);

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
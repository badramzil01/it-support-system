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
     * Crée un ticket Laravel puis tente de le pousser vers Jira.
     *
     * Payload attendu par n8n :
     *   conversation_id  (int|required)
     *   user_id          (int|required)
     *   summary          (string|required)
     *   description      (string|required)
     *   priority         (string|in:low,medium,high,critical)
     *   category         (string|nullable)
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

            $data = $validator->validated();

            // ── 2. Création du ticket dans Laravel ────────────────────
            $ticket = Ticket::create([
                'conversation_id' => $data['conversation_id'],
                'user_id'         => $data['user_id'],
                'title'           => $data['summary'],
                'description'     => $data['description'],
                'priority'        => $data['priority'] ?? 'medium',
                'category'        => $data['category'] ?? null,
                'status'          => 'open',
                'ticket_status'   => 'open',
                'source'          => 'n8n',
            ]);

            Log::info('jira.create.ticket', [
                'ticket_id' => $ticket->id,
                'summary'   => $data['summary'],
            ]);

            // ── 3. Appel du service Jira ──────────────────────────────
            $jiraKey = null;

            try {
                /** @var JiraService $jiraService */
                $jiraService = app(JiraService::class);

                $jiraResponse = $jiraService->createTicket(
                    $data['summary'],
                    $data['description']
                );

                // Extraire la clé Jira depuis la réponse
                $jiraKey = $jiraResponse['key'] ?? null;

                if ($jiraKey) {
                    $ticket->update([
                        'jira_ticket_id' => $jiraKey,
                        'ticket_status'  => 'in_progress',
                    ]);

                    Log::info('jira.create.success', [
                        'ticket_id' => $ticket->id,
                        'jira_key'  => $jiraKey,
                    ]);
                }
            } catch (\Throwable $jiraException) {
                // L'appel Jira échoue mais le ticket Laravel est créé
                Log::warning('jira.create.failed', [
                    'ticket_id' => $ticket->id,
                    'error'     => $jiraException->getMessage(),
                ]);
            }

            // ── 4. Réponse JSON ───────────────────────────────────────
            return response()->json([
                'success'   => true,
                'message'   => $jiraKey
                    ? 'Ticket Laravel et issue Jira créés avec succès.'
                    : 'Ticket Laravel créé. Jira non disponible.',
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
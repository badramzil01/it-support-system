<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Vérifier si un ticket existe déjà
     */
    public function checkTicket(Request $request)
    {
        if (!$request->conversation_id) {

            return response()->json([
                'exists' => false,
                'message' => 'conversation_id required'
            ]);
        }

        $ticket = Ticket::where(
            'conversation_id',
            $request->conversation_id
        )
        ->where('status', 'open')
        ->first();

        return response()->json([
            'exists' => $ticket ? true : false,
            'ticket' => $ticket
        ]);
    }

    /**
     * Création ticket
     */
    public function createTicket(Request $request)
    {
        return $this->saveTicket($request);
    }

    /**
     * Alias store
     */
    public function store(Request $request)
    {
        return $this->saveTicket($request);
    }

    /**
     * Sauvegarde ticket
     */
    private function saveTicket(Request $request)
    {
        try {

            Log::info('FULL REQUEST', $request->all());

            Log::info('TICKET_CONTROLLER_RECEIVED', [
                'conversation_id' => $request->input('conversation_id'),
                'user_id' => $request->input('user_id'),
                'is_escalated' => $request->input('is_escalated'),
                'is_urgent' => $request->input('is_urgent'),
                'priority' => $request->input('priority'),
                'category' => $request->input('category'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ]);

            $conversation = null;

            if (!empty($request->conversation_id)) {

                $conversation = Conversation::find(
                    $request->conversation_id
                );
            }

            $conversationId =
                $request->conversation_id
                ?? null;

            $userId =
                $request->user_id
                ?? $conversation?->user_id
                ?? auth()->id()
                ?? null;

            $isEscalated = filter_var(
                $request->input('is_escalated', false),
                FILTER_VALIDATE_BOOLEAN
            );

            $isUrgent = filter_var(
                $request->input('is_urgent', false),
                FILTER_VALIDATE_BOOLEAN
            );

            Log::info('ticket.create.request', [
                'conversation_id' => $conversationId,
                'request_user_id' => $request->user_id,
                'conversation_user_id' => $conversation?->user_id,
                'final_user_id' => $userId,
                'is_escalated' => $isEscalated,
                'is_urgent' => $isUrgent,
            ]);

            $ticket = Ticket::create([

                'conversation_id' => $conversationId,

                'user_id' => $userId,

                'message_id' => $request->message_id,

                'trigger_message_id' => $request->trigger_message_id
                    ?? $request->message_id,

                'assigned_to' => null,

                'title' => $request->title
                    ?? 'Support Ticket',

                'description' => $request->description
                    ?? '',

                'jira_ticket_id' => $request->jira_ticket_id,

                'solution' => $request->solution
                    ?? null,

                'source' => $request->source
                    ?? 'ai',

                'status' => 'open',

                'priority' => $request->priority
                    ?? 'medium',

                'category' => $request->category
                    ?? 'general',

                'confidence' => (float)
                    ($request->confidence ?? 0),

                'feedback' => $request->feedback,

                'is_urgent' => $isUrgent,

                'is_escalated' => $isEscalated,

                'has_image' => filter_var(
                    $request->input('has_image', false),
                    FILTER_VALIDATE_BOOLEAN
                ),

                'image_url' => $request->input('image_url'),

                'mime_type' => $request->input('mime_type'),

                'ticket_status' => 'pending',

                // Routage automatique : tous les tickets clients démarrent en N1
                'support_level' => 'N1',
                'assigned_team' => 'support_n1',
                'escalation_level' => 0,
            ]);

            Log::info('ticket.created', [
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'conversation_id' => $ticket->conversation_id,
                'support_level' => $ticket->support_level,
                'assigned_team' => $ticket->assigned_team,
                'escalation_level' => $ticket->escalation_level,
                'is_escalated' => $ticket->is_escalated,
                'is_urgent' => $ticket->is_urgent,
                'title' => $ticket->title,
            ]);

            return response()->json([
                'success' => true,
                'ticket' => $ticket
            ]);

        } catch (\Throwable $e) {

            Log::error('ticket.create.error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour statut
     */
    public function updateStatus(Request $request)
    {
        $ticket = Ticket::find(
            $request->ticket_id
        );

        if (!$ticket) {

            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }
}

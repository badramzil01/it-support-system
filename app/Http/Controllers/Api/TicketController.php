<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{

    // =====================================================
    // ✅ CHECK IF TICKET EXISTS
    // =====================================================
    public function checkTicket(Request $request)
    {

        $conversationId = $request->conversation_id;

        if (!$conversationId) {

            return response()->json([

                'exists' => false,

                'message' => 'conversation_id required'

            ]);

        }

        $ticket = Ticket::where('conversation_id', $conversationId)
            ->where('status', 'open')
            ->first();

        return response()->json([

            'exists' => $ticket ? true : false,

            'ticket' => $ticket

        ]);

    }


    // =====================================================
    // ✅ CREATE TICKET
    // =====================================================
    public function createTicket(Request $request)
    {

        $ticket = Ticket::create([

            'conversation_id' => $request->conversation_id,

            'user_id' => $request->user_id,

            'message_id' => $request->message_id,

            'title' => $request->title ?? 'Support Ticket',

            'description' => $request->description ?? 'No description',

            'priority' => $request->priority ?? 'medium',

            'category' => $request->category ?? 'general',

            'status' => $request->status ?? 'open',

            'jira_ticket_id' => $request->jira_ticket_id,

            'solution' => $request->solution,

            'source' => $request->source ?? 'ai',

            'confidence' => $request->confidence ?? 0,

            'feedback' => $request->feedback,

            'is_escalated' => $request->is_escalated ?? false

        ]);

        return response()->json([

            'success' => true,

            'ticket' => $ticket

        ]);

    }


    // =====================================================
    // ✅ SAVE TICKET
    // =====================================================
    public function store(Request $request)
    {

        $ticket = Ticket::create([

            'conversation_id' => $request->conversation_id,

            'user_id' => $request->user_id,

            'message_id' => $request->message_id,

            'title' => $request->title ?? 'Support Ticket',

            'description' => $request->description ?? 'No description',

            'priority' => $request->priority ?? 'medium',

            'category' => $request->category ?? 'general',

            'status' => $request->status ?? 'open',

            'jira_ticket_id' => $request->jira_ticket_id,

            'solution' => $request->solution,

            'source' => $request->source ?? 'ai',

            'confidence' => $request->confidence ?? 0,

            'feedback' => $request->feedback,

            'is_escalated' => $request->is_escalated ?? false

        ]);

        return response()->json([

            'success' => true,

            'ticket' => $ticket

        ]);

    }


    // =====================================================
    // ✅ UPDATE TICKET STATUS
    // =====================================================
    public function updateStatus(Request $request)
    {

        $ticket = Ticket::find($request->ticket_id);

        if (!$ticket) {

            return response()->json([

                'success' => false,

                'message' => 'Ticket not found'

            ], 404);

        }

        $ticket->status = $request->status;

        $ticket->save();

        return response()->json([

            'success' => true,

            'ticket' => $ticket

        ]);

    }

}
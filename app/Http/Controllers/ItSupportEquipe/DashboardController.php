<?php

namespace App\Http\Controllers\ItSupportEquipe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Conversation;


class DashboardController extends Controller
{
    public function index()
    {
        return view('support.dashboard', [
            'tickets_open' => Ticket::where('status','open')->count(),
            'tickets_escalated' => Ticket::where('is_escalated',1)->count(),
            'my_tickets' => Ticket::where('user_id', auth()->id())->count(),
        ]);

    }
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        return view('support.tickets.show', compact('ticket'));
    }

    public function ListeTickets()
    {
        $tickets = Ticket::where('user_id', auth()->id())->latest()->get();;

        return view('support.tickets', compact('tickets'));
    }

    public function listeConversations()
    {
        $conversations = Conversation::all();

        return view('support.conversations', compact('conversations'));
    }
}

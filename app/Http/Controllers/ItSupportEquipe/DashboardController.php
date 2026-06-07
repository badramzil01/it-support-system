<?php

namespace App\Http\Controllers\ItSupportEquipe;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Conversation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalTickets' => Ticket::count(),

            'openTickets' => Ticket::where('status', 'open')->count(),

            'closedTickets' => Ticket::where('status', 'closed')->count(),

            'escalatedTickets' => Ticket::where('is_escalated', 1)->count(),

            'highPriorityTickets' => Ticket::where('priority', 'high')->count(),

            'todayConversations' => Conversation::whereDate(
                'created_at',
                Carbon::today()
            )->count(),
        ];

        $recentTickets = Ticket::latest()
            ->take(10)
            ->get();

        return view(
            'support.dashboard',
            compact(
                'stats',
                'recentTickets'
            )
        );
    }

    public function listeTickets()
    {
        $tickets = Ticket::latest()
            ->paginate(20);

        return view(
            'support.tickets',
            compact('tickets')
        );
    }

    public function listeConversations()
    {
        $conversations = Conversation::latest()
            ->paginate(20);

        return view(
            'support.conversations',
            compact('conversations')
        );
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        return view(
            'support.tickets.show',
            compact('ticket')
        );
    }
}
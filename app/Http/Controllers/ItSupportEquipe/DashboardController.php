<?php

namespace App\Http\Controllers\ItSupportEquipe;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Conversation;
use App\Models\User;
use App\Models\SupportTeam;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userLevel = $this->getUserSupportLevel($user);

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
                'recentTickets',
                'userLevel'
            )
        );
    }

    private function getUserSupportLevel($user): ?string
    {
        if ($user->hasRole('admin')) return null;
        $team = SupportTeam::whereHas('members', fn($q) => $q->where('user_id', $user->id))->first();
        return $team?->support_level;
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
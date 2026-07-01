<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\SupportTeam;
use Illuminate\Http\Request;

class EscalatedTicketsController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketEscalation::with([
            'ticket.user',
            'ticket.assignedAgent',
            'escalatedBy',
            'assignedTo'
        ])->latest();

        // Filter by escalation direction
        if ($request->filled('direction')) {
            $direction = $request->input('direction');
            $parts = explode('_to_', $direction);
            if (count($parts) === 2) {
                $query->where('from_level', $parts[0])->where('to_level', $parts[1]);
            }
        }

        // Filter by date range
        if ($request->filled('period')) {
            $query->where(function($q) use ($request) {
                switch ($request->input('period')) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                        break;
                }
            });
        }

        // Search by ticket title/ID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('ticket', function($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%")
                       ->orWhere('id', $search);
                });
            });
        }

        $escalations = $query->paginate(20)->appends($request->query());

        // Stats for dashboard cards
        $stats = [
            'total_escalations' => TicketEscalation::count(),
            'escalated_today' => TicketEscalation::whereDate('created_at', today())->count(),
            'escalated_this_week' => TicketEscalation::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'escalated_this_month' => TicketEscalation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Tickets by team (for dashboards)
        $ticketsByTeam = [
            'n1_total' => Ticket::where('support_level', 'n1')->whereNotIn('status', ['closed', 'resolved'])->count(),
            'n1_escalated' => TicketEscalation::where('from_level', 'n1')->count(),
            'n2_from_n1' => TicketEscalation::where('from_level', 'n1')->where('to_level', 'n2')->count(),
            'n2_total' => Ticket::where('support_level', 'n2')->whereNotIn('status', ['closed', 'resolved'])->count(),
            'n2_escalated_to_n3' => TicketEscalation::where('from_level', 'n2')->where('to_level', 'n3')->count(),
            'n3_from_n2' => TicketEscalation::where('from_level', 'n2')->where('to_level', 'n3')->count(),
            'n3_total' => Ticket::where('support_level', 'n3')->whereNotIn('status', ['closed', 'resolved'])->count(),
            'n3_critical' => Ticket::where('support_level', 'n3')->where('priority', 'critical')->whereNotIn('status', ['closed', 'resolved'])->count(),
            'manager_total' => Ticket::where('support_level', 'manager')->whereNotIn('status', ['closed', 'resolved'])->count(),
        ];

        $directions = [
            'n1_to_n2' => 'N1 → N2',
            'n2_to_n3' => 'N2 → N3',
            'n3_to_manager' => 'N3 → Manager',
        ];

        return view('admin.escalations.index', compact('escalations', 'stats', 'ticketsByTeam', 'directions'));
    }
}
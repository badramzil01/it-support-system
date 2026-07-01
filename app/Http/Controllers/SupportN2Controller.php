<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\User;
use App\Models\Notification as AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportN2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:support_n2|admin');
    }

    /**
     * Dashboard N2 : tickets support_level = N2, non résolus.
     */
    public function dashboard(Request $request)
    {
        $query = Ticket::with(['user', 'assignedAgent', 'escalatedByUser'])
            ->N2()
            ->notResolved()
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low') ASC")
            ->orderBy('escalated_at', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Ticket::N2()->notResolved()->count(),
            'urgent' => Ticket::N2()->notResolved()->where('priority', 'high')->count(),
            'in_progress' => Ticket::N2()->notResolved()->where('status', 'in_progress')->count(),
            'pending' => Ticket::N2()->notResolved()->where('status', 'open')->count(),
        ];

        return view('support-n2.dashboard', compact('tickets', 'stats'));
    }

    /**
     * Voir un ticket N2 en détail.
     */
    public function show(Ticket $ticket)
    {
        if ($ticket->support_level !== 'N2') {
            abort(403, 'Ce ticket n\'est pas au niveau N2.');
        }

        $ticket->load([
            'user',
            'assignedAgent',
            'escalatedByUser',
            'resolvedByUser',
            'escalationHistory.escalatedBy',
            'escalationHistory.assignedTo',
            'conversation',
            'message',
        ]);

        return view('support-n2.show', compact('ticket'));
    }

    /**
     * Mettre à jour le statut d'un ticket N2.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if ($ticket->support_level !== 'N2') {
            return back()->with('error', 'Action non autorisée sur ce ticket.');
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $newStatus = $request->input('status');
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $updateData['resolved_at'] = now();
            $updateData['resolved_by'] = auth()->id();
        }

        if ($newStatus === 'closed') {
            $updateData['closed_at'] = now();
            $updateData['resolved_by'] = $updateData['resolved_by'] ?? auth()->id();
        }

        if ($newStatus === 'in_progress') {
            $updateData['assigned_to'] = $updateData['assigned_to'] ?? auth()->id();
        }

        $ticket->update($updateData);

        // Notifier l'employé si résolu
        if (in_array($newStatus, ['resolved', 'closed'])) {
            AppNotification::create([
                'user_id' => $ticket->user_id,
                'type' => 'ticket_resolved',
                'ticket_id' => $ticket->id,
                'status' => 'pending',
                'sent_at' => now(),
                'data' => [
                    'ticket_title' => $ticket->title,
                    'status' => $newStatus,
                    'resolved_by' => auth()->user()->name,
                ],
            ]);
        }

        Log::info('n2.ticket.status_updated', [
            'ticket_id' => $ticket->id,
            'status' => $newStatus,
            'by' => auth()->id(),
        ]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Retourner un ticket de N2 vers N1.
     */
    public function returnToN1(Request $request, Ticket $ticket)
    {
        if ($ticket->support_level !== 'N2') {
            return back()->with('error', 'Action non autorisée sur ce ticket.');
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:2000',
        ]);

        $reason = $request->input('reason');
        $user = auth()->user();

        // Créer l'historique
        TicketEscalation::create([
            'ticket_id' => $ticket->id,
            'from_level' => 'N2',
            'to_level' => 'N1',
            'escalated_by' => $user->id,
            'reason' => 'Retour vers N1 : ' . $reason,
        ]);

        // Mettre à jour le ticket
        $ticket->update([
            'support_level'     => 'N1',
            'assigned_team'     => 'support_n1',
            'escalated'         => false,
            'escalated_by'      => null,
            'escalated_at'      => null,
            'is_escalated'      => false,
            'escalation_reason' => null,
            'status'            => 'open',
        ]);

        // Notifier les agents N1
        $n1Users = User::role('support_n1')->get();
        foreach ($n1Users as $n1User) {
            AppNotification::create([
                'user_id' => $n1User->id,
                'type' => 'ticket_returned',
                'ticket_id' => $ticket->id,
                'status' => 'pending',
                'sent_at' => now(),
                'data' => [
                    'reason' => $reason,
                    'from_level' => 'N2',
                    'to_level' => 'N1',
                    'ticket_title' => $ticket->title,
                ],
            ]);
        }

        Log::info('n2.ticket.returned_to_n1', [
            'ticket_id' => $ticket->id,
            'by' => $user->id,
            'reason' => $reason,
        ]);

        return redirect()->route('support-n2.dashboard')
            ->with('success', 'Ticket retourné vers le niveau N1.');
    }
}
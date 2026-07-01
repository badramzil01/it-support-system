<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\User;
use App\Models\Notification as AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportN1Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:support_n1|support_n2|admin');
    }

    /**
     * Dashboard N1 : tickets support_level = N1, non résolus.
     */
    public function dashboard(Request $request)
    {
        $query = Ticket::with(['user', 'assignedAgent', 'escalatedByUser'])
            ->N1()
            ->notResolved()
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low') ASC")
            ->orderBy('created_at', 'desc');

        // Filtres
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

        // Statistiques pour les compteurs
        $stats = [
            'total' => Ticket::N1()->notResolved()->count(),
            'urgent' => Ticket::N1()->notResolved()->where('priority', 'high')->count(),
            'in_progress' => Ticket::N1()->notResolved()->where('status', 'in_progress')->count(),
            'pending' => Ticket::N1()->notResolved()->where('status', 'open')->count(),
        ];

        return view('support-n1.dashboard', compact('tickets', 'stats'));
    }

    /**
     * Voir un ticket N1 en détail.
     */
    public function show(Ticket $ticket)
    {
        // Sécurité : seul le N1 peut voir ses tickets
        if ($ticket->support_level !== 'N1') {
            abort(403, 'Ce ticket n\'est pas au niveau N1.');
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

        return view('support-n1.show', compact('ticket'));
    }

    /**
     * Mettre à jour le statut d'un ticket N1.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if ($ticket->support_level !== 'N1') {
            return back()->with('error', 'Action non autorisée sur ce ticket.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,open',
        ]);

        $newStatus = $request->input('status');
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $updateData['resolved_at'] = now();
            $updateData['resolved_by'] = auth()->id();
        }

        if ($newStatus === 'in_progress') {
            $updateData['assigned_to'] = $updateData['assigned_to'] ?? auth()->id();
        }

        $ticket->update($updateData);

        Log::info('n1.ticket.status_updated', [
            'ticket_id' => $ticket->id,
            'status' => $newStatus,
            'by' => auth()->id(),
        ]);

        $message = $newStatus === 'resolved'
            ? 'Ticket résolu avec succès.'
            : 'Statut mis à jour avec succès.';

        return back()->with('success', $message);
    }

    /**
     * Escalader un ticket de N1 vers N2.
     */
    public function escalate(Request $request, Ticket $ticket)
    {
        if ($ticket->support_level !== 'N1') {
            return back()->with('error', 'Ce ticket n\'est pas au niveau N1.');
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:2000',
        ]);

        $user = auth()->user();
        $reason = $request->input('reason');

        // Utiliser la méthode du modèle pour l'escalade
        $success = $ticket->escalateToN2($user->id, $reason);

        if (!$success) {
            return back()->with('error', 'Impossible d\'escalader ce ticket.');
        }

        // Notifier les agents N2
        $n2Users = User::role('support_n2')->get();
        foreach ($n2Users as $n2User) {
            AppNotification::create([
                'user_id' => $n2User->id,
                'type' => 'ticket_escalated',
                'ticket_id' => $ticket->id,
                'status' => 'pending',
                'sent_at' => now(),
                'data' => [
                    'reason' => $reason,
                    'from_level' => 'N1',
                    'to_level' => 'N2',
                    'ticket_title' => $ticket->title,
                ],
            ]);
        }

        Log::info('n1.ticket.escalated_to_n2', [
            'ticket_id' => $ticket->id,
            'escalated_by' => $user->id,
            'reason' => $reason,
        ]);

        return redirect()->route('support-n1.dashboard')
            ->with('success', 'Ticket escaladé vers le niveau N2 avec succès.');
    }
}
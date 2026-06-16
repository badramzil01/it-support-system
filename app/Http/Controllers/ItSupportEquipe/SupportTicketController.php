<?php

namespace App\Http\Controllers\ItSupportEquipe;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\JiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SupportTicketController extends Controller
{
    /**
     * Liste des tickets
     */
    public function index(Request $request)
    {
        // Only show tickets synchronized with Jira
        $query = Ticket::with([
            'user',
            'assignedAgent'
        ])
            ->whereNotNull('jira_ticket_id')
            ->where('jira_ticket_id', '!=', '');

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'title',
                    'like',
                    '%' . $request->search . '%'
                )
                ->orWhere(
                    'description',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'support.tickets',
            compact('tickets')
        );
    }

    /**
     * Détail ticket
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'user',
            'assignedAgent',
            'conversation',
            'message'
        ]);

        return view(
            'support.tickets-show',
            compact('ticket')
        );
    }

    /**
     * Assigner ticket à moi
     */
    public function assignToMe(Ticket $ticket)
    {
        $ticket->update([
            'assigned_to' => auth()->id()
        ]);

        return back()->with(
            'success',
            'Ticket assigné avec succès.'
        );
    }

    /**
     * Changer statut
     */
    public function updateStatus(
        Request $request,
        Ticket $ticket,
        JiraService $jiraService
    ) {
        $request->validate([
            'status' => [
                'required',
                'in:open,in_progress,resolved,closed'
            ]
        ]);

        $newStatus = $request->status;

        Log::info('ticket.status.change.request', [
            'ticket_id'      => $ticket->id,
            'jira_ticket_id' => $ticket->jira_ticket_id,
            'old_status'     => $ticket->status,
            'new_status'     => $newStatus,
        ]);

        // Update Jira first — if it fails, do NOT update Laravel
        if (!empty($ticket->jira_ticket_id)) {
            try {
                Log::info('jira.sync.start', [
                    'ticket_id' => $ticket->id,
                    'jira_key'  => $ticket->jira_ticket_id,
                    'status'    => $newStatus,
                ]);

                $jiraSuccess = false;

                switch ($newStatus) {
                    case 'open':
                        $jiraSuccess = $jiraService->moveToTodo($ticket->jira_ticket_id);
                        break;
                    case 'in_progress':
                        $jiraSuccess = $jiraService->moveToInProgress($ticket->jira_ticket_id);
                        break;
                    case 'resolved':
                    case 'closed':
                        $jiraSuccess = $jiraService->moveToDone($ticket->jira_ticket_id);
                        break;
                }

                Log::info('jira.sync.result', [
                    'ticket_id' => $ticket->id,
                    'jira_key'  => $ticket->jira_ticket_id,
                    'success'   => $jiraSuccess,
                ]);

                if (!$jiraSuccess) {
                    return back()->with('error', 'Jira update failed. Status not changed.');
                }
            } catch (\Exception $e) {
                Log::error('jira.sync.error', [
                    'ticket_id'      => $ticket->id,
                    'jira_ticket_id' => $ticket->jira_ticket_id,
                    'message'        => $e->getMessage(),
                    'line'           => $e->getLine(),
                    'file'           => $e->getFile(),
                ]);
                return back()->with('error', 'Jira update failed. Status not changed.');
            }
        }

        // Jira updated (or no Jira key) — now update Laravel
        $data = ['status' => $newStatus];
        if ($newStatus === 'resolved') $data['resolved_at'] = Carbon::now();
        if ($newStatus === 'closed') $data['closed_at'] = Carbon::now();
        $ticket->update($data);

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Tickets ouverts
     */
    public function openTickets()
    {
        $tickets = Ticket::where(
            'status',
            'open'
        )
        ->latest()
        ->paginate(15);

        return view(
            'support.tickets',
            compact('tickets')
        );
    }

    /**
     * Tickets fermés
     */
    public function closedTickets()
    {
        $tickets = Ticket::where(
            'status',
            'closed'
        )
        ->latest()
        ->paginate(15);

        return view(
            'support.tickets',
            compact('tickets')
        );
    }

    /**
     * Tickets escaladés
     */
    public function escalatedTickets()
    {
        $tickets = Ticket::where(
            'is_escalated',
            true
        )
        ->latest()
        ->paginate(15);

        return view(
            'support.tickets',
            compact('tickets')
        );
    }

    /**
     * Mes tickets
     */
    public function myTickets()
    {
        $tickets = Ticket::where(
            'assigned_to',
            auth()->id()
        )
        ->latest()
        ->paginate(15);

        return view(
            'support.tickets',
            compact('tickets')
        );
    }
    
}
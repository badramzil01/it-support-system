<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Services\JiraService;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Only show tickets synchronized with Jira
        $query = Ticket::with(['user', 'assignedAgent'])
            ->whereNotNull('jira_ticket_id')
            ->where('jira_ticket_id', '!=', '');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('title', 'like', "%{$s}%")->orWhere('description','like', "%{$s}%");
        }

        foreach (['status','priority','category'] as $f) {
            if ($request->filled($f)) {
                $query->where($f, $request->input($f));
            }
        }

        if ($request->filled('is_urgent')) {
            $query->where('is_urgent', (bool) $request->input('is_urgent'));
        }

        if ($request->filled('is_escalated')) {
            $query->where('is_escalated', (bool) $request->input('is_escalated'));
        }

        if ($request->filled('assigned_to_me') && auth()->check()) {
            $query->where('assigned_to', auth()->id());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sorting
        $allowedSorts = ['id','created_at','priority','status','category','jira_ticket_id','title'];
        $sort = $request->input('sort');
        $direction = $request->input('direction','desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(20)->appends($request->query());

        // For filters UI
        $statuses = Ticket::select('status')->distinct()->pluck('status')->filter()->values();
        $priorities = Ticket::select('priority')->distinct()->pluck('priority')->filter()->values();
        $categories = Ticket::select('category')->distinct()->pluck('category')->filter()->values();

        return view('support.tickets.index', compact('tickets','statuses','priorities','categories','sort','direction'));
    }

    public function show(Ticket $ticket)
    {
        return view('support.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|string']);
        $newStatus = $request->input('status');

        // Update Jira first — if it fails, do NOT update Laravel
        if (!empty($ticket->jira_ticket_id)) {
            try {
                $jira = app(JiraService::class);
                $ok = true;
                if ($newStatus === 'open') $ok = $jira->moveToTodo($ticket->jira_ticket_id);
                elseif ($newStatus === 'in_progress') $ok = $jira->moveToInProgress($ticket->jira_ticket_id);
                elseif (in_array($newStatus, ['resolved','closed'], true)) $ok = $jira->moveToDone($ticket->jira_ticket_id);

                if (!$ok) {
                    \Log::warning('jira.update.failed', ['ticket_id' => $ticket->id, 'jira' => $ticket->jira_ticket_id, 'status' => $newStatus]);
                    return back()->with('error', 'Jira update failed. Status not changed.');
                }
            } catch (\Throwable $e) {
                \Log::error('jira.update.error', ['error' => $e->getMessage(), 'ticket_id' => $ticket->id]);
                return back()->with('error', 'Jira update failed. Status not changed.');
            }
        }

        // Jira updated (or no Jira key) — now update Laravel
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'resolved') $updateData['resolved_at'] = now();
        if ($newStatus === 'closed') $updateData['closed_at'] = now();
        $ticket->update($updateData);

        return back()->with('success', 'Status updated');
    }

    public function assignToMe(Request $request, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => auth()->id() ?? null]);
        return back()->with('success', 'Ticket assigned to you');
    }
}

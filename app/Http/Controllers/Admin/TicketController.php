<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\JiraService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Only show tickets synchronized with Jira
        $query = Ticket::with(['user','assignedAgent'])
            ->whereNotNull('jira_ticket_id')
            ->where('jira_ticket_id', '!=', '');

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
            });
        }
        foreach (['status','priority','category'] as $f) {
            if ($request->filled($f)) $query->where($f, $request->input($f));
        }
        if ($request->filled('is_urgent')) $query->where('is_urgent', (bool) $request->input('is_urgent'));
        if ($request->filled('is_escalated')) $query->where('is_escalated', (bool) $request->input('is_escalated'));
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));

        $allowedSorts = ['id','created_at','priority','status','category','jira_ticket_id','title'];
        $sort = $request->input('sort');
        $direction = $request->input('direction','desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $allowedSorts)) $query->orderBy($sort, $direction);
        else $query->orderBy('created_at','desc');

        $tickets = $query->paginate(20)->appends($request->query());

        $statuses   = Ticket::select('status')->distinct()->pluck('status')->filter()->values();
        $priorities = Ticket::select('priority')->distinct()->pluck('priority')->filter()->values();
        $categories = Ticket::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.tickets.index', compact('tickets','statuses','priorities','categories','sort','direction'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user','assignedAgent','conversation','message']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function assignToMe(Ticket $ticket)
    {
        $ticket->update(['assigned_to' => auth()->id()]);
        return back()->with('success', 'Ticket assigné avec succès.');
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

    public function exportCsv(Request $request): StreamedResponse
    {
        $tickets = $this->buildExportQuery($request)->get();
        $filename = 'tickets_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = ['ID','Titre','Description','Utilisateur','Statut','Priorité','Catégorie','Urgent','Escaladé','Jira','Source','Créé le','Résolu le'];
        $callback = function() use ($tickets, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns, ';');
            foreach ($tickets as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->title,
                    strip_tags((string) $t->description),
                    optional($t->user)->name,
                    $t->status,
                    $t->priority,
                    $t->category,
                    $t->is_urgent ? 'Oui' : 'Non',
                    $t->is_escalated ? 'Oui' : 'Non',
                    $t->jira_ticket_id,
                    $t->source,
                    optional($t->created_at)->format('d/m/Y H:i'),
                    optional($t->resolved_at)->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        // Excel CSV format (UTF-8 with BOM, ; separator)
        $tickets = $this->buildExportQuery($request)->get();
        $filename = 'tickets_'.now()->format('Ymd_His').'.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = ['ID','Titre','Description','Utilisateur','Statut','Priorité','Catégorie','Urgent','Escaladé','Jira','Source','Créé le','Résolu le'];
        $callback = function() use ($tickets, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns, "\t");
            foreach ($tickets as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->title,
                    strip_tags((string) $t->description),
                    optional($t->user)->name,
                    $t->status,
                    $t->priority,
                    $t->category,
                    $t->is_urgent ? 'Oui' : 'Non',
                    $t->is_escalated ? 'Oui' : 'Non',
                    $t->jira_ticket_id,
                    $t->source,
                    optional($t->created_at)->format('d/m/Y H:i'),
                    optional($t->resolved_at)->format('d/m/Y H:i'),
                ], "\t");
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    private function buildExportQuery(Request $request)
    {
        $query = Ticket::with(['user']);
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
            });
        }
        foreach (['status','priority','category'] as $f) {
            if ($request->filled($f)) $query->where($f, $request->input($f));
        }
        if ($request->filled('is_urgent')) $query->where('is_urgent', (bool) $request->input('is_urgent'));
        if ($request->filled('is_escalated')) $query->where('is_escalated', (bool) $request->input('is_escalated'));
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));
        return $query->orderBy('created_at','desc');
    }
}

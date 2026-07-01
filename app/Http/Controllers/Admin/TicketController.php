<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\User;
use App\Models\SupportTeam;
use App\Models\Notification as AppNotification;
use App\Services\JiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if ($request->filled('support_level')) $query->where('support_level', $request->input('support_level'));
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));

        $allowedSorts = ['id','created_at','priority','status','category','jira_ticket_id','title','support_level'];
        $sort = $request->input('sort');
        $direction = $request->input('direction','desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $allowedSorts)) $query->orderBy($sort, $direction);
        else $query->orderBy('created_at','desc');

        $tickets = $query->paginate(20)->appends($request->query());

        $statuses   = Ticket::select('status')->distinct()->pluck('status')->filter()->values();
        $priorities = Ticket::select('priority')->distinct()->pluck('priority')->filter()->values();
        $categories = Ticket::select('category')->distinct()->pluck('category')->filter()->values();
        $levels     = ['n1' => 'N1', 'n2' => 'N2', 'n3' => 'N3', 'manager' => 'Manager'];
        $supportTeams = SupportTeam::where('is_active', true)->get();

        return view('admin.tickets.index', compact('tickets','statuses','priorities','categories','sort','direction','levels','supportTeams'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user','assignedAgent','conversation','message', 'escalationHistory.escalatedBy', 'escalationHistory.assignedTo']);
        $levels = ['n1' => 'N1', 'n2' => 'N2', 'n3' => 'N3', 'manager' => 'Support Manager'];
        $supportTeams = SupportTeam::where('is_active', true)->get();
        $supportUsers = User::role('support')->get();
        $escalationHistory = $ticket->escalationHistory()->with(['escalatedBy', 'assignedTo'])->get();

        return view('admin.tickets.show', compact('ticket', 'levels', 'supportTeams', 'supportUsers', 'escalationHistory'));
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

    /**
     * Manual escalation to a different support level.
     */
    public function escalate(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'to_level' => 'required|in:n1,n2,n3,manager',
            'reason' => 'required|string|min:5',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $user = auth()->user();
        $fromLevel = $ticket->support_level ?? 'n1';
        $toLevel = $validated['to_level'];

        // Validate escalation rules
        $canEscalate = $this->validateEscalationRule($user, $fromLevel, $toLevel);
        if (!$canEscalate) {
            return back()->with('error', 'You do not have permission to perform this escalation.');
        }

        // Find target team
        $targetTeam = SupportTeam::where('support_level', $toLevel)
            ->where('is_active', true)
            ->first();

        if (!$targetTeam) {
            return back()->with('error', 'Target support team not found or inactive.');
        }

        // Determine new assignee
        $newAssignee = null;
        if (!empty($validated['assigned_to'])) {
            $newAssignee = User::find($validated['assigned_to']);
        } else {
            // Find available agent in target team
            $newAssignee = $this->findAvailableAgent($targetTeam);
        }

        $previousOwner = $ticket->assigned_to;

        // Create escalation log
        TicketEscalation::create([
            'ticket_id' => $ticket->id,
            'from_level' => $fromLevel,
            'to_level' => $toLevel,
            'escalated_by' => $user->id,
            'assigned_to' => $newAssignee?->id,
            'reason' => $validated['reason'],
        ]);

        // Also log in the existing EscalationLog for SLA tracking
        \App\Models\EscalationLog::create([
            'ticket_id' => $ticket->id,
            'from_level' => $this->levelToNumber($fromLevel),
            'to_level' => $this->levelToNumber($toLevel),
            'from_user_id' => $previousOwner,
            'to_user_id' => $newAssignee?->id,
            'reason' => 'Manual escalation: ' . $validated['reason'],
            'sla_breached' => false,
        ]);

        // Update ticket
        $ticket->update([
            'support_level' => $toLevel,
            'assigned_team' => $targetTeam->slug,
            'assigned_to' => $newAssignee?->id,
            'escalation_level' => $this->levelToNumber($toLevel),
            'is_escalated' => true,
            'escalated_at' => now(),
            'previous_owner' => $previousOwner,
            'escalation_reason' => 'Manual escalation: ' . $validated['reason'],
            'sla_deadline' => now()->addHours($targetTeam->getSlaHours($ticket->priority ?? 'medium')),
        ]);

        Log::info('ticket.manual_escalated', [
            'ticket_id' => $ticket->id,
            'from_level' => $fromLevel,
            'to_level' => $toLevel,
            'by' => $user->id,
            'assigned_to' => $newAssignee?->id,
        ]);

        // Send notifications
        $this->notifyEscalation($ticket, $targetTeam, $newAssignee, $validated['reason']);

        // Sync to Jira
        $this->syncEscalationToJira($ticket, $toLevel, $validated['reason'], $newAssignee);

        return back()->with('success', "Ticket escalated from " . strtoupper($fromLevel) . " to " . strtoupper($toLevel) . " successfully.");
    }

    /**
     * Reassign ticket to a specific user.
     */
    public function reassign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'nullable|string',
        ]);

        $newUser = User::findOrFail($validated['assigned_to']);
        $previousOwner = $ticket->assigned_to;

        $ticket->update([
            'assigned_to' => $newUser->id,
            'previous_owner' => $previousOwner,
        ]);

        // Create notification
        AppNotification::create([
            'user_id' => $newUser->id,
            'type' => 'ticket_reassigned',
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'sent_at' => now(),
            'data' => [
                'reason' => $validated['reason'] ?? 'Reassigned by admin',
                'from_user' => $previousOwner,
            ],
        ]);

        Log::info('ticket.reassigned', [
            'ticket_id' => $ticket->id,
            'from' => $previousOwner,
            'to' => $newUser->id,
        ]);

        return back()->with('success', 'Ticket reassigned successfully.');
    }

    /**
     * Get escalation history for a ticket (API).
     */
    public function history(Ticket $ticket)
    {
        $history = $ticket->escalationHistory()->with(['escalatedBy', 'assignedTo'])->get();
        return response()->json($history);
    }

    /**
     * Validate escalation rules based on user role and current level.
     */
    private function validateEscalationRule($user, string $fromLevel, string $toLevel): bool
    {
        // Admin can always escalate
        if ($user->hasRole('admin')) {
            return true;
        }

        // Support Manager can escalate to any level
        if ($user->hasRole('support') && $fromLevel === 'manager') {
            return true;
        }

        // N1 can escalate to N2 only
        if ($fromLevel === 'n1') {
            return $toLevel === 'n2';
        }

        // N2 can escalate to N3 or return to N1
        if ($fromLevel === 'n2') {
            return in_array($toLevel, ['n1', 'n3']);
        }

        // N3 can return to N2
        if ($fromLevel === 'n3') {
            return $toLevel === 'n2';
        }

        return false;
    }

    /**
     * Convert level string to number.
     */
    private function levelToNumber(?string $level): int
    {
        return match ($level) {
            'n1' => 1,
            'n2' => 2,
            'n3' => 3,
            'manager' => 4,
            default => 0,
        };
    }

    /**
     * Find available agent in a team.
     */
    private function findAvailableAgent(SupportTeam $team): ?User
    {
        $memberIds = $team->members()->pluck('users.id')->toArray();
        if (empty($memberIds)) return null;

        return User::whereIn('id', $memberIds)
            ->withCount(['tickets as open_tickets_count' => function ($q) {
                $q->whereNotIn('status', ['closed', 'resolved']);
            }])
            ->orderBy('open_tickets_count')
            ->first();
    }

    /**
     * Send notifications for manual escalation.
     */
    private function notifyEscalation(Ticket $ticket, SupportTeam $targetTeam, ?User $newAgent, string $reason): void
    {
        if ($newAgent) {
            // In-app notification
            AppNotification::create([
                'user_id' => $newAgent->id,
                'type' => 'ticket_escalated',
                'ticket_id' => $ticket->id,
                'status' => 'pending',
                'sent_at' => now(),
                'data' => [
                    'reason' => $reason,
                    'from_level' => $ticket->support_level,
                    'to_level' => $targetTeam->support_level,
                ],
            ]);

            // Email notification
            try {
                \Mail::raw(
                    "Ticket #{$ticket->id} has been escalated to you.\n\nTitle: {$ticket->title}\nPriority: {$ticket->priority}\nReason: {$reason}\n\nTarget Team: {$targetTeam->name}",
                    function ($message) use ($newAgent, $ticket) {
                        $message->to($newAgent->email)
                            ->subject("Ticket #{$ticket->id} Escalated - {$ticket->title}");
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('escalation.email.failed', ['error' => $e->getMessage()]);
            }
        }

        // Notify team leaders
        $leaders = $targetTeam->leaders()->get();
        foreach ($leaders as $leader) {
            if ($leader->id !== ($newAgent?->id)) {
                AppNotification::create([
                    'user_id' => $leader->id,
                    'type' => 'ticket_escalated',
                    'ticket_id' => $ticket->id,
                    'status' => 'pending',
                    'sent_at' => now(),
                    'data' => [
                        'reason' => $reason,
                        'team' => $targetTeam->name,
                    ],
                ]);
            }
        }
    }

    /**
     * Sync escalation to Jira.
     */
    private function syncEscalationToJira(Ticket $ticket, string $toLevel, string $reason, ?User $newAssignee): void
    {
        if (empty($ticket->jira_ticket_id)) return;

        try {
            $baseUrl = env('JIRA_URL');
            $auth = [env('JIRA_EMAIL'), env('JIRA_API_TOKEN')];

            // Add comment
            \Illuminate\Support\Facades\Http::withBasicAuth(...$auth)->post(
                "$baseUrl/rest/api/3/issue/{$ticket->jira_ticket_id}/comment",
                [
                    'body' => [
                        'type' => 'doc',
                        'version' => 1,
                        'content' => [
                            ['type' => 'paragraph', 'content' => [
                                ['type' => 'text', 'text' => "Manual escalation to " . strtoupper($toLevel) . ": $reason"]
                            ]]
                        ]
                    ]
                ]
            );

            // Update Jira priority based on new level
            $priorityMap = ['n1' => 'Low', 'n2' => 'Medium', 'n3' => 'High', 'manager' => 'Highest'];
            if (isset($priorityMap[$toLevel])) {
                \Illuminate\Support\Facades\Http::withBasicAuth(...$auth)->put(
                    "$baseUrl/rest/api/3/issue/{$ticket->jira_ticket_id}",
                    ['fields' => ['priority' => ['name' => $priorityMap[$toLevel]]]]
                );
            }

            Log::info('escalation.jira.synced', [
                'ticket_id' => $ticket->id,
                'jira_key' => $ticket->jira_ticket_id,
                'to_level' => $toLevel,
            ]);
        } catch (\Throwable $e) {
            Log::warning('escalation.jira.failed', ['error' => $e->getMessage()]);
        }
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $tickets = $this->buildExportQuery($request)->get();
        $filename = 'tickets_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = ['ID','Titre','Description','Utilisateur','Statut','Priorité','Catégorie','Urgent','Escaladé','Support Level','Jira','Source','Créé le','Résolu le'];
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
                    strtoupper($t->support_level ?? 'N1'),
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
        $tickets = $this->buildExportQuery($request)->get();
        $filename = 'tickets_'.now()->format('Ymd_His').'.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $columns = ['ID','Titre','Description','Utilisateur','Statut','Priorité','Catégorie','Urgent','Escaladé','Support Level','Jira','Source','Créé le','Résolu le'];
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
                    strtoupper($t->support_level ?? 'N1'),
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
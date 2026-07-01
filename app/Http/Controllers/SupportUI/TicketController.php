<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\User;
use App\Models\SupportTeam;
use App\Models\Notification as AppNotification;
use App\Services\JiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userLevel = $this->getUserSupportLevel($user);

        $query = Ticket::with(['user', 'assignedAgent'])
            ->whereNotNull('jira_ticket_id')
            ->where('jira_ticket_id', '!=', '');

        // Visibility rules by support level
        // N1 -> only N1
        // N2 -> N1 + N2
        // N3 -> N1 + N2 + N3
        // Manager -> all
        if ($userLevel && !$user->hasRole('admin')) {
            $allowedLevels = match ($userLevel) {
                'n1' => ['n1'],
                'n2' => ['n1', 'n2'],
                'n3' => ['n1', 'n2', 'n3'],
                default => ['n1', 'n2', 'n3', 'manager'],
            };
            $query->whereIn('support_level', $allowedLevels);
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('description','like', "%{$s}%");
            });
        }

        foreach (['status','priority','category','support_level'] as $f) {
            if ($request->filled($f)) $query->where($f, $request->input($f));
        }
        if ($request->filled('is_urgent')) $query->where('is_urgent', (bool) $request->input('is_urgent'));
        if ($request->filled('is_escalated')) $query->where('is_escalated', (bool) $request->input('is_escalated'));
        if ($request->filled('assigned_to_me')) $query->where('assigned_to', auth()->id());
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->input('date_from'));
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->input('date_to'));

        $allowedSorts = ['id','created_at','priority','status','category','jira_ticket_id','title'];
        $sort = $request->input('sort');
        $direction = $request->input('direction','desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $allowedSorts)) $query->orderBy($sort, $direction);
        else $query->orderBy('created_at', 'desc');

        $tickets = $query->paginate(20)->appends($request->query());

        $statuses = Ticket::select('status')->distinct()->pluck('status')->filter()->values();
        $priorities = Ticket::select('priority')->distinct()->pluck('priority')->filter()->values();
        $categories = Ticket::select('category')->distinct()->pluck('category')->filter()->values();
        $supportTeams = SupportTeam::where('is_active', true)->get();
        $levels = ['n1' => 'N1', 'n2' => 'N2', 'n3' => 'N3', 'manager' => 'Manager'];

        return view('support.tickets.index', compact('tickets','statuses','priorities','categories','sort','direction','supportTeams','levels','userLevel'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user','assignedAgent','conversation','message', 'escalationHistory.escalatedBy', 'escalationHistory.assignedTo']);
        $user = auth()->user();
        $userLevel = $this->getUserSupportLevel($user);
        $supportTeams = SupportTeam::where('is_active', true)->get();
        $supportUsers = User::role('support')->get();
        $escalationHistory = $ticket->escalationHistory()->with(['escalatedBy', 'assignedTo'])->get();
        $levels = ['n1' => 'N1', 'n2' => 'N2', 'n3' => 'N3', 'manager' => 'Support Manager'];

        return view('support.tickets.show', compact('ticket', 'supportTeams', 'supportUsers', 'escalationHistory', 'levels', 'userLevel'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|string']);
        $newStatus = $request->input('status');

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

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'resolved') $updateData['resolved_at'] = now();
        if ($newStatus === 'closed') $updateData['closed_at'] = now();
        $ticket->update($updateData);

        return back()->with('success', 'Status updated');
    }

    public function assignToMe(Request $request, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => auth()->id()]);
        return back()->with('success', 'Ticket assigned to you');
    }

    /**
     * Escalate ticket from support portal.
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

        // Validate escalation rules based on user level
        if (!$this->validateEscalationRule($user, $fromLevel, $toLevel)) {
            return back()->with('error', 'You do not have permission to perform this escalation.');
        }

        $targetTeam = SupportTeam::where('support_level', $toLevel)
            ->where('is_active', true)
            ->first();

        if (!$targetTeam) {
            return back()->with('error', 'Target support team not found or inactive.');
        }

        $newAssignee = null;
        if (!empty($validated['assigned_to'])) {
            $newAssignee = User::find($validated['assigned_to']);
        } else {
            $memberIds = $targetTeam->members()->pluck('users.id')->toArray();
            if (!empty($memberIds)) {
                $newAssignee = User::whereIn('id', $memberIds)
                    ->withCount(['tickets as open_count' => fn($q) => $q->whereNotIn('status', ['closed','resolved'])])
                    ->orderBy('open_count')
                    ->first();
            }
        }

        $previousOwner = $ticket->assigned_to;

        // Log in ticket_escalations
        TicketEscalation::create([
            'ticket_id' => $ticket->id,
            'jira_key' => $ticket->jira_ticket_id,
            'from_level' => $fromLevel,
            'to_level' => $toLevel,
            'previous_team' => $ticket->assigned_team,
            'current_team' => $targetTeam->slug,
            'escalated_by' => $user->id,
            'assigned_to' => $newAssignee?->id,
            'reason' => $validated['reason'],
            'escalation_level' => $this->levelToNumber($toLevel),
        ]);

        // Also log in EscalationLog for SLA
        \App\Models\EscalationLog::create([
            'ticket_id' => $ticket->id,
            'from_level' => $this->levelToNumber($fromLevel),
            'to_level' => $this->levelToNumber($toLevel),
            'from_user_id' => $previousOwner,
            'to_user_id' => $newAssignee?->id,
            'reason' => 'Support escalation: ' . $validated['reason'],
            'sla_breached' => false,
        ]);

        $ticket->update([
            'support_level' => $toLevel,
            'assigned_team' => $targetTeam->slug,
            'assigned_to' => $newAssignee?->id,
            'escalation_level' => $this->levelToNumber($toLevel),
            'is_escalated' => true,
            'escalated_at' => now(),
            'previous_owner' => $previousOwner,
            'escalation_reason' => $validated['reason'],
            'sla_deadline' => now()->addHours($targetTeam->getSlaHours($ticket->priority ?? 'medium')),
        ]);

        // Notifications
        $this->notifyEscalation($ticket, $targetTeam, $newAssignee, $validated['reason']);

        // Jira sync
        $this->syncToJira($ticket, $toLevel, $validated['reason']);

        Log::info('support.ticket.escalated', [
            'ticket_id' => $ticket->id,
            'from' => $fromLevel,
            'to' => $toLevel,
            'by' => $user->id,
        ]);

        return back()->with('success', 'Ticket escalated from ' . strtoupper($fromLevel) . ' to ' . strtoupper($toLevel));
    }

    /**
     * Reassign to a specific user from support portal.
     */
    public function reassign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $newUser = User::findOrFail($validated['assigned_to']);
        $previousOwner = $ticket->assigned_to;

        $ticket->update([
            'assigned_to' => $newUser->id,
            'previous_owner' => $previousOwner,
        ]);

        AppNotification::create([
            'user_id' => $newUser->id,
            'type' => 'ticket_reassigned',
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'sent_at' => now(),
            'data' => ['from_user' => $previousOwner],
        ]);

        return back()->with('success', 'Ticket reassigned successfully.');
    }

    // /**
    //  * Escalated tickets page for support portal.
    //  * DISABLED: Merged into single Tickets page
    //  */
    // public function escalatedIndex(Request $request)
    // {
    //     $user = auth()->user();
    //     $userLevel = $this->getUserSupportLevel($user);

    //     $query = TicketEscalation::with(['ticket.user', 'ticket.assignedAgent', 'escalatedBy', 'assignedTo'])->latest();

    //     // Visibility rules:
    //     // N1  -> to_level = 'n1' (escalations directed TO N1 from Manager or returned from N2)
    //     // N2  -> current_team = N2 team slug (escalations from N1 → N2)
    //     // N3  -> current_team = N3 team slug (escalations from N2 → N3)
    //     // Manager/Admin -> all escalations
    //     if ($userLevel && !$user->hasRole('admin')) {
    //         $userTeamSlug = \App\Models\SupportTeam::where('support_level', $userLevel)
    //             ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
    //             ->value('slug');

    //         if ($userLevel === 'n1') {
    //             // N1 sees escalations where to_level is N1 (from Manager or returned from N2)
    //             $query->where(function ($q) use ($userTeamSlug) {
    //                 $q->where('to_level', 'n1');
    //                 if ($userTeamSlug) {
    //                     $q->orWhere('current_team', $userTeamSlug);
    //                 }
    //             });
    //         } elseif ($userTeamSlug) {
    //             // N2, N3: filter by current team slug
    //             $query->where('current_team', $userTeamSlug);
    //         }
    //     }

    //     if ($request->filled('direction')) {
    //         $parts = explode('_to_', $request->input('direction'));
    //         if (count($parts) === 2) {
    //             $query->where('from_level', $parts[0])->where('to_level', $parts[1]);
    //         }
    //     }

    //     if ($request->filled('period')) {
    //         switch ($request->input('period')) {
    //             case 'today': $query->whereDate('created_at', today()); break;
    //             case 'week': $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]); break;
    //             case 'month': $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year); break;
    //         }
    //     }

    //     if ($request->filled('assigned_to_me')) {
    //         $query->where('assigned_to', auth()->id());
    //     }

    //     if ($request->filled('search')) {
    //         $s = $request->input('search');
    //         $query->whereHas('ticket', fn($q) => $q->where('title','like',"%{$s}%")->orWhere('id',$s));
    //     }

    //     $escalations = $query->paginate(20)->appends($request->query());

    //     $userTeamSlug = null;
    //     if ($userLevel && !$user->hasRole('admin')) {
    //         $userTeamSlug = \App\Models\SupportTeam::where('support_level', $userLevel)
    //             ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
    //             ->value('slug');
    //     }

    //     $stats = [
    //         'total' => TicketEscalation::count(),
    //         'incoming' => $userTeamSlug ? TicketEscalation::where('current_team', $userTeamSlug)->count() : 0,
    //         'outgoing' => TicketEscalation::where('from_level', $userLevel)->count(),
    //     ];

    //     $directions = ['n1_to_n2' => 'N1 → N2', 'n2_to_n3' => 'N2 → N3', 'n3_to_manager' => 'N3 → Manager'];

    //     return view('support.escalations.index', compact('escalations', 'stats', 'directions', 'userLevel'));
    // }

    private function getUserSupportLevel($user): ?string
    {
        if ($user->hasRole('admin')) return null;
        // Find the support level from their team membership
        $team = \App\Models\SupportTeam::whereHas('members', fn($q) => $q->where('user_id', $user->id))->first();
        return $team?->support_level;
    }

    private function validateEscalationRule($user, string $fromLevel, string $toLevel): bool
    {
        if ($user->hasRole('admin')) return true;

        // N1 can escalate to N2 only
        if ($fromLevel === 'n1') return $toLevel === 'n2';
        // N2 can escalate to N3 or return to N1
        if ($fromLevel === 'n2') return in_array($toLevel, ['n1', 'n3']);
        // N3 can return to N2
        if ($fromLevel === 'n3') return $toLevel === 'n2';
        return false;
    }

    private function levelToNumber(?string $level): int
    {
        return match ($level) { 'n1' => 1, 'n2' => 2, 'n3' => 3, 'manager' => 4, default => 0 };
    }

    private function notifyEscalation(Ticket $ticket, SupportTeam $targetTeam, ?User $newAgent, string $reason): void
    {
        if ($newAgent) {
            AppNotification::create([
                'user_id' => $newAgent->id,
                'type' => 'ticket_escalated',
                'ticket_id' => $ticket->id,
                'status' => 'pending',
                'sent_at' => now(),
                'data' => ['reason' => $reason, 'from_level' => $ticket->support_level, 'to_level' => $targetTeam->support_level],
            ]);
            try {
                \Mail::raw("Ticket #{$ticket->id} escalated to you.\n\nTitle: {$ticket->title}\nReason: {$reason}",
                    fn($m) => $m->to($newAgent->email)->subject("Ticket #{$ticket->id} Escalated"));
            } catch (\Throwable $e) { Log::warning('escalation.email.failed', ['error' => $e->getMessage()]); }
        }
        foreach ($targetTeam->leaders()->get() as $leader) {
            if ($leader->id !== ($newAgent?->id)) {
                AppNotification::create([
                    'user_id' => $leader->id, 'type' => 'ticket_escalated',
                    'ticket_id' => $ticket->id, 'status' => 'pending', 'sent_at' => now(),
                    'data' => ['reason' => $reason, 'team' => $targetTeam->name],
                ]);
            }
        }
    }

    private function syncToJira(Ticket $ticket, string $toLevel, string $reason): void
    {
        if (empty($ticket->jira_ticket_id)) return;
        try {
            $auth = [env('JIRA_EMAIL'), env('JIRA_API_TOKEN')];
            $base = env('JIRA_URL');
            \Illuminate\Support\Facades\Http::withBasicAuth(...$auth)->post(
                "$base/rest/api/3/issue/{$ticket->jira_ticket_id}/comment", [
                'body' => ['type' => 'doc', 'version' => 1, 'content' => [
                    ['type' => 'paragraph', 'content' => [
                        ['type' => 'text', 'text' => "Escalated from " . strtoupper($ticket->support_level) . " to " . strtoupper($toLevel) . ": $reason"]
                    ]]
                ]]
            ]);
            $pMap = ['n1'=>'Low','n2'=>'Medium','n3'=>'High','manager'=>'Highest'];
            if (isset($pMap[$toLevel])) {
                \Illuminate\Support\Facades\Http::withBasicAuth(...$auth)->put(
                    "$base/rest/api/3/issue/{$ticket->jira_ticket_id}",
                    ['fields' => ['priority' => ['name' => $pMap[$toLevel]]]]
                );
            }
        } catch (\Throwable $e) { Log::warning('escalation.jira.failed', ['error' => $e->getMessage()]); }
    }
}
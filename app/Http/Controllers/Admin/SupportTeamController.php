<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTeam;
use App\Models\User;
use App\Services\EscalationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupportTeamController extends Controller
{
    protected EscalationService $escalationService;

    public function __construct(EscalationService $escalationService)
    {
        $this->escalationService = $escalationService;
    }

    /**
     * Display a listing of support teams.
     */
    public function index()
    {
        $teams = SupportTeam::withCount('members')->get();
        return view('admin.support-teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        $levels = ['n1' => 'N1 - First Line', 'n2' => 'N2 - Advanced', 'n3' => 'N3 - Expert', 'manager' => 'Support Manager'];
        return view('admin.support-teams.create', compact('levels'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'support_level' => 'required|in:n1,n2,n3,manager',
            'is_active' => 'sometimes|boolean',
            'sla_hours_low' => 'required|integer|min:1',
            'sla_hours_medium' => 'required|integer|min:1',
            'sla_hours_high' => 'required|integer|min:1',
            'sla_minutes_critical' => 'required|integer|min:1',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        SupportTeam::create($validated);

        Log::info('support_team.created', [
            'name' => $validated['name'],
            'level' => $validated['support_level'],
        ]);

        return redirect()->route('admin.support-teams.index')
            ->with('success', 'Support team created successfully.');
    }

    /**
     * Show the form for editing a team.
     */
    public function edit(SupportTeam $supportTeam)
    {
        $levels = ['n1' => 'N1 - First Line', 'n2' => 'N2 - Advanced', 'n3' => 'N3 - Expert', 'manager' => 'Support Manager'];
        $allUsers = User::with('roles')->get();
        $teamMemberIds = $supportTeam->members()->pluck('users.id')->toArray();
        $teamLeaderIds = $supportTeam->leaders()->pluck('users.id')->toArray();

        return view('admin.support-teams.edit', compact(
            'supportTeam', 'levels', 'allUsers', 'teamMemberIds', 'teamLeaderIds'
        ));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, SupportTeam $supportTeam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'support_level' => 'required|in:n1,n2,n3,manager',
            'is_active' => 'sometimes|boolean',
            'sla_hours_low' => 'required|integer|min:1',
            'sla_hours_medium' => 'required|integer|min:1',
            'sla_hours_high' => 'required|integer|min:1',
            'sla_minutes_critical' => 'required|integer|min:1',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'leaders' => 'nullable|array',
            'leaders.*' => 'exists:users,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $supportTeam->update($validated);

        // Sync members — ensure a user belongs to ONLY ONE support team
        if ($request->has('members')) {
            $syncData = [];
            $transferredUsers = [];

            foreach ($request->members as $userId) {
                $isLeader = $request->has('leaders') && in_array($userId, $request->leaders);
                $syncData[$userId] = ['is_leader' => $isLeader];

                // Check if user already belongs to another team
                $otherTeam = SupportTeam::whereHas('members', fn($q) => $q->where('user_id', $userId))
                    ->where('id', '!=', $supportTeam->id)
                    ->first();

                if ($otherTeam) {
                    // Detach from the other team
                    $otherTeam->members()->detach($userId);
                    $transferredUsers[] = [
                        'user' => User::find($userId),
                        'from_team' => $otherTeam,
                    ];
                }
            }

            $supportTeam->members()->sync($syncData);

            // Log transfers
            foreach ($transferredUsers as $transfer) {
                Log::info('support_team.member.transferred', [
                    'user_id' => $transfer['user']->id,
                    'user_name' => $transfer['user']->name,
                    'from_team' => $transfer['from_team']->name,
                    'from_level' => $transfer['from_team']->support_level,
                    'to_team' => $supportTeam->name,
                    'to_level' => $supportTeam->support_level,
                ]);
            }

            // Flash success message with transfer info
            if (!empty($transferredUsers)) {
                $names = collect($transferredUsers)->pluck('user.name')->implode(', ');
                $levelLabels = ['n1' => 'N1', 'n2' => 'N2', 'n3' => 'N3', 'manager' => 'Manager'];
                $fromName = $levelLabels[$transferredUsers[0]['from_team']->support_level] ?? $transferredUsers[0]['from_team']->name;
                $toName = $levelLabels[$supportTeam->support_level] ?? $supportTeam->name;

                session()->flash('info', "{$names} appartenait à l'équipe {$fromName}. Transfert automatique vers {$toName} effectué.");
            }
        }

        Log::info('support_team.updated', [
            'id' => $supportTeam->id,
            'name' => $supportTeam->name,
        ]);

        return redirect()->route('admin.support-teams.index')
            ->with('success', 'Support team updated successfully.');
    }

    /**
     * Remove the specified team.
     */
    public function destroy(SupportTeam $supportTeam)
    {
        $supportTeam->members()->detach();
        $supportTeam->delete();

        Log::info('support_team.deleted', [
            'id' => $supportTeam->id,
            'name' => $supportTeam->name,
        ]);

        return redirect()->route('admin.support-teams.index')
            ->with('success', 'Support team deleted successfully.');
    }

    /**
     * Show escalation reporting dashboard.
     */
    public function reporting(EscalationService $escalationService)
    {
        $slaStats = $escalationService->getSlaStats();
        $teamPerformance = $escalationService->getTeamPerformance();

        // Recent escalation logs
        $recentEscalations = \App\Models\EscalationLog::with(['ticket', 'fromUser', 'toUser'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.support-teams.reporting', compact(
            'slaStats', 'teamPerformance', 'recentEscalations'
        ));
    }

    /**
     * API: Get SLA stats (JSON).
     */
    public function apiSlaStats()
    {
        return response()->json($this->escalationService->getSlaStats());
    }

    /**
     * API: Get team performance (JSON).
     */
    public function apiTeamPerformance()
    {
        return response()->json($this->escalationService->getTeamPerformance());
    }

    /**
     * API: Get escalation logs (JSON).
     */
    public function apiEscalationLogs()
    {
        $logs = \App\Models\EscalationLog::with(['ticket', 'fromUser', 'toUser'])
            ->latest()
            ->paginate(50);

        return response()->json($logs);
    }

    /**
     * API: Get teams list (JSON).
     */
    public function apiTeams()
    {
        $teams = SupportTeam::withCount('members')->get();
        return response()->json($teams);
    }
}
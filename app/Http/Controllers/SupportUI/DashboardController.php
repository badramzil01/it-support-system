<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userLevel = $this->getUserSupportLevel($user);

        // Base query scoped by support level (N1 sees N1, N2 sees N1+N2, N3 sees all, Manager sees all)
        $baseQuery = Ticket::query();
        if ($userLevel && !$user->hasRole('admin')) {
            $allowedLevels = match ($userLevel) {
                'n1' => ['n1'],
                'n2' => ['n1', 'n2'],
                'n3' => ['n1', 'n2', 'n3'],
                default => ['n1', 'n2', 'n3', 'manager'],
            };
            $baseQuery->whereIn('support_level', $allowedLevels);
        }

        // Level-aware dashboard stats
        $myTickets = (clone $baseQuery)->whereIn('status', ['open', 'in_progress', 'pending'])->count();
        
        $escalatedToN2 = 0;
        $receivedFromN1 = 0;
        $escalatedToN3 = 0;
        $receivedFromN2 = 0;
        $criticalTickets = 0;

        if ($userLevel === 'n1') {
            $escalatedToN2 = (clone $baseQuery)->where('is_escalated', true)->where('support_level', 'n2')->count();
        } elseif ($userLevel === 'n2') {
            $receivedFromN1 = (clone $baseQuery)->where('support_level', 'n1')->where('is_escalated', true)->count();
            $escalatedToN3 = (clone $baseQuery)->where('is_escalated', true)->where('support_level', 'n3')->count();
        } elseif ($userLevel === 'n3') {
            $receivedFromN2 = (clone $baseQuery)->where('support_level', 'n2')->where('is_escalated', true)->count();
            $criticalTickets = (clone $baseQuery)->where('is_urgent', true)->count();
        }

        // Global counts (for context)
        $total = Ticket::count();
        $open = Ticket::where('status', 'open')->count();
        $inProgress = Ticket::where('status', 'pending')->count();
        $resolved = Ticket::where('status', 'resolved')->count();
        $closed = Ticket::where('status', 'closed')->count();
        $urgent = Ticket::where('is_urgent', true)->count();
        $escalated = Ticket::where('is_escalated', true)->count();
        $jira = Ticket::whereNotNull('jira_ticket_id')->count();

        // Users active (with at least one conversation)
        $activeUsers = User::whereIn('id', function ($q) {
            $q->select('user_id')->from('conversations')->distinct();
        })->count();

        $conversations = Conversation::count();

        // Latest and flagged tickets scoped to user's level
        $latestTickets = (clone $baseQuery)->orderBy('created_at', 'desc')->take(10)->get();
        $urgentTickets = (clone $baseQuery)->where('is_urgent', true)->orderBy('created_at', 'desc')->take(8)->get();

        // Chart data: status, priority, category (scoped)
        $statusCounts = (clone $baseQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total','status')->toArray();
        $priorityCounts = (clone $baseQuery)->select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total','priority')->toArray();
        $categoryCounts = (clone $baseQuery)->select('category', DB::raw('count(*) as total'))->groupBy('category')->pluck('total','category')->toArray();

        // Prepare labels/data arrays for JS
        $statusLabels = array_keys($statusCounts);
        $statusData = array_values($statusCounts);

        $priorityLabels = array_keys($priorityCounts);
        $priorityData = array_values($priorityCounts);

        $categoryLabels = array_keys($categoryCounts);
        $categoryData = array_values($categoryCounts);

        return view('support.dashboard', compact(
            'total','open','inProgress','resolved','closed','urgent','escalated','jira','conversations','activeUsers',
            'latestTickets','urgentTickets',
            'statusLabels','statusData','priorityLabels','priorityData','categoryLabels','categoryData',
            'userLevel','myTickets','escalatedToN2','receivedFromN1','escalatedToN3','receivedFromN2','criticalTickets'
        ));
    }

    private function getUserSupportLevel($user): ?string
    {
        if ($user->hasRole('admin')) return null;
        $team = \App\Models\SupportTeam::whereHas('members', fn($q) => $q->where('user_id', $user->id))->first();
        return $team?->support_level;
    }
}

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
        // Basic counts
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

        // Latest and flagged tickets
        $latestTickets = Ticket::orderBy('created_at', 'desc')->take(10)->get();
        $urgentTickets = Ticket::where('is_urgent', true)->orderBy('created_at', 'desc')->take(8)->get();

        // Chart data: status, priority, category
        $statusCounts = Ticket::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total','status')->toArray();
        $priorityCounts = Ticket::select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total','priority')->toArray();
        $categoryCounts = Ticket::select('category', DB::raw('count(*) as total'))->groupBy('category')->pluck('total','category')->toArray();

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
            'statusLabels','statusData','priorityLabels','priorityData','categoryLabels','categoryData'
        ));
    }
}

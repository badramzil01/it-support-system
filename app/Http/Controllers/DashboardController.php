<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // ── Ticket stats ──
        $tickets = Ticket::where('user_id', $userId);

        $stats = [
            'open'       => (clone $tickets)->where('status', 'open')->count(),
            'in_progress'=> (clone $tickets)->where('status', 'in_progress')->count(),
            'resolved'   => (clone $tickets)->where('status', 'resolved')->count(),
            'closed'     => (clone $tickets)->where('status', 'closed')->count(),
            'escalated'  => (clone $tickets)->where('is_escalated', true)->count(),
            'total'      => (clone $tickets)->count(),
        ];

        // ── Recent tickets ──
        $recentTickets = Ticket::where('user_id', $userId)
            ->with('assignedAgent')
            ->latest()
            ->take(10)
            ->get();

        // ── Recent conversations ──
        $recentConversations = Conversation::where('user_id', $userId)
            ->with('latestMessage')
            ->latest()
            ->take(5)
            ->get();

        // ── Ticket creation by month (current year) ──
        $monthlyStats = Ticket::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->select(DB::raw("COUNT(*) as count"), DB::raw("MONTH(created_at) as month"))
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = $monthlyStats[$m] ?? 0;
        }

        // ── Latest activity ──
        $latestActivity = Ticket::where('user_id', $userId)
            ->latest('updated_at')
            ->take(5)
            ->get(['id', 'title', 'status', 'updated_at']);

        // ── AI response count ──
        $aiResponseCount = Message::whereHas('conversation', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('sender', 'bot')->count();

        // ── Knowledge base suggestions ──
        $suggestions = KnowledgeBase::latest()
            ->take(6)
            ->get(['id', 'problem_keywords', 'category']);

        return view('dashboard', compact(
            'stats',
            'recentTickets',
            'recentConversations',
            'monthlyData',
            'latestActivity',
            'aiResponseCount',
            'suggestions'
        ));
    }
}
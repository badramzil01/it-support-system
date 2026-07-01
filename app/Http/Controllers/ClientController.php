<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Dashboard client
     */
    public function dashboard()
    {
        $user = auth()->user();
        $userId = $user->id;

        // ── Ticket stats ──
        $tickets = Ticket::where('user_id', $userId);

        $stats = [
            'open'        => (clone $tickets)->where('status', 'open')->count(),
            'in_progress' => (clone $tickets)->where('status', 'in_progress')->count(),
            'resolved'    => (clone $tickets)->where('status', 'resolved')->count(),
            'closed'      => (clone $tickets)->where('status', 'closed')->count(),
            'escalated'   => (clone $tickets)->where('is_escalated', true)->count(),
            'total'       => (clone $tickets)->count(),
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

        return view('client.dashboard', compact(
            'stats',
            'recentTickets',
            'recentConversations',
            'monthlyData',
            'latestActivity',
            'aiResponseCount',
            'suggestions'
        ));
    }

    /**
     * Page Profil
     */
    public function profile()
    {
        return view('client.profile');
    }

    /**
     * Mise à jour du profil
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('client.profile')
            ->with('status', 'Profil mis à jour avec succès.');
    }

    /**
     * Mise à jour du mot de passe
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('client.profile')
            ->with('status', 'Mot de passe modifié avec succès.');
    }

    /**
     * Upload photo de profil
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete old photo
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        return redirect()->route('client.profile')
            ->with('status', 'Photo de profil mise à jour.');
    }

    /**
     * Toggle dark mode
     */
    public function toggleDarkMode()
    {
        $user = auth()->user();
        $user->update(['dark_mode' => !$user->dark_mode]);

        return response()->json(['dark_mode' => $user->dark_mode]);
    }
}
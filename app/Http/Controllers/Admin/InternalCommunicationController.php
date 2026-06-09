<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternalMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class InternalCommunicationController extends Controller
{
    /**
     * Display the internal communication page
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Get all support agents (users with support role)
        $teamMembers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'support'))
            ->withCount([
                'receivedInternalMessages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $userId)->where('is_read', false),
            ])
            ->with('sentInternalMessages')
            ->orderBy('name')
            ->get();

        $selectedUserId = $request->integer('with');
        $selectedUser = null;
        $messages = collect();
        $tickets = collect();
        $stats = ['sent' => 0, 'received' => 0, 'unread' => 0];

        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);

            if ($selectedUser) {
                // Messages between current user and selected user
                $messages = InternalMessage::query()
                    ->between($userId, $selectedUserId)
                    ->with(['sender', 'receiver', 'ticket'])
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mark messages as read
                InternalMessage::query()
                    ->where('sender_id', $selectedUserId)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);

                // Tickets related to this conversation
                $ticketIds = $messages->whereNotNull('ticket_id')->pluck('ticket_id')->unique();
                $tickets = Ticket::whereIn('id', $ticketIds)
                    ->with(['user', 'assignedAgent', 'message'])
                    ->latest()
                    ->get();

                // Stats
                $stats = [
                    'sent'     => $messages->where('sender_id', $userId)->count(),
                    'received' => $messages->where('sender_id', $selectedUserId)->count(),
                    'unread'   => $messages->where('sender_id', $selectedUserId)->where('is_read', false)->count(),
                ];
            }
        }

        return view('admin.internal-communication.index', compact(
            'teamMembers', 'selectedUser', 'messages', 'tickets', 'stats', 'userId'
        ));
    }

    /**
     * Send an internal message (Admin → Support)
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required|string|max:5000',
            'type'        => 'nullable|string|in:message,request,info_request,follow_up',
            'ticket_id'   => 'nullable|exists:tickets,id',
        ]);

        try {
            $message = InternalMessage::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $validated['receiver_id'],
                'content'     => $validated['content'],
                'type'        => $validated['type'] ?? 'message',
                'ticket_id'   => $validated['ticket_id'] ?? null,
            ]);

            // Create notification record
            if (Schema::hasTable('notifications')) {
                try {
                    \App\Models\Notification::create([
                        'type'      => 'internal_message',
                        'status'    => 'pending',
                        'sent_at'   => now(),
                        'ticket_id' => $validated['ticket_id'] ?? null,
                    ]);
                } catch (\Throwable $e) {}
            }

            return back()->with('success', 'Message envoyé.');
        } catch (\Throwable $e) {
            Log::error('internal_message.send_error', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Erreur lors de l\'envoi du message.');
        }
    }

    /**
     * API: Get messages between two users (for AJAX polling)
     */
    public function apiMessages(Request $request, int $userId)
    {
        $currentUserId = auth()->id();
        $lastId = $request->integer('last_id', 0);

        $messages = InternalMessage::query()
            ->between($currentUserId, $userId)
            ->with(['sender', 'receiver', 'ticket'])
            ->when($lastId > 0, fn ($q) => $q->where('id', '>', $lastId))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * API: Get team members with unread counts
     */
    public function apiTeam()
    {
        $userId = auth()->id();

        $teamMembers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'support'))
            ->withCount([
                'receivedInternalMessages as unread_count' => fn ($q) => $q->where('sender_id', '!=', $userId)->where('is_read', false),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'unread_count' => $user->unread_count,
            ]);

        return response()->json([
            'success'     => true,
            'team_members' => $teamMembers,
            'total_unread' => $teamMembers->sum('unread_count'),
        ]);
    }

    /**
     * API: Mark all messages as read
     */
    public function apiMarkRead(int $userId)
    {
        InternalMessage::query()
            ->where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * API: Get unread count for navbar
     */
    public function apiUnreadCount()
    {
        $count = InternalMessage::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['success' => true, 'unread' => $count]);
    }
}
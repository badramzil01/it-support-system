<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $messagesHasReadAt = Schema::hasColumn('messages', 'read_at');
        $conversationsHasLastRead = Schema::hasColumn('conversations', 'last_read_by_support_at');
        $ticketsHasTriggerMessage = Schema::hasColumn('tickets', 'trigger_message_id');
        $ticketsHasIsUrgent = Schema::hasColumn('tickets', 'is_urgent');

        $userCountRelations = [
            'conversations',
            'tickets',
            'tickets as escalated_tickets_count' => fn ($query) => $query->where('is_escalated', true),
        ];

        if ($ticketsHasIsUrgent) {
            $userCountRelations['tickets as urgent_tickets_count'] = fn ($query) => $query->where('is_urgent', true);
        }

        if ($messagesHasReadAt) {
            $userCountRelations['messages as unread_messages_count'] = fn ($query) => $query->where('sender', 'user')->whereNull('read_at');
        }

        $users = User::query()
            ->whereHas('conversations')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with('latestMessage')
            ->withCount($userCountRelations)
            ->orderByDesc(Message::select('created_at')->whereColumn('messages.user_id', 'users.id')->latest()->limit(1))
            ->orderBy('name')
            ->get()
            ->each(function ($user) use ($ticketsHasIsUrgent, $messagesHasReadAt) {
                if (!$ticketsHasIsUrgent) $user->urgent_tickets_count = 0;
                if (!$messagesHasReadAt) $user->unread_messages_count = 0;
            });

        $selectedUser = null;
        $conversationsList = collect();
        $selectedConversation = null;
        $messages = collect();
        $tickets = collect();
        $customerStats = ['conversations' => 0, 'tickets' => 0, 'urgent' => 0, 'escalated' => 0];
        $lastTicket = null;
        $triggerTicket = null;
        $triggerMessage = null;

        $selectedUserId = $request->integer('user_id') ?: optional($users->first())->id;

        if ($selectedUserId) {
            $selectedUser = User::query()->withCount($userCountRelations)->find($selectedUserId);
            if ($selectedUser && !$ticketsHasIsUrgent) $selectedUser->urgent_tickets_count = 0;
        }

        if ($selectedUser) {
            $conversationsList = Conversation::query()
                ->where('user_id', $selectedUser->id)
                ->with(['latestMessage', 'tickets'])
                ->withCount('messages')
                ->orderByDesc(Message::select('created_at')->whereColumn('messages.conversation_id', 'conversations.id')->latest()->limit(1))
                ->orderByDesc('updated_at')
                ->get();

            $conversationId = $request->integer('conversation_id') ?: optional($conversationsList->first())->id;
            if ($conversationId) {
                $selectedConversation = Conversation::query()
                    ->where('user_id', $selectedUser->id)
                    ->with(['tickets.message', 'tickets.triggerMessage'])
                    ->find($conversationId);
            }

            $tickets = Ticket::query()
                ->where('user_id', $selectedUser->id)
                ->with(['message', 'assignedAgent', 'triggerMessage'])
                ->latest()->get();

            $lastTicket = $tickets->first();
            $customerStats = [
                'conversations' => (int) $selectedUser->conversations_count,
                'tickets' => (int) $selectedUser->tickets_count,
                'urgent' => (int) $selectedUser->urgent_tickets_count,
                'escalated' => (int) $selectedUser->escalated_tickets_count,
            ];
        }

        if ($selectedConversation) {
            if ($messagesHasReadAt) {
                Message::query()->where('conversation_id', $selectedConversation->id)
                    ->where('sender', 'user')->whereNull('read_at')->update(['read_at' => now()]);
            }
            if ($conversationsHasLastRead) $selectedConversation->update(['last_read_by_support_at' => now()]);

            $messages = Message::query()->where('conversation_id', $selectedConversation->id)
                ->with(['ticket', 'triggeredTicket'])->oldest()->get();
            $tickets = Ticket::query()->where('conversation_id', $selectedConversation->id)
                ->with(['message', 'assignedAgent', 'triggerMessage'])->latest()->get();
            $triggerTicket = $tickets->first(fn ($ticket) => $ticket->is_escalated || $ticket->is_urgent || $ticket->message_id);
            $triggerMessage = $triggerTicket?->triggerMessage ?: $triggerTicket?->message;
        }

        return view('admin.conversations.index', compact(
            'users','search','selectedUser','conversationsList','selectedConversation',
            'messages','tickets','customerStats','lastTicket','triggerTicket','triggerMessage','ticketsHasTriggerMessage'
        ));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:10000',
        ]);

        $conversation = Conversation::where('user_id', $validated['user_id'])->findOrFail($validated['conversation_id']);

        $messageData = [
            'user_id' => $validated['user_id'],
            'conversation_id' => $conversation->id,
            'sender' => 'support',
            'content' => $validated['content'],
            'response' => null,
            'channel' => 'web',
            'source' => 'admin',
            'status' => 'sent',
            'user_message' => null,
        ];
        if (Schema::hasColumn('messages', 'support_agent_id')) $messageData['support_agent_id'] = auth()->id();

        $as = $request->input('_as');
        if ($as === 'ai') {
            $messageData['sender'] = 'ai';
            $messageData['source'] = 'ai_admin';
        }

        $message = Message::create($messageData);
        $conversation->touch();

        return back()->with('success', 'Message envoyé.');
    }

    public function createTicket(Request $request)
    {
        $validated = $request->validate(['conversation_id' => 'required|exists:conversations,id']);
        $conversation = Conversation::with('user')->findOrFail($validated['conversation_id']);
        $message = $conversation->messages()->where('sender', 'user')->latest()->first()
            ?: $conversation->messages()->latest()->first();

        $ticketData = [
            'user_id' => $conversation->user_id,
            'conversation_id' => $conversation->id,
            'message_id' => $message?->id,
            'assigned_to' => auth()->id(),
            'title' => Str::limit($conversation->title ?: 'Ticket admin depuis conversation', 120, ''),
            'description' => $message?->content ?: $conversation->current_problem ?: 'Ticket créé par admin.',
            'solution' => null,
            'source' => 'admin',
            'status' => 'open',
            'priority' => 'medium',
            'category' => $conversation->current_category ?: 'general',
            'confidence' => 0,
            'feedback' => 'pending',
            'is_urgent' => false,
            'is_escalated' => false,
        ];
        if (Schema::hasColumn('tickets', 'trigger_message_id')) $ticketData['trigger_message_id'] = $message?->id;
        if (Schema::hasColumn('tickets', 'has_image')) $ticketData['has_image'] = (bool) $message?->image_path;

        $ticket = Ticket::create($ticketData);

        try {
            \App\Models\Notification::create([
                'ticket_id' => $ticket->id, 'type' => 'ticket_created_admin', 'status' => 'pending', 'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {}

        $conversation->update([
            'ticket_created' => true,
            'current_ticket_id' => $ticket->jira_ticket_id ?: (string) $ticket->id,
        ]);

        return back()->with('success', 'Ticket créé depuis la conversation.');
    }

    public function escalate(Request $request, Ticket $ticket)
    {
        $data = [
            'is_escalated' => true,
            'priority' => in_array($ticket->priority, ['critical','high'], true) ? $ticket->priority : 'high',
            'status' => $ticket->status === 'closed' ? 'open' : $ticket->status,
            'assigned_to' => $ticket->assigned_to ?: auth()->id(),
        ];
        if (Schema::hasColumn('tickets', 'is_urgent')) $data['is_urgent'] = true;
        if (Schema::hasColumn('tickets', 'trigger_message_id')) $data['trigger_message_id'] = $ticket->trigger_message_id ?: $ticket->message_id;

        $ticket->update($data);

        try {
            \App\Models\Notification::create(['ticket_id' => $ticket->id, 'type' => 'ticket_escalated_admin', 'status' => 'pending', 'sent_at' => now()]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Ticket escaladé.');
    }
}

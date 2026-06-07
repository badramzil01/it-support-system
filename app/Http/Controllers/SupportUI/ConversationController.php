<?php

namespace App\Http\Controllers\SupportUI;

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
    public function show(Conversation $conversation)
    {
        $selectedUser = $conversation->user;

        // users list for sidebar (simple)
        $users = User::withCount('conversations')->orderBy('name')->get();
        $users->transform(function($u){
            $u->last_message = Message::where('user_id', $u->id)->latest()->first();
            $u->messages_count = Message::where('user_id', $u->id)->count();
            return $u;
        });

        $conversationsList = Conversation::where('user_id', $selectedUser->id)
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        $messages = Message::where('conversation_id', $conversation->id)->oldest()->get();
        $tickets = Ticket::where('conversation_id', $conversation->id)->latest()->get();

        return view('support.conversations.show', compact('users','selectedUser','conversationsList','conversation','messages','tickets'));
    }

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
            $userCountRelations['messages as unread_messages_count'] = fn ($query) => $query
                ->where('sender', 'user')
                ->whereNull('read_at');
        }

        $users = User::query()
            ->whereHas('conversations')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with('latestMessage')
            ->withCount($userCountRelations)
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('messages.user_id', 'users.id')
                    ->latest()
                    ->limit(1)
            )
            ->orderBy('name')
            ->get()
            ->each(function ($user) use ($ticketsHasIsUrgent, $messagesHasReadAt) {
                if (!$ticketsHasIsUrgent) {
                    $user->urgent_tickets_count = 0;
                }

                if (!$messagesHasReadAt) {
                    $user->unread_messages_count = 0;
                }
            });

        $selectedUser = null;
        $conversationsList = collect();
        $selectedConversation = null;
        $messages = collect();
        $tickets = collect();
        $customerStats = [
            'conversations' => 0,
            'tickets' => 0,
            'urgent' => 0,
            'escalated' => 0,
        ];
        $lastTicket = null;
        $triggerTicket = null;
        $triggerMessage = null;

        $selectedUserId = $request->integer('user_id') ?: optional($users->first())->id;

        if ($selectedUserId) {
            $selectedUser = User::query()
                ->withCount($userCountRelations)
                ->find($selectedUserId);

            if ($selectedUser && !$ticketsHasIsUrgent) {
                $selectedUser->urgent_tickets_count = 0;
            }
        }

        if ($selectedUser) {
            $conversationsList = Conversation::query()
                ->where('user_id', $selectedUser->id)
                ->with(['latestMessage', 'tickets'])
                ->withCount('messages')
                ->orderByDesc(
                    Message::select('created_at')
                        ->whereColumn('messages.conversation_id', 'conversations.id')
                        ->latest()
                        ->limit(1)
                )
                ->orderByDesc('updated_at')
                ->get();

            $conversationId = $request->integer('conversation_id') ?: optional($conversationsList->first())->id;

            if ($conversationId) {
                $conversationRelations = ['tickets.message'];

                if ($ticketsHasTriggerMessage) {
                    $conversationRelations[] = 'tickets.triggerMessage';
                }

                $selectedConversation = Conversation::query()
                    ->where('user_id', $selectedUser->id)
                    ->with($conversationRelations)
                    ->find($conversationId);
            }

            $ticketRelations = ['message', 'assignedAgent'];

            if ($ticketsHasTriggerMessage) {
                $ticketRelations[] = 'triggerMessage';
            }

            $tickets = Ticket::query()
                ->where('user_id', $selectedUser->id)
                ->with($ticketRelations)
                ->latest()
                ->get();

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
                Message::query()
                    ->where('conversation_id', $selectedConversation->id)
                    ->where('sender', 'user')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }

            if ($conversationsHasLastRead) {
                $selectedConversation->update(['last_read_by_support_at' => now()]);
            }

            $messageRelations = ['ticket'];

            if ($ticketsHasTriggerMessage) {
                $messageRelations[] = 'triggeredTicket';
            }

            $messages = Message::query()
                ->where('conversation_id', $selectedConversation->id)
                ->with($messageRelations)
                ->oldest()
                ->get();

            $ticketRelations = ['message', 'assignedAgent'];

            if ($ticketsHasTriggerMessage) {
                $ticketRelations[] = 'triggerMessage';
            }

            $tickets = Ticket::query()
                ->where('conversation_id', $selectedConversation->id)
                ->with($ticketRelations)
                ->latest()
                ->get();

            $triggerTicket = $tickets
                ->first(fn ($ticket) => $ticket->is_escalated || $ticket->is_urgent || $ticket->message_id);
            $triggerMessage = $ticketsHasTriggerMessage
                ? ($triggerTicket?->triggerMessage ?: $triggerTicket?->message)
                : $triggerTicket?->message;
        }

        return view('support.conversations.index', compact(
            'users',
            'search',
            'selectedUser',
            'conversationsList',
            'selectedConversation',
            'messages',
            'tickets',
            'customerStats',
            'lastTicket',
            'triggerTicket',
            'triggerMessage',
            'ticketsHasTriggerMessage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:10000',
        ]);

        $conversation = Conversation::query()
            ->where('user_id', $validated['user_id'])
            ->findOrFail($validated['conversation_id']);

        $messageData = [
            'user_id' => $validated['user_id'],
            'conversation_id' => $conversation->id,
            'sender' => 'support',
            'content' => $validated['content'],
            'response' => null,
            'channel' => 'web',
            'source' => 'support',
            'status' => 'sent',
            'user_message' => null,
        ];

        if (Schema::hasColumn('messages', 'support_agent_id')) {
            $messageData['support_agent_id'] = auth()->id();
        }

        // allow support to send as AI when UI posts _as=ai
        $as = $request->input('_as');
        if ($as === 'ai') {
            $messageData['sender'] = 'ai';
            $messageData['source'] = 'ai_support';
        }

        $message = Message::create($messageData);

        $conversation->touch();

        Log::info('support.reply.created', [
            'user_id' => $validated['user_id'],
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'agent_id' => auth()->id(),
        ]);

        return redirect()
            ->route('support.ui.conversations.index', [
                'user_id' => $validated['user_id'],
                'conversation_id' => $conversation->id,
            ])
            ->with('success', 'Message support envoye.');
    }

    public function createTicket(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        $conversation = Conversation::with('user')->findOrFail($validated['conversation_id']);
        $message = $conversation->messages()->where('sender', 'user')->latest()->first()
            ?: $conversation->messages()->latest()->first();

        $ticketData = [
            'user_id' => $conversation->user_id,
            'conversation_id' => $conversation->id,
            'message_id' => $message?->id,
            'assigned_to' => auth()->id(),
            'title' => Str::limit($conversation->title ?: 'Ticket depuis conversation', 120, ''),
            'description' => $message?->content ?: $conversation->current_problem ?: 'Ticket cree depuis la console support.',
            'solution' => null,
            'source' => 'support',
            'status' => 'open',
            'priority' => 'medium',
            'category' => $conversation->current_category ?: 'general',
            'confidence' => 0,
            'feedback' => 'pending',
            'is_urgent' => false,
            'is_escalated' => false,
            'has_image' => (bool) $message?->image_path,
        ];

        if (Schema::hasColumn('tickets', 'trigger_message_id')) {
            $ticketData['trigger_message_id'] = $message?->id;
        }

        if (!Schema::hasColumn('tickets', 'is_urgent')) {
            unset($ticketData['is_urgent']);
        }

        if (!Schema::hasColumn('tickets', 'has_image')) {
            unset($ticketData['has_image']);
        }

        $ticket = Ticket::create($ticketData);

        // Create a notification entry so the support UI can display new tickets
        try {
            \App\Models\Notification::create([
                'ticket_id' => $ticket->id,
                'type' => 'ticket_created',
                'status' => 'pending',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // If notifications table/migration is missing, don't block ticket creation
            Log::warning('notification.create.failed', ['err' => $e->getMessage()]);
        }

        $conversation->update([
            'ticket_created' => true,
            'current_ticket_id' => $ticket->jira_ticket_id ?: (string) $ticket->id,
        ]);

        return back()->with('success', 'Ticket cree depuis la conversation.');
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        $ticket->update(['assigned_to' => auth()->id()]);

        return back()->with('success', 'Ticket assigne a votre compte.');
    }

    public function escalateTicket(Request $request, Ticket $ticket)
    {
        $data = [
            'is_escalated' => true,
            'priority' => in_array($ticket->priority, ['critical', 'high'], true) ? $ticket->priority : 'high',
            'status' => $ticket->status === 'closed' ? 'open' : $ticket->status,
            'assigned_to' => $ticket->assigned_to ?: auth()->id(),
        ];

        if (Schema::hasColumn('tickets', 'is_urgent')) {
            $data['is_urgent'] = true;
        }

        if (Schema::hasColumn('tickets', 'trigger_message_id')) {
            $data['trigger_message_id'] = $ticket->trigger_message_id ?: $ticket->message_id;
        }

        $ticket->update($data);

        // Create a notification for escalation so it appears in the notifications center
        try {
            \App\Models\Notification::create([
                'ticket_id' => $ticket->id,
                'type' => 'ticket_escalated',
                'status' => 'pending',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('notification.create.failed', ['err' => $e->getMessage()]);
        }

        return back()->with('success', 'Ticket marque urgent et escalade.');
    }
}

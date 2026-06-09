@extends('admin.layouts.app')
@section('title', 'Communication Interne')
@section('content')
@php
use Illuminate\Support\Str;
$typeStyles = [
    'message'      => ['bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-750', 'label' => 'Message'],
    'request'      => ['bg' => 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900', 'label' => 'Demande'],
    'info_request' => ['bg' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900', 'label' => 'Demande d\'infos'],
    'follow_up'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900', 'label' => 'Suivi'],
];
@endphp

<div class="space-y-5">
    {{-- Header --}}
    <div>
        <h2 class="brand-font text-xl font-bold text-slate-900 dark:text-white">Communication Interne</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Échangez directement avec les membres de l'équipe de Support IT sans passer par les tickets.</p>
    </div>

    {{-- Chat layout container --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-md">
        <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)_260px] h-[650px] divide-y lg:divide-y-0 lg:divide-x divide-slate-250 dark:divide-slate-800">
            
            {{-- COLONNE A : Liste de l'équipe Support --}}
            <div class="flex flex-col overflow-hidden bg-slate-50/20 dark:bg-slate-900/10">
                <div class="px-4 py-3 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-550">Équipe Support</h4>
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    @forelse($teamMembers as $member)
                        @php
                            $isActive = optional($selectedUser)->id === $member->id;
                            $unread = $member->unread_count ?? 0;
                        @endphp
                        <a href="{{ route('admin.ui.internal.index', ['with' => $member->id]) }}"
                           class="flex items-center gap-3 p-3.5 transition hover:bg-slate-55 dark:hover:bg-slate-800/40 relative border-l-4 
                                  {{ $isActive ? 'bg-amber-500/5 dark:bg-amber-500/10 border-amber-500' : 'border-transparent' }}">
                            <div class="relative shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-xs font-bold text-white dark:text-slate-950">
                                    {{ Str::upper(Str::substr($member->name, 0, 1)) }}
                                </div>
                                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">{{ $member->name }}</p>
                                    @if($unread > 0)
                                        <span class="rounded-full bg-amber-500 text-[10px] font-bold text-slate-950 px-1.5 py-0.5">{{ $unread }}</span>
                                    @endif
                                </div>
                                <p class="truncate text-[10px] text-slate-450 dark:text-slate-500 font-medium">{{ $member->email }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="p-5 text-xs text-slate-400 text-center font-medium">Aucun membre de l'équipe support.</div>
                    @endforelse
                </div>
            </div>

            {{-- COLONNE B : Espace Discussion --}}
            <div class="flex flex-col overflow-hidden bg-slate-50/30 dark:bg-slate-950/20">
                @if($selectedUser)
                    {{-- Chat header --}}
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-3 bg-white dark:bg-slate-900 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-xs font-bold text-white dark:text-slate-950">
                                {{ Str::upper(Str::substr($selectedUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $selectedUser->name }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $selectedUser->email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Messages box --}}
                    <div id="internalChatBox" class="flex-1 overflow-y-auto p-5 space-y-4" data-last-message-id="0">
                        @forelse($messages as $msg)
                            @php
                                $isMe = $msg->sender_id === $userId;
                                $typeConfig = $typeStyles[$msg->type] ?? $typeStyles['message'];
                            @endphp
                            <div class="flex items-end gap-2.5 {{ $isMe ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $msg->id }}">
                                @if(!$isMe)
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-250 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-250">
                                        {{ Str::upper(Str::substr($selectedUser->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="max-w-[75%] space-y-1">
                                    <div class="flex items-center gap-2 px-1 {{ $isMe ? 'justify-end' : '' }}">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                            {{ $isMe ? 'Vous (Admin)' : $msg->sender->name }}
                                        </span>
                                        <span class="text-[9px] text-slate-450 font-semibold">{{ $msg->created_at->format('d/m H:i') }}</span>
                                    </div>
                                    <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm relative 
                                          {{ $isMe ? 'bg-amber-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-tl-none' }}">
                                        
                                        {{-- Badge du type de message s'il n'est pas standard --}}
                                        @if($msg->type !== 'message')
                                            <span class="inline-block text-[9px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full mb-1.5 {{ $typeConfig['bg'] }}">
                                                {{ $typeConfig['label'] }}
                                            </span>
                                        @endif

                                        <p class="whitespace-pre-line">{{ $msg->content }}</p>

                                        {{-- Ticket associé si présent --}}
                                        @if($msg->ticket)
                                            <div class="mt-2.5 pt-2.5 border-t border-black/10 dark:border-white/10 flex flex-col gap-1.5">
                                                <div class="rounded-xl bg-slate-100/50 dark:bg-slate-900/50 p-2.5 border border-black/5 dark:border-white/5 text-xs text-slate-650 dark:text-slate-350">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-bold">Ticket #{{ $msg->ticket->id }}</span>
                                                        <span class="text-[9px] font-extrabold tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $msg->ticket->status }}</span>
                                                    </div>
                                                    <p class="mt-1 font-semibold truncate">{{ $msg->ticket->title }}</p>
                                                </div>
                                                <a href="{{ route('admin.ui.tickets.show', $msg->ticket) }}" class="inline-flex items-center justify-center bg-slate-950 dark:bg-slate-150 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 text-[10px] font-bold py-1 px-3.5 rounded-lg shadow-sm">
                                                    Ouvrir le ticket
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($isMe)
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950">AD</div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-16 text-center text-slate-400">
                                <svg class="h-10 w-10 text-slate-300 dark:text-slate-700 mb-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-xs font-bold">Aucun message échangé</p>
                                <p class="text-[11px] text-slate-500 font-medium">Démarrez la conversation ci-dessous.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Reply form --}}
                    <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shrink-0">
                        <form method="POST" action="{{ route('admin.ui.internal.send') }}" id="internalSendForm">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                            
                            <textarea name="content" id="internalContent" rows="2" required
                                      class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 placeholder:text-slate-400 dark:text-slate-100 transition"
                                      placeholder="Écrire un message interne…"></textarea>

                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/60">
                                {{-- Option Type de message --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Type de message</label>
                                    <select name="type" class="w-full rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 py-1.5 px-3 text-xs outline-none focus:border-amber-500 dark:text-slate-350">
                                        <option value="message">Message simple</option>
                                        <option value="request">Demande</option>
                                        <option value="info_request">Demande d'information</option>
                                        <option value="follow_up">Suivi de dossier</option>
                                    </select>
                                </div>

                                {{-- Associer à un ticket --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Associer un ticket (Optionnel)</label>
                                    @php
                                        $availableTickets = \App\Models\Ticket::where('assigned_to', $selectedUser->id)
                                                            ->orWhereNull('assigned_to')
                                                            ->orderBy('created_at', 'desc')
                                                            ->take(20)->get();
                                    @endphp
                                    <select name="ticket_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 py-1.5 px-3 text-xs outline-none focus:border-amber-500 dark:text-slate-350">
                                        <option value="">Aucun ticket</option>
                                        @foreach($availableTickets as $t)
                                            <option value="{{ $t->id }}">#{{ $t->id }} - {{ Str::limit($t->title, 40) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3.5 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 px-5 py-2 text-xs font-bold text-slate-950 transition shadow-sm shadow-amber-500/10">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3.105 2.289a.75.75 0 0 0-.826.95l1.414 4.949a.75.75 0 0 0 .702.544l6.364.243c.34.013.34.509 0 .522l-6.364.243a.75.75 0 0 0-.702.544l-1.414 4.95a.75.75 0 0 0 .826.949 43.789 43.789 0 0 0 14.822-6.607.75.75 0 0 0 0-1.18A43.789 43.789 0 0 0 3.105 2.289Z"/>
                                    </svg>
                                    Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="flex flex-1 flex-col items-center justify-center gap-2.5 text-center p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-350">Sélectionnez un agent support</p>
                        <p class="text-xs text-slate-400 font-medium">Choisissez un membre de l'équipe dans la liste latérale gauche pour démarrer la messagerie interne.</p>
                    </div>
                @endif
            </div>

            {{-- COLONNE C : Fiche Support --}}
            <div class="flex flex-col overflow-y-auto bg-white dark:bg-slate-900">
                @if($selectedUser)
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Profil de l'agent</h4>
                        <dl class="space-y-2.5 text-xs">
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Nom</dt>
                                <dd class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedUser->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Email</dt>
                                <dd class="font-bold text-amber-600 dark:text-amber-400 truncate">{{ $selectedUser->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Rôles</dt>
                                <dd class="mt-1 flex flex-wrap gap-1">
                                    @foreach($selectedUser->roles as $role)
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold bg-amber-500/10 text-amber-600 rounded-full border border-amber-500/20">{{ $role->name }}</span>
                                    @endforeach
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Statistiques d'échange</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2 border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Envoyés</p>
                                <p class="text-base font-extrabold mt-0.5">{{ $stats['sent'] }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2 border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Reçus</p>
                                <p class="text-base font-extrabold mt-0.5">{{ $stats['received'] }}</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 dark:bg-amber-950/20 p-2 border border-amber-100/60 dark:border-amber-900/40 text-center">
                                <p class="text-[8px] font-bold text-amber-700 uppercase tracking-wider">Non lus</p>
                                <p class="text-base font-extrabold mt-0.5">{{ $stats['unread'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tickets assignés --}}
                    @php
                        $assignedTickets = \App\Models\Ticket::where('assigned_to', $selectedUser->id)->orderBy('created_at', 'desc')->take(10)->get();
                    @endphp
                    <div class="p-4">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Tickets assignés à l'agent</h4>
                        <div class="space-y-2.5 max-h-80 overflow-y-auto">
                            @forelse($assignedTickets as $t)
                                <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850/60 p-3 shadow-sm relative group/item">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">#{{ $t->id }}</p>
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-extrabold tracking-wide uppercase bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-450">
                                            {{ $t->status }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-350 mt-1 line-clamp-2">{{ $t->title }}</p>
                                    <a href="{{ route('admin.ui.tickets.show', $t) }}" class="block w-full text-center bg-slate-950 dark:bg-slate-100 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 font-bold py-1 rounded-lg text-[10px] shadow-sm mt-2.5">
                                        Ouvrir le ticket
                                    </a>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 font-medium">Aucun ticket assigné.</p>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="flex flex-1 items-center justify-center p-6 text-xs text-slate-450 font-bold">Sélectionnez un membre</div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('scripts')
@if($selectedUser)
<script>
    let currentUserId = {{ $selectedUser->id }};
    let lastMessageId = 0;
    let pollInterval = null;

    function scrollChatToBottom() {
        const box = document.getElementById('internalChatBox');
        if (box) box.scrollTop = box.scrollHeight;
    }

    async function pollMessages() {
        const box = document.getElementById('internalChatBox');
        if (!box) return;

        try {
            const response = await fetch(`/admin/internal-communication/api/messages/${currentUserId}?last_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (document.querySelector(`[data-message-id="${msg.id}"]`)) return;

                    const isMe = msg.sender_id === {{ $userId }};
                    const typeLabel = msg.type !== 'message' ? msg.type_label : '';
                    const typeBadge = msg.type !== 'message' ? msg.type_badge : '';
                    
                    const wrap = document.createElement('div');
                    wrap.className = `flex items-end gap-2.5 ${isMe ? 'justify-end' : 'justify-start'}`;
                    wrap.dataset.messageId = msg.id;

                    let badgeHtml = '';
                    if (msg.type !== 'message') {
                        let badgeBg = 'bg-slate-100 text-slate-700';
                        if (msg.type === 'request') badgeBg = 'bg-amber-100 text-amber-700';
                        if (msg.type === 'info_request') badgeBg = 'bg-blue-100 text-blue-700';
                        if (msg.type === 'follow_up') badgeBg = 'bg-emerald-100 text-emerald-700';

                        badgeHtml = `<span class="inline-block text-[9px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full mb-1.5 ${badgeBg}">${msg.type_label}</span>`;
                    }

                    let ticketHtml = '';
                    if (msg.ticket) {
                        ticketHtml = `
                            <div class="mt-2.5 pt-2.5 border-t border-black/10 dark:border-white/10 flex flex-col gap-1.5">
                                <div class="rounded-xl bg-slate-100/50 dark:bg-slate-900/50 p-2.5 border border-black/5 dark:border-white/5 text-xs text-slate-650 dark:text-slate-350">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold">Ticket #${msg.ticket.id}</span>
                                        <span class="text-[9px] font-extrabold tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">${msg.ticket.status}</span>
                                    </div>
                                    <p class="mt-1 font-semibold truncate">${msg.ticket.title}</p>
                                </div>
                                <a href="/admin/tickets/${msg.ticket.id}" class="inline-flex items-center justify-center bg-slate-950 dark:bg-slate-150 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 text-[10px] font-bold py-1 px-3.5 rounded-lg shadow-sm">
                                    Ouvrir le ticket
                                </a>
                            </div>
                        `;
                    }

                    wrap.innerHTML = `
                        ${!isMe ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-250 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-250">${escHtml(msg.sender.name.charAt(0).toUpperCase())}</div>` : ''}
                        <div class="max-w-[75%] space-y-1">
                            <div class="flex items-center gap-2 px-1 ${isMe ? 'justify-end' : ''}">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">${isMe ? 'Vous (Admin)' : escHtml(msg.sender.name)}</span>
                                <span class="text-[9px] text-slate-455 font-semibold">${fmtDt(msg.created_at)}</span>
                            </div>
                            <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm relative ${isMe ? 'bg-amber-600 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-tl-none'}">
                                ${badgeHtml}
                                <p class="whitespace-pre-line">${escHtml(msg.content)}</p>
                                ${ticketHtml}
                            </div>
                        </div>
                        ${isMe ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950">AD</div>` : ''}
                    `;

                    box.appendChild(wrap);
                    lastMessageId = msg.id;
                });
                scrollChatToBottom();
            }
        } catch(e) {
            console.error('polling error', e);
        }
    }

    window.onload = function() {
        const box = document.getElementById('internalChatBox');
        if (box) {
            const items = box.querySelectorAll('[data-message-id]');
            if (items.length > 0) {
                lastMessageId = parseInt(items[items.length - 1].dataset.messageId);
            }
        }
        scrollChatToBottom();
        
        // Polling loop
        pollInterval = setInterval(pollMessages, 5000);
        
        // Mark read immediately
        fetch(`/admin/internal-communication/api/mark-read/${currentUserId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
    };
</script>
@endif
@endpush
@endsection

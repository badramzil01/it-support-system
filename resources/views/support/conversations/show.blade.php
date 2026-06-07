@extends('support.layouts.app')

@section('title', 'Conversation')

@section('content')
@php
    $senderStyles = [
        'user'    => 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100',
        'support' => 'bg-blue-600 text-white',
        'agent'   => 'bg-blue-600 text-white',
        'ai'      => 'bg-violet-600 text-white',
        'bot'     => 'bg-violet-600 text-white',
        'system'  => 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200',
    ];
@endphp

<div>
    <!-- Stats bar: full width, above conversation layout -->
    <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded shadow flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="text-sm font-semibold">Statistiques</div>
            <div class="flex items-center gap-2 text-sm">
                <div class="px-3 py-1 bg-blue-50 text-blue-700 rounded">Total tickets: {{ $tickets->count() }}</div>
                <div class="px-3 py-1 bg-red-50 text-red-700 rounded">Urgents: {{ $tickets->where('is_urgent',1)->count() }}</div>
                <div class="px-3 py-1 bg-pink-50 text-pink-700 rounded">Escalés: {{ $tickets->where('is_escalated',1)->count() }}</div>
            </div>
        </div>
        <div class="text-xs text-gray-500">Dernière mise à jour: {{ optional($conversation->updated_at)->diffForHumans() }}</div>
    </div>

<div class="flex h-[calc(100vh-8.5rem)] gap-4 overflow-hidden">

    {{-- ── COL 1 : Contacts sidebar ── --}}
    <aside class="flex w-72 shrink-0 flex-col overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4 py-3">
            <h3 class="text-sm font-semibold">Contacts</h3>
            <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-300">{{ $users->count() }}</span>
        </div>
        <div class="border-b border-slate-200 dark:border-slate-800 px-3 py-2.5">
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 top-2 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                </svg>
                <input type="text" placeholder="Rechercher" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-1.5 pl-8 pr-3 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
            </div>
        </div>

        <ul class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($users as $u)
                @php $active = optional($selectedUser)->id == $u->id; @endphp
                <li>
                    <a href="{{ route('support.ui.conversations.show', ['conversation' => optional($u->conversations->first())->id ?? '']) }}"
                       class="flex items-center gap-3 p-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/60
                              {{ $active ? 'bg-blue-50 dark:bg-blue-950/40 border-l-2 border-blue-600' : '' }}">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-xs font-semibold text-white dark:text-slate-900">
                            {{ Str::upper(Str::substr($u->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <p class="truncate text-sm font-medium">{{ $u->name }}</p>
                                @if($u->messages_count ?? 0)
                                    <span class="shrink-0 rounded-full bg-blue-600 px-1.5 py-0.5 text-[9px] font-medium text-white">{{ $u->messages_count }}</span>
                                @endif
                            </div>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $u->email }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>

    {{-- ── COL 2 : Conversations list ── --}}
    <div class="flex w-72 shrink-0 flex-col overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4 py-3">
            <h3 class="text-sm font-semibold">Fils</h3>
            <a href="#" class="rounded-md bg-blue-50 dark:bg-blue-950 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition">
                + Nouveau
            </a>
        </div>

        <ul class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($conversationsList as $conv)
                @php $activeConv = $conv->id == $conversation->id; @endphp
                <li>
                    <a href="{{ route('support.ui.conversations.show', ['conversation' => $conv->id]) }}"
                       class="block p-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/60
                              {{ $activeConv ? 'bg-slate-100 dark:bg-slate-800 border-l-2 border-blue-600' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="truncate text-sm font-semibold">{{ $conv->title ?? 'Conversation #'.$conv->id }}</h4>
                            @if($conv->messages_count ?? 0)
                                <span class="shrink-0 rounded-full bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 text-[10px] text-slate-500 dark:text-slate-300">{{ $conv->messages_count }}</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-[11px] text-slate-400 dark:text-slate-500">{{ optional($conv->updated_at)->diffForHumans() }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- ── COL 3 : Chat ── --}}
    <main class="flex flex-1 flex-col overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">

        {{-- Chat header --}}
        <header class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-sm font-semibold text-white dark:text-slate-900">
                    {{ Str::upper(Str::substr($selectedUser->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ $selectedUser->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $selectedUser->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" class="p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800/60" title="Assigner">
                    <svg class="h-5 w-5 text-slate-500 dark:text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 4a3 3 0 100 6 3 3 0 000-6z"/><path fill-rule="evenodd" d="M2 14s1-4 8-4 8 4 8 4-1 2-8 2-8-2-8-2z" clip-rule="evenodd"/></svg>
                </a>
                <a href="#" class="p-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800/60 text-red-600" title="Marquer urgent">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.516 9.8A1.75 1.75 0 0116.516 16H3.484a1.75 1.75 0 01-1.742-3.101l5.515-9.8zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-.25-6a.75.75 0 00-1.5 0v3a.75.75 0 001.5 0v-3z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </header>

        {{-- Messages area --}}
        <section id="chatBox" class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-950 p-4">
            <div class="space-y-3">
                @foreach($messages as $m)
                    @php
                        $sender  = $m->sender ?: 'user';
                        $isRight = in_array($sender, ['support', 'agent'], true);
                        $isAi    = in_array($sender, ['ai', 'bot'], true);
                        $bubble  = $senderStyles[$sender] ?? $senderStyles['system'];
                    @endphp
                    <div class="flex items-end gap-2 {{ $isRight ? 'justify-end' : 'justify-start' }}">
                        @if(!$isRight)
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                {{ Str::upper(Str::substr($m->user?->name ?? $selectedUser->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="max-w-[75%]">
                            <div class="mb-1 flex items-center gap-1.5 {{ $isRight ? 'justify-end' : '' }}">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    {{ $sender === 'user' ? ($m->user?->name ?? $selectedUser->name) : ($isAi ? 'IA' : 'Support') }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $m->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed {{ $bubble }}
                                        {{ $isRight ? 'rounded-br-sm' : 'rounded-bl-sm' }}">
                                {!! nl2br(e($m->content)) !!}
                            </div>
                        </div>

                        @if($isRight)
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950 text-xs font-semibold text-blue-700 dark:text-blue-300">
                                SP
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Reply form --}}
        <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
            <form method="POST" action="{{ route('support.ui.conversations.send') }}" class="flex flex-col gap-3">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                <textarea id="content" name="content" rows="3"
                          class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400"
                          placeholder="Écrire un message..."></textarea>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <button type="button" class="flex items-center gap-1 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7l-4-4H4z"/></svg>
                            Fichier
                        </button>
                        <button type="button" class="flex items-center gap-1 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11.25a.75.75 0 0 1 1.5 0v2.5h2.5a.75.75 0 0 1 0 1.5h-2.5v2.5a.75.75 0 0 1-1.5 0v-2.5H7a.75.75 0 0 1 0-1.5h2.5v-2.5Z" clip-rule="evenodd"/></svg>
                            Emoji
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" name="_as" value="ai"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <svg class="h-3.5 w-3.5 text-violet-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                            Envoyer (IA)
                        </button>
                        <button type="submit" name="_as" value="support"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-medium text-white hover:bg-blue-700 active:scale-95 transition">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3.105 2.289a.75.75 0 0 0-.826.95l1.414 4.949a.75.75 0 0 0 .702.544l6.364.243c.34.013.34.509 0 .522l-6.364.243a.75.75 0 0 0-.702.544l-1.414 4.95a.75.75 0 0 0 .826.949 43.789 43.789 0 0 0 14.822-6.607.75.75 0 0 0 0-1.18A43.789 43.789 0 0 0 3.105 2.289Z"/></svg>
                            Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </footer>
    </main>

</div>

@push('scripts')
<script>
    (function () {
        const chat = document.getElementById('chatBox');
        if (chat) chat.scrollTop = chat.scrollHeight;
        const ta = document.getElementById('content');
        if (ta) ta.focus();
    })();
</script>
@endpush
@endsection
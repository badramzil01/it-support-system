@extends('support.layouts.app')
@section('title', 'Ticket #' . $ticket->id)
@section('content')
@php
use Illuminate\Support\Str;

$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950',      'text' => 'text-red-700 dark:text-red-300',      'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950',   'text' => 'text-amber-700 dark:text-amber-300',   'dot' => 'bg-amber-500'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',   'text' => 'text-slate-600 dark:text-slate-300',   'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-950',     'text' => 'text-blue-700 dark:text-blue-300'],
    'in_progress' => ['bg' => 'bg-violet-100 dark:bg-violet-950', 'text' => 'text-violet-700 dark:text-violet-300'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950','text' => 'text-emerald-700 dark:text-emerald-300'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',   'text' => 'text-slate-500 dark:text-slate-400'],
];
$pCfg = $priorityConfig[$ticket->priority] ?? $priorityConfig['low'];
$sCfg = $statusConfig[$ticket->status]   ?? $statusConfig['closed'];

$confidencePct = is_numeric($ticket->confidence) ? (float) $ticket->confidence : 0;
$confidencePct = $confidencePct > 1 ? $confidencePct : $confidencePct * 100;
$confidenceColor = $confidencePct >= 75 ? 'bg-emerald-500' : ($confidencePct >= 45 ? 'bg-amber-500' : 'bg-red-500');
@endphp

{{-- Breadcrumb --}}
<div class="mb-4 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
    <a href="{{ route('support.ui.tickets.index') }}" class="hover:text-slate-900 dark:hover:text-slate-100 transition">Tickets</a>
    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
    <span class="text-slate-900 dark:text-slate-100 font-medium">Ticket #{{ $ticket->id }}</span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-4">

    {{-- ── COL PRINCIPALE ── --}}
    <div class="space-y-4">

        {{-- Titre + badges --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="font-mono text-xs text-slate-400">#{{ $ticket->id }}</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'open')) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $pCfg['dot'] }}"></span>
                            {{ ucfirst($ticket->priority ?? 'low') }}
                        </span>
                        @if($ticket->is_urgent)
                            <span class="rounded-full bg-red-100 dark:bg-red-950 px-2.5 py-1 text-[11px] font-medium text-red-700 dark:text-red-300">🔴 Urgent</span>
                        @endif
                        @if($ticket->is_escalated)
                            <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2.5 py-1 text-[11px] font-medium text-amber-800 dark:text-amber-300">⚠ Escaladé</span>
                        @endif
                    </div>
                    <h2 class="text-lg font-semibold leading-snug text-slate-900 dark:text-slate-100">{{ $ticket->title }}</h2>
                </div>

                {{-- Quick status update --}}
                <form method="POST" action="{{ route('support.ui.tickets.updateStatus', $ticket) }}" class="flex items-center gap-2 shrink-0">
                    @csrf
                    <label class="text-xs text-slate-500 dark:text-slate-400">Statut</label>
                    <select name="status" onchange="this.form.submit()"
                            class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 pl-3 pr-7 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 cursor-pointer">
                        <option value="open"        {{ $ticket->status=='open'        ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved"    {{ $ticket->status=='resolved'    ? 'selected' : '' }}>Resolved</option>
                        <option value="closed"      {{ $ticket->status=='closed'      ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Description --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Description du problème</h3>
            <div class="prose prose-sm max-w-none text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                {{ $ticket->description ?: 'Aucune description fournie.' }}
            </div>
        </div>

        {{-- IA Analysis --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-950">
                    <svg class="h-4 w-4 text-violet-600 dark:text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 1a6 6 0 0 0-3.815 10.631C7.237 12.5 8 13.443 8 14.456v.644a.75.75 0 0 0 .572.729 6.016 6.016 0 0 0 2.856 0A.75.75 0 0 0 12 15.1v-.644c0-1.013.762-1.957 1.815-2.825A6 6 0 0 0 10 1ZM8.863 17.414a.75.75 0 0 0-.226 1.483 9.066 9.066 0 0 0 2.726 0 .75.75 0 0 0-.226-1.483 7.553 7.553 0 0 1-2.274 0Z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold">Analyse IA</h3>
            </div>

            @if($ticket->solution)
                <div class="mb-4 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-2">Solution proposée</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $ticket->solution }}</p>
                </div>
            @else
                <div class="mb-4 rounded-lg bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-300 dark:border-slate-700 p-4 text-center">
                    <p class="text-xs text-slate-400">Aucune solution proposée par l'IA</p>
                </div>
            @endif

            {{-- Confidence bar --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Confiance IA</span>
                    <span class="text-xs font-semibold {{ $confidencePct >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($confidencePct >= 45 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                        {{ number_format($confidencePct, 0) }}%
                    </span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full transition-all {{ $confidenceColor }}" style="width: {{ min(100, $confidencePct) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Message déclencheur --}}
        @if($ticket->message)
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Message déclencheur</h3>
                <div class="rounded-lg bg-slate-50 dark:bg-slate-800 border-l-4 border-blue-500 px-4 py-3">
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed italic">"{{ $ticket->message->content }}"</p>
                    <p class="mt-2 text-[11px] text-slate-400">{{ $ticket->message->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @endif

    </div>

    {{-- ── SIDEBAR DROITE ── --}}
    <div class="space-y-4">

        {{-- Infos ticket --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Informations</h3>
            <dl class="space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Catégorie</dt>
                    <dd class="text-xs font-medium text-right">{{ $ticket->category ?? '—' }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Source</dt>
                    <dd class="text-xs font-medium text-right">{{ $ticket->source ?? '—' }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Feedback</dt>
                    <dd>
                        @php
                            $fbColors = ['positive'=>'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300','negative'=>'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300','pending'=>'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'];
                            $fbCls = $fbColors[$ticket->feedback ?? 'pending'] ?? $fbColors['pending'];
                        @endphp
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-medium {{ $fbCls }}">{{ ucfirst($ticket->feedback ?? 'pending') }}</span>
                    </dd>
                </div>
                @if($ticket->jira_ticket_id)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Jira</dt>
                        <dd>
                            <a href="{{ rtrim(config('services.jira.url','#'), '/').'/browse/'.$ticket->jira_ticket_id }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300 hover:underline">
                                {{ $ticket->jira_ticket_id }}
                                <svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd"/></svg>
                            </a>
                        </dd>
                    </div>
                @endif
                <div class="border-t border-slate-100 dark:border-slate-800 pt-3 flex items-start justify-between gap-3">
                    <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Créé</dt>
                    <dd class="text-xs font-medium text-right">{{ $ticket->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-xs text-slate-500 dark:text-slate-400 shrink-0">Mis à jour</dt>
                    <dd class="text-xs font-medium text-right">{{ $ticket->updated_at?->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>

        {{-- Client --}}
        @if($ticket->user)
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client</h3>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-sm font-semibold text-white dark:text-slate-900">
                        {{ Str::upper(Str::substr($ticket->user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">{{ $ticket->user->name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $ticket->user->email }}</p>
                    </div>
                </div>
                @if($ticket->user->department ?? $ticket->user->role)
                    <div class="mt-3 flex justify-between text-xs">
                        <span class="text-slate-500">Département</span>
                        <span class="font-medium">{{ $ticket->user->department ?? $ticket->user->role }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Agent assigné --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Agent assigné</h3>
            @if($ticket->assignedAgent)
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950 text-xs font-semibold text-blue-700 dark:text-blue-300">
                        {{ Str::upper(Str::substr($ticket->assignedAgent->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ $ticket->assignedAgent->name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $ticket->assignedAgent->email }}</p>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 mb-3">Aucun agent assigné</p>
            @endif
            <form method="POST" action="{{ route('support.ui.tickets.assignToMe', $ticket) }}" class="mt-3">
                @csrf
                <button class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                    M'assigner ce ticket
                </button>
            </form>
        </div>

        {{-- Liens rapides --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Liens rapides</h3>
            <div class="space-y-2">
                @if($ticket->conversation_id)
                    <a href="{{ route('support.ui.conversations.index', ['conversation_id' => $ticket->conversation_id]) }}"
                       class="flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <svg class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5Zm3.293 1.293a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L7.586 10 5.293 7.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                        Ouvrir chat utilisateur
                    </a>
                @endif
                @if($ticket->jira_ticket_id)
                    <a href="{{ rtrim(config('services.jira.url','#'), '/').'/browse/'.$ticket->jira_ticket_id }}"
                       target="_blank"
                       class="flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2.5 text-xs font-medium text-white hover:bg-blue-700 transition">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.061l1.224-1.225a4 4 0 0 0-5.656-5.656L8.929 5.414a.75.75 0 1 0 1.06 1.061l2.243-2.243Z"/><path d="M7.768 15.768a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.061l-1.224 1.225a4 4 0 1 0 5.656 5.656l2.243-2.242a.75.75 0 1 0-1.06-1.061l-2.243 2.243Z"/></svg>
                        Ouvrir dans Jira
                    </a>
                @endif
                <a href="{{ route('support.ui.tickets.index') }}"
                   class="flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd"/></svg>
                    Retour aux tickets
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
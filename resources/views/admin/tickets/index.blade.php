@extends('admin.layouts.app')
@section('title', 'Tickets')
@section('content')
@php
use Illuminate\Support\Str;

$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950',    'text' => 'text-red-700 dark:text-red-300',    'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-500'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',  'text' => 'text-slate-600 dark:text-slate-300',  'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-amber-100 dark:bg-blue-950',    'text' => 'text-amber-700 dark:text-amber-300'],
    'in_progress' => ['bg' => 'bg-violet-100 dark:bg-violet-950','text' => 'text-violet-700 dark:text-violet-300'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950','text' => 'text-emerald-700 dark:text-emerald-300'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',  'text' => 'text-slate-500 dark:text-slate-400'],
];

$levelConfig = [
    'n1' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-700 dark:text-blue-300', 'label' => 'N1'],
    'n2' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-700 dark:text-amber-300', 'label' => 'N2'],
    'n3' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-700 dark:text-red-300', 'label' => 'N3'],
    'manager' => ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-700 dark:text-purple-300', 'label' => 'Mgr'],
];

function sortUrl($col, $sort, $direction) {
    $dir = ($sort === $col && $direction === 'desc') ? 'asc' : 'desc';
    return url()->current() . '?' . http_build_query(array_merge(request()->query(), ['sort' => $col, 'direction' => $dir]));
}
function sortIcon($col, $sort, $direction) {
    if ($sort !== $col) return '';
    return $direction === 'desc' ? '↓' : '↑';
}
@endphp

<div class="space-y-4">

    {{-- ── FILTER BAR ── --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <form method="GET" action="{{ route('admin.ui.tickets.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">

                {{-- Search --}}
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Recherche</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Titre, description…"
                               class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 pl-9 pr-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Statut --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Statut</label>
                    <select name="status" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                        <option value="">Tous</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Priorité --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Priorité</label>
                    <select name="priority" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                        <option value="">Toutes</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Support Level --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Support Level</label>
                    <select name="support_level" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                        <option value="">Tous</option>
                        @foreach($levels as $key => $label)
                            <option value="{{ $key }}" {{ request('support_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Catégorie</label>
                    <select name="category" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                        <option value="">Toutes</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date from --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Depuis</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                </div>

                {{-- Date to --}}
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Jusqu'au</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:focus:ring-blue-950">
                </div>

                {{-- Checkboxes + Actions --}}
                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-6 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="is_urgent" value="1" {{ request('is_urgent') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <span class="text-slate-600 dark:text-slate-300">Urgent</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="is_escalated" value="1" {{ request('is_escalated') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-slate-600 dark:text-slate-300">Escaladé</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="assigned_to_me" value="1" {{ request('assigned_to_me') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-600 dark:text-slate-300">Assigné à moi</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.ui.tickets.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Réinitialiser
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 active:scale-95 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                            </svg>
                            Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ── TABLE ── --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">

        {{-- Table header info --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 dark:border-slate-800">
            <p class="text-sm font-semibold">
                {{ $tickets->total() }} ticket{{ $tickets->total() > 1 ? 's' : '' }}
                @if(request()->hasAny(['search','status','priority','category','is_urgent','is_escalated','assigned_to_me','date_from','date_to']))
                    <span class="ml-2 rounded-full bg-amber-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300">Filtrés</span>
                @endif
            </p>
            <p class="text-xs text-slate-400">Page {{ $tickets->currentPage() }} / {{ $tickets->lastPage() }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        @php
                            $cols = [
                                'id'             => 'ID',
                                'title'          => 'Titre',
                                'user'           => 'Utilisateur',
                                'category'       => 'Catégorie',
                                'support_level'  => 'Level',
                                'priority'       => 'Priorité',
                                'status'         => 'Statut',
                                'jira_ticket_id' => 'Jira',
                                'created_at'     => 'Date',
                            ];
                        @endphp
                        @foreach($cols as $col => $label)
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                <a href="{{ sortUrl($col, $sort ?? '', $direction ?? 'desc') }}"
                                   class="inline-flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100 transition">
                                    {{ $label }}
                                    @php $icon = sortIcon($col, $sort ?? '', $direction ?? 'desc'); @endphp
                                    @if($icon)
                                        <span class="text-blue-600 dark:text-amber-400">{{ $icon }}</span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">↕</span>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Flags</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($tickets as $t)
                        @php
                            $pCfg = $priorityConfig[$t->priority] ?? $priorityConfig['low'];
                            $sCfg = $statusConfig[$t->status]   ?? $statusConfig['closed'];
                            $lCfg = $levelConfig[$t->support_level] ?? ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-400', 'label' => strtoupper($t->support_level ?? 'N1')];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">

                            {{-- ID --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs font-mono text-slate-400">#{{ $t->id }}</span>
                            </td>

                            {{-- Titre --}}
                            <td class="px-4 py-3 max-w-xs">
                                <a href="{{ route('admin.ui.tickets.show', $t) }}"
                                   class="font-medium text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-amber-400 transition line-clamp-2">
                                    {{ Str::limit($t->title, 65) }}
                                </a>
                            </td>

                            {{-- Utilisateur --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($t->user)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                            {{ Str::upper(Str::substr($t->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-slate-700 dark:text-slate-200">{{ $t->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Catégorie --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ $t->category ?? '—' }}</span>
                            </td>

                            {{-- Support Level --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $lCfg['bg'] }} {{ $lCfg['text'] }}">
                                    {{ $lCfg['label'] }}
                                </span>
                            </td>

                            {{-- Priorité --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $pCfg['dot'] }}"></span>
                                    {{ ucfirst($t->priority ?? 'low') }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">
                                    {{ ucfirst(str_replace('_', ' ', $t->status ?? 'open')) }}
                                </span>
                            </td>

                            {{-- Jira --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($t->jira_ticket_id)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300">
                                        {{ $t->jira_ticket_id }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t->created_at->format('d/m/Y') }}</span>
                                <br>
                                <span class="text-[10px] text-slate-400">{{ $t->created_at->format('H:i') }}</span>
                            </td>

                            {{-- Flags --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    @if($t->is_urgent)
                                        <span class="rounded-full bg-red-100 dark:bg-red-950 px-2 py-0.5 text-[10px] font-medium text-red-700 dark:text-red-300">Urgent</span>
                                    @endif
                                    @if($t->is_escalated)
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2 py-0.5 text-[10px] font-medium text-amber-800 dark:text-amber-300">Escaladé</span>
                                    @endif
                                    @if(!$t->is_urgent && !$t->is_escalated)
                                        <span class="text-[11px] text-slate-400">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.ui.tickets.show', $t) }}"
                                       class="inline-flex items-center gap-1 rounded-lg bg-amber-600 px-2.5 py-1.5 text-[11px] font-medium text-white hover:bg-amber-700 transition">
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41Z" clip-rule="evenodd"/></svg>
                                        Voir
                                    </a>

                                    {{-- Escalate Button --}}
                                    <button type="button"
                                            onclick="openEscalateModal({{ $t->id }}, '{{ $t->support_level ?? 'n1' }}')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-amber-200 dark:border-amber-800 px-2.5 py-1.5 text-[11px] font-medium text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                                        Escalader
                                    </button>

                                    <form method="POST" action="{{ route('admin.ui.tickets.assignToMe', $t) }}">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                                            Assigner
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.ui.tickets.updateStatus', $t) }}" class="flex items-center gap-1">
                                        @csrf
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 pl-2 pr-6 text-[11px] outline-none focus:border-amber-500 cursor-pointer">
                                            <option value="open"        {{ $t->status=='open'        ? 'selected' : '' }}>Open</option>
                                            <option value="in_progress" {{ $t->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved"    {{ $t->status=='resolved'    ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed"      {{ $t->status=='closed'      ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        <svg class="h-6 w-6 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Zm1 3.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1-.75-.75Zm0 3.25A.75.75 0 0 1 7.25 9.25h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 10Zm.75 2.5h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1 0-1.5Z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun ticket trouvé</p>
                                    <p class="text-xs text-slate-400">Essayez de modifier les filtres</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tickets->hasPages())
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Affichage {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} sur {{ $tickets->total() }}</span>
                    <div class="flex items-center gap-1">
                        @if($tickets->onFirstPage())
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </span>
                        @else
                            <a href="{{ $tickets->previousPageUrl() }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </a>
                        @endif

                        @foreach($tickets->getUrlRange(max(1, $tickets->currentPage()-2), min($tickets->lastPage(), $tickets->currentPage()+2)) as $page => $url)
                            <a href="{{ $url }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-xs font-medium transition
                                      {{ $page == $tickets->currentPage()
                                          ? 'border-blue-600 bg-amber-600 text-white'
                                          : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        @if($tickets->hasMorePages())
                            <a href="{{ $tickets->nextPageUrl() }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </a>
                        @else
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Escalate Modal --}}
<div id="escalateModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEscalateModal()"></div>
    <div class="relative bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg mx-4 p-6 z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold brand-font">Escalate Ticket</h3>
            <button onclick="closeEscalateModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>

        <form id="escalateForm" method="POST">
            @csrf

            <div class="space-y-4">
                {{-- Current Level --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Current Level</label>
                    <p id="currentLevelDisplay" class="text-sm font-semibold text-slate-900 dark:text-white px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg"></p>
                </div>

                {{-- Target Level --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Target Level</label>
                    <select name="to_level" id="targetLevel" required
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                        <option value="n1">N1 - First Line Support</option>
                        <option value="n2">N2 - Advanced Technical Support</option>
                        <option value="n3">N3 - Expert Support</option>
                        <option value="manager">Support Manager</option>
                    </select>
                </div>

                {{-- Assignee --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Assign To (optional)</label>
                    <select name="assigned_to" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                        <option value="">Auto-assign to team member</option>
                        @foreach($supportTeams as $team)
                            <optgroup label="{{ $team->name }}">
                                @foreach($team->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Reason --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="escalateReason" rows="3" required minlength="5"
                              class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200"
                              placeholder="e.g., Complex network issue, requires database expertise, security investigation..."></textarea>
                </div>

                {{-- Suggested reasons --}}
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-2">Quick reasons:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="setReason('Complex network issue')"
                                class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Complex network issue
                        </button>
                        <button type="button" onclick="setReason('Requires database expertise')"
                                class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Requires database expertise
                        </button>
                        <button type="button" onclick="setReason('Security investigation')"
                                class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Security investigation
                        </button>
                        <button type="button" onclick="setReason('Infrastructure problem')"
                                class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Infrastructure problem
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 shadow-lg shadow-amber-500/30 transition">
                    Escalate Ticket
                </button>
                <button type="button" onclick="closeEscalateModal()"
                        class="px-6 py-2 rounded-lg text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEscalateModal(ticketId, currentLevel) {
    document.getElementById('escalateForm').action = '/admin/tickets/' + ticketId + '/escalate';
    document.getElementById('currentLevelDisplay').textContent = currentLevel.toUpperCase();
    document.getElementById('escalateReason').value = '';
    document.getElementById('escalateModal').classList.remove('hidden');
}

function closeEscalateModal() {
    document.getElementById('escalateModal').classList.add('hidden');
}

function setReason(reason) {
    document.getElementById('escalateReason').value = reason;
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEscalateModal();
});
</script>
@endpush
@endsection
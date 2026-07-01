@extends('support.layouts.app')
@section('title', 'Escalated Tickets')
@section('content')
@php
use Illuminate\Support\Str;

$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950',    'text' => 'text-red-700 dark:text-red-300',    'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-400'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',  'text' => 'text-slate-600 dark:text-slate-300',  'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-950',    'text' => 'text-blue-700 dark:text-blue-300'],
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

    {{-- Level indicator --}}
    @if($userLevel)
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 flex items-center gap-3">
        <span class="text-xs text-slate-500">Your level:</span>
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $levelConfig[$userLevel]['bg'] ?? 'bg-slate-100' }} {{ $levelConfig[$userLevel]['text'] ?? 'text-slate-600' }}">
            {{ strtoupper($userLevel) }}
        </span>
        <span class="text-xs text-slate-400 ml-2">{{ $escalations->total() }} escalation(s) visible(s)</span>
    </div>
    @endif

    {{-- ── FILTER BAR ── --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <form method="GET" action="{{ route('support.ui.escalations.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Recherche</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="ID ou titre du ticket..."
                               class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Direction</label>
                    <select name="direction" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500">
                        <option value="">Toutes</option>
                        @foreach($directions as $k => $label)
                            <option value="{{ $k }}" {{ request('direction') == $k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Période</label>
                    <select name="period" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500">
                        <option value="">Toutes</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-6 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="assigned_to_me" value="1" {{ request('assigned_to_me') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-600 dark:text-slate-300">Assigné à moi</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('support.ui.escalations.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Réinitialiser
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 active:scale-95 transition">
                            Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ── TABLE ── --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 dark:border-slate-800">
            <p class="text-sm font-semibold">{{ $escalations->total() }} escalation(s)</p>
            <p class="text-xs text-slate-400">Page {{ $escalations->currentPage() }} / {{ $escalations->lastPage() }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        @php $cols = ['ticket_id'=>'ID','ticket.title'=>'Titre','ticket.user'=>'Utilisateur','ticket.category'=>'Catégorie','ticket.priority'=>'Priorité','from_level'=>'Niveau précédent','to_level'=>'Niveau actuel','ticket.status'=>'Statut','jira_key'=>'Jira','created_at'=>'Date','reason'=>'Raison escalade']; @endphp
                        @foreach($cols as $col => $label)
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 whitespace-nowrap">
                                {{ $label }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Flags</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($escalations as $esc)
                        @php
                            $ticket = $esc->ticket;
                            $pCfg = $priorityConfig[$ticket->priority ?? 'low'];
                            $sCfg = $statusConfig[$ticket->status ?? 'open'] ?? $statusConfig['open'];
                            $lCfgFrom = $levelConfig[$esc->from_level] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-600','label'=>strtoupper($esc->from_level)];
                            $lCfgTo = $levelConfig[$esc->to_level] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-600','label'=>strtoupper($esc->to_level)];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">
                            <td class="px-4 py-3 whitespace-nowrap"><span class="text-xs font-mono text-slate-400">#{{ $esc->ticket_id ?? '—' }}</span></td>
                            <td class="px-4 py-3 max-w-xs">
                                @if($ticket)
                                    <a href="{{ route('support.ui.tickets.show', $ticket) }}"
                                       class="font-medium text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition line-clamp-2">
                                        {{ Str::limit($ticket->title, 65) }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($ticket && $ticket->user)
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                            {{ Str::upper(Str::substr($ticket->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-slate-700 dark:text-slate-200">{{ $ticket->user->name }}</span>
                                    </div>
                                @else <span class="text-xs text-slate-400">—</span> @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap"><span class="text-xs text-slate-600 dark:text-slate-300">{{ $ticket->category ?? '—' }}</span></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $pCfg['dot'] }}"></span>{{ ucfirst($ticket->priority ?? 'low') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $lCfgFrom['bg'] }} {{ $lCfgFrom['text'] }}">{{ $lCfgFrom['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $lCfgTo['bg'] }} {{ $lCfgTo['text'] }}">{{ $lCfgTo['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($ticket)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">
                                        {{ ucfirst(str_replace('_',' ',$ticket->status ?? 'open')) }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($esc->jira_key)
                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300">{{ $esc->jira_key }}</span>
                                @else <span class="text-xs text-slate-400">—</span> @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-500">{{ $esc->created_at->format('d/m/Y') }}</span><br>
                                <span class="text-[10px] text-slate-400">{{ $esc->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ Str::limit($esc->reason, 50) }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    @if($ticket && $ticket->is_urgent) <span class="rounded-full bg-red-100 dark:bg-red-950 px-2 py-0.5 text-[10px] font-medium text-red-700">Urgent</span> @endif
                                    @if($ticket && $ticket->is_escalated) <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2 py-0.5 text-[10px] font-medium text-amber-800">Escaladé</span> @endif
                                    @if(!$ticket || (!$ticket->is_urgent && !$ticket->is_escalated)) <span class="text-[11px] text-slate-400">—</span> @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('support.ui.tickets.show', $ticket) }}"
                                       class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-2.5 py-1.5 text-[11px] font-medium text-white hover:bg-blue-700 transition">
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41Z" clip-rule="evenodd"/></svg>
                                        Voir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="px-4 py-16 text-center text-slate-400">
                            <p class="text-sm font-medium">Aucune escalation trouvée</p>
                            <p class="text-xs mt-1">Essayez de modifier les filtres</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($escalations->hasPages())
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">{{ $escalations->links() }}</div>
        @endif
    </div>
</div>
@endsection
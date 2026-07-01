@extends('admin.layouts.app')
@section('title', 'Escalated Tickets')
@section('content')
@php
$levelColors = [
    'n1' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-700 dark:text-blue-300'],
    'n2' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-700 dark:text-amber-300'],
    'n3' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-700 dark:text-red-300'],
    'manager' => ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-700 dark:text-purple-300'],
];
$priorityConfig = [
    'critical' => 'text-red-700 dark:text-red-300',
    'high'     => 'text-orange-700 dark:text-orange-300',
    'medium'   => 'text-amber-700 dark:text-amber-300',
    'low'      => 'text-slate-600 dark:text-slate-400',
];
@endphp
<div class="space-y-4">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase">Total Escalations</p>
            <p class="text-2xl font-bold mt-1 text-amber-500">{{ $stats['total_escalations'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase">Today</p>
            <p class="text-2xl font-bold mt-1">{{ $stats['escalated_today'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase">This Week</p>
            <p class="text-2xl font-bold mt-1">{{ $stats['escalated_this_week'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase">This Month</p>
            <p class="text-2xl font-bold mt-1">{{ $stats['escalated_this_month'] }}</p>
        </div>
    </div>

    {{-- Team Dashboards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- N1 Dashboard --}}
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> N1 Dashboard
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Assigned to N1:</span><span class="font-medium">{{ $ticketsByTeam['n1_total'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Escalated from N1:</span><span class="font-medium text-amber-500">{{ $ticketsByTeam['n1_escalated'] }}</span></div>
            </div>
        </div>

        {{-- N2 Dashboard --}}
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> N2 Dashboard
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Received from N1:</span><span class="font-medium text-blue-500">{{ $ticketsByTeam['n2_from_n1'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Assigned to N2:</span><span class="font-medium">{{ $ticketsByTeam['n2_total'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Escalated to N3:</span><span class="font-medium text-red-500">{{ $ticketsByTeam['n2_escalated_to_n3'] }}</span></div>
            </div>
        </div>

        {{-- N3 Dashboard --}}
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> N3 Dashboard
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Received from N2:</span><span class="font-medium text-amber-500">{{ $ticketsByTeam['n3_from_n2'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Assigned to N3:</span><span class="font-medium">{{ $ticketsByTeam['n3_total'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Critical tickets:</span><span class="font-medium text-red-500">{{ $ticketsByTeam['n3_critical'] }}</span></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <form method="GET" action="{{ route('admin.escalations.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500">Direction</label>
                    <select name="direction" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500">
                        <option value="">All Directions</option>
                        @foreach($directions as $key => $label)
                            <option value="{{ $key }}" {{ request('direction') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500">Period</label>
                    <select name="period" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500">
                        <option value="">All Time</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[11px] font-medium text-slate-500">Search</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by ticket title or ID..."
                               class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-amber-500">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 transition">Filter</button>
                        <a href="{{ route('admin.escalations.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Escalations Table --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800">
            <p class="text-sm font-semibold">{{ $escalations->total() }} escalation{{ $escalations->total() > 1 ? 's' : '' }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Ticket ID</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Jira Key</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Title</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Customer</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Priority</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Previous Team</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Current Team</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Escalated By</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-slate-500">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($escalations as $esc)
                        @php
                            $fromCfg = $levelColors[$esc->from_level] ?? $levelColors['n1'];
                            $toCfg = $levelColors[$esc->to_level] ?? $levelColors['n2'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.ui.tickets.show', $esc->ticket_id) }}" class="text-amber-600 hover:underline font-mono">
                                    #{{ $esc->ticket_id }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $esc->jira_key ?? ($esc->ticket?->jira_ticket_id ?? '—') }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $esc->ticket?->title ?? 'Deleted Ticket' }}</td>
                            <td class="px-4 py-3">{{ $esc->ticket?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium {{ $priorityConfig[$esc->ticket?->priority] ?? '' }}">
                                    {{ ucfirst($esc->ticket?->priority ?? '—') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs">{{ ucfirst(str_replace('_',' ',$esc->ticket?->status ?? '—')) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $fromCfg['bg'] }} {{ $fromCfg['text'] }}">
                                    {{ strtoupper($esc->from_level) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $toCfg['bg'] }} {{ $toCfg['text'] }}">
                                    {{ strtoupper($esc->to_level) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $esc->escalatedBy?->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">{{ $esc->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500 max-w-[200px] truncate" title="{{ $esc->reason }}">{{ $esc->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-16 text-center text-slate-400">
                                <svg class="h-8 w-8 mx-auto mb-2 text-slate-300 dark:text-slate-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                <p class="text-sm font-medium">No escalations found</p>
                                <p class="text-xs mt-1">Try adjusting your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($escalations->hasPages())
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
                {{ $escalations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
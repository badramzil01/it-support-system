@extends('admin.layouts.app')
@section('title', 'Escalation & SLA Reporting')
@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.support-teams.index') }}"
               class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold brand-font">SLA & Escalation Reporting</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Monitor SLA compliance, escalation rates, and team performance</p>
            </div>
        </div>
    </div>

    {{-- SLA Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Tickets</p>
            <p class="text-2xl font-bold mt-1">{{ $slaStats['total_tickets'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">SLA Breached</p>
            <p class="text-2xl font-bold mt-1 text-red-500">{{ $slaStats['total_breached'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">SLA Compliance</p>
            <p class="text-2xl font-bold mt-1 {{ $slaStats['compliance_rate'] >= 90 ? 'text-emerald-500' : ($slaStats['compliance_rate'] >= 70 ? 'text-amber-500' : 'text-red-500') }}">
                {{ $slaStats['compliance_rate'] }}%
            </p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Escalation Rate</p>
            <p class="text-2xl font-bold mt-1 text-amber-500">{{ $slaStats['escalation_rate'] }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Resolved</p>
            <p class="text-2xl font-bold mt-1 text-emerald-500">{{ $slaStats['total_resolved'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Escalated</p>
            <p class="text-2xl font-bold mt-1 text-amber-500">{{ $slaStats['escalated_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Avg Resolution</p>
            <p class="text-2xl font-bold mt-1">{{ $slaStats['avg_resolution_hours'] }}h</p>
        </div>
    </div>

    {{-- Tickets by Support Level --}}
    <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="font-semibold text-sm mb-4">Tickets by Support Level</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @php
                $levelLabels = ['n1' => 'N1 - First Line', 'n2' => 'N2 - Advanced', 'n3' => 'N3 - Expert', 'manager' => 'Support Manager'];
                $levelColors = ['n1' => 'bg-blue-500', 'n2' => 'bg-amber-500', 'n3' => 'bg-red-500', 'manager' => 'bg-purple-500'];
            @endphp
            @foreach($levelLabels as $key => $label)
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-3 text-center">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <span class="w-3 h-3 rounded-full {{ $levelColors[$key] ?? 'bg-slate-400' }}"></span>
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $label }}</span>
                    </div>
                    <p class="text-xl font-bold">{{ $slaStats['by_level'][$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Team Performance --}}
    <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="font-semibold text-sm mb-4">Team Performance</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Team</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Level</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Members</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Tickets</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Resolved</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Res. Rate</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">SLA Comp.</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Avg Time</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Escalated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($teamPerformance as $perf)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                        <td class="px-3 py-2 font-medium">{{ $perf['team'] }}</td>
                        <td class="text-center px-3 py-2">
                            <span class="badge bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                                {{ strtoupper($perf['level']) }}
                            </span>
                        </td>
                        <td class="text-center px-3 py-2">{{ $perf['members_count'] }}</td>
                        <td class="text-center px-3 py-2">{{ $perf['total_tickets'] }}</td>
                        <td class="text-center px-3 py-2">{{ $perf['resolved'] }}</td>
                        <td class="text-center px-3 py-2 font-medium">{{ $perf['resolution_rate'] }}%</td>
                        <td class="text-center px-3 py-2">
                            <span class="font-medium {{ $perf['sla_compliance'] >= 90 ? 'text-emerald-500' : ($perf['sla_compliance'] >= 70 ? 'text-amber-500' : 'text-red-500') }}">
                                {{ $perf['sla_compliance'] }}%
                            </span>
                        </td>
                        <td class="text-center px-3 py-2">{{ $perf['avg_resolution_hours'] }}h</td>
                        <td class="text-center px-3 py-2">{{ $perf['escalated'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">No team data available yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Escalation Logs --}}
    <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="font-semibold text-sm mb-4">Recent Escalation Events</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Date</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Ticket</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">From</th>
                        <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">To</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Previous Owner</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">New Owner</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($recentEscalations as $log)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                        <td class="px-3 py-2 text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.ui.tickets.show', $log->ticket_id) }}" class="text-amber-500 hover:underline font-medium">
                                #{{ $log->ticket_id }}
                            </a>
                            @if($log->ticket)
                                <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $log->ticket->title }}</p>
                            @endif
                        </td>
                        <td class="text-center px-3 py-2">
                            <span class="badge bg-slate-100 dark:bg-slate-800 text-xs">
                                {{ $log->level_name }}
                            </span>
                        </td>
                        <td class="text-center px-3 py-2">
                            @php
                                $nextLevelNames = [0 => 'Initial', 1 => 'N1', 2 => 'N2', 3 => 'N3', 4 => 'Support Manager'];
                                $nextName = $nextLevelNames[$log->to_level] ?? 'Unknown';
                            @endphp
                            <span class="badge bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs">
                                {{ $nextName }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-xs">{{ $log->fromUser?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs">{{ $log->toUser?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs text-slate-500 truncate max-w-[250px]">{{ $log->reason }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">No escalations recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
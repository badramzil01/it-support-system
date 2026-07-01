@extends('admin.layouts.app')
@section('title', 'Support Teams')
@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold brand-font">Support Teams</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage support teams, members and SLA configuration</p>
        </div>
        <a href="{{ route('admin.support-teams.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 shadow-lg shadow-amber-500/30 transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            New Team
        </a>
    </div>

    {{-- Teams Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4">
        @forelse($teams as $team)
        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-5 space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-lg">{{ $team->name }}</h3>
                        @if($team->is_active)
                            <span class="badge bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">Active</span>
                        @else
                            <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $team->description ?? 'No description' }}</p>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('admin.support-teams.edit', $team) }}"
                       class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition"
                       title="Edit team">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.support-teams.destroy', $team) }}"
                          onsubmit="return confirm('Delete this team?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                title="Delete team">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Level Badge --}}
            <div class="flex items-center gap-2">
                <span class="badge bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-semibold px-2.5 py-1">
                    {{ strtoupper($team->support_level) }}
                </span>
                <span class="text-xs text-slate-400">
                    {{ $team->members_count }} member{{ $team->members_count !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- SLA Configuration --}}
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-2">
                    <span class="text-slate-500">Low SLA:</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $team->sla_hours_low }}h</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-2">
                    <span class="text-slate-500">Medium SLA:</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $team->sla_hours_medium }}h</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-2">
                    <span class="text-slate-500">High SLA:</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $team->sla_hours_high }}h</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-2">
                    <span class="text-slate-500">Critical SLA:</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $team->sla_minutes_critical }}min</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-slate-400">
            <svg class="h-12 w-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
            <p class="text-lg font-medium">No support teams yet</p>
            <p class="text-sm mt-1">Create your first team to get started.</p>
        </div>
        @endforelse
    </div>

    {{-- Links --}}
    <div class="flex gap-4 mt-6">
        <a href="{{ route('admin.support-teams.reporting') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 2.75A.75.75 0 011.75 2h16.5a.75.75 0 010 1.5H18v8.75A2.75 2.75 0 0115.25 15h-4.5A2.75 2.75 0 018 12.25V3h-.5a.75.75 0 010-1.5H1.75A.75.75 0 011 2.75zm7.5 0a.25.25 0 00-.25-.25H4.5a.25.25 0 00-.25.25v2.5c0 .138.112.25.25.25h3.75a.25.25 0 00.25-.25v-2.5z" clip-rule="evenodd"/></svg>
            View Reporting
        </a>
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('title', 'Create Support Team')
@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.support-teams.index') }}"
           class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold brand-font">Create Support Team</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Configure a new support team with SLA rules</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.support-teams.store') }}" class="max-w-2xl">
        @csrf

        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-6 space-y-6">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Team Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200"
                       placeholder="e.g., Network Infrastructure Team">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200"
                          placeholder="Describe the team's responsibilities">{{ old('description') }}</textarea>
            </div>

            {{-- Support Level --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Support Level</label>
                <select name="support_level" required
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ old('support_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('support_level') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Active toggle --}}
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-500/50 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
                <span class="text-sm text-slate-600 dark:text-slate-400">Active</span>
            </div>

            {{-- SLA Configuration --}}
            <div>
                <h3 class="font-semibold text-sm text-slate-700 dark:text-slate-300 mb-3">SLA Time Limits</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Low Priority (hours)</label>
                        <input type="number" name="sla_hours_low" value="{{ old('sla_hours_low', 24) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Medium Priority (hours)</label>
                        <input type="number" name="sla_hours_medium" value="{{ old('sla_hours_medium', 8) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">High Priority (hours)</label>
                        <input type="number" name="sla_hours_high" value="{{ old('sla_hours_high', 2) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Critical Priority (minutes)</label>
                        <input type="number" name="sla_minutes_critical" value="{{ old('sla_minutes_critical', 30) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 shadow-lg shadow-amber-500/30 transition">
                    Create Team
                </button>
                <a href="{{ route('admin.support-teams.index') }}"
                   class="px-6 py-2 rounded-lg text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
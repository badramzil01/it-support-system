@extends('admin.layouts.app')
@section('title', 'Edit Support Team')
@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.support-teams.index') }}"
           class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold brand-font">Edit: {{ $supportTeam->name }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Update team configuration and members</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.support-teams.update', $supportTeam) }}" class="max-w-3xl">
        @csrf @method('PUT')

        <div class="bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 p-6 space-y-6">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Team Name</label>
                <input type="text" name="name" value="{{ old('name', $supportTeam->name) }}" required
                       class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">{{ old('description', $supportTeam->description) }}</textarea>
            </div>

            {{-- Support Level --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Support Level</label>
                <select name="support_level" required
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ old('support_level', $supportTeam->support_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Active toggle --}}
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supportTeam->is_active) ? 'checked' : '' }}
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
                        <input type="number" name="sla_hours_low" value="{{ old('sla_hours_low', $supportTeam->sla_hours_low) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Medium Priority (hours)</label>
                        <input type="number" name="sla_hours_medium" value="{{ old('sla_hours_medium', $supportTeam->sla_hours_medium) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">High Priority (hours)</label>
                        <input type="number" name="sla_hours_high" value="{{ old('sla_hours_high', $supportTeam->sla_hours_high) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Critical Priority (minutes)</label>
                        <input type="number" name="sla_minutes_critical" value="{{ old('sla_minutes_critical', $supportTeam->sla_minutes_critical) }}" required min="1"
                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                    </div>
                </div>
            </div>

            {{-- Members --}}
            <div>
                <h3 class="font-semibold text-sm text-slate-700 dark:text-slate-300 mb-3">Team Members</h3>
                <p class="text-xs text-slate-500 mb-3">Select users to add to this team. Check the "Leader" column to designate team leaders.</p>
                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50">
                                <th class="text-left px-3 py-2 text-xs font-medium text-slate-500">Member</th>
                                <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">In Team</th>
                                <th class="text-center px-3 py-2 text-xs font-medium text-slate-500">Leader</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($allUsers as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-red-600 text-[10px] font-bold text-white">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center px-3 py-2">
                                    <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                           {{ in_array($user->id, $teamMemberIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 dark:border-slate-600 text-amber-500 focus:ring-amber-500/50 member-checkbox">
                                </td>
                                <td class="text-center px-3 py-2">
                                    <input type="checkbox" name="leaders[]" value="{{ $user->id }}"
                                           {{ in_array($user->id, $teamLeaderIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 dark:border-slate-600 text-amber-500 focus:ring-amber-500/50 leader-checkbox">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 shadow-lg shadow-amber-500/30 transition">
                    Update Team
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-disable leader checkbox when member is unchecked
    document.querySelectorAll('.member-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const row = this.closest('tr');
            const leaderCb = row.querySelector('.leader-checkbox');
            if (!this.checked && leaderCb) {
                leaderCb.checked = false;
                leaderCb.disabled = true;
            } else if (leaderCb) {
                leaderCb.disabled = false;
            }
        });
        // Initial state
        const row = cb.closest('tr');
        const leaderCb = row.querySelector('.leader-checkbox');
        if (leaderCb && !cb.checked) {
            leaderCb.disabled = true;
        }
    });
});
</script>
@endpush
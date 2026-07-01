@extends('admin.layouts.app')
@section('title', 'Ticket #'.$ticket->id)
@php use Illuminate\Support\Str; @endphp
@section('content')

@php
$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950', 'text' => 'text-red-700 dark:text-red-300', 'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-950', 'text' => 'text-blue-700 dark:text-blue-300'],
    'in_progress' => ['bg' => 'bg-violet-100 dark:bg-violet-950', 'text' => 'text-violet-700 dark:text-violet-300'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950', 'text' => 'text-emerald-700 dark:text-emerald-300'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-500 dark:text-slate-400'],
];
$levelColors = [
    'n1' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-700 dark:text-blue-300', 'border' => 'border-blue-300 dark:border-blue-700'],
    'n2' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-700 dark:text-amber-300', 'border' => 'border-amber-300 dark:border-amber-700'],
    'n3' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-700 dark:text-red-300', 'border' => 'border-red-300 dark:border-red-700'],
    'manager' => ['bg' => 'bg-purple-100 dark:bg-purple-900/40', 'text' => 'text-purple-700 dark:text-purple-300', 'border' => 'border-purple-300 dark:border-purple-700'],
];
$pCfg = $priorityConfig[$ticket->priority] ?? $priorityConfig['low'];
$sCfg = $statusConfig[$ticket->status] ?? $statusConfig['closed'];
@endphp

<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.ui.tickets.index') }}" class="hover:text-amber-600 dark:hover:text-amber-400">← Retour aux tickets</a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Main Content --}}
        <div class="xl:col-span-2 space-y-4">
            {{-- Ticket Header --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-mono text-slate-400 mb-1">#{{ $ticket->id }}</p>
                        <h1 class="brand-font text-2xl font-bold text-slate-900 dark:text-white">{{ $ticket->title }}</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Créé {{ $ticket->created_at->diffForHumans() }} · {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $pCfg['dot'] }}"></span>{{ ucfirst($ticket->priority ?? 'low') }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">Utilisateur</p>
                        <p class="text-sm font-semibold mt-1">{{ optional($ticket->user)->name ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">Catégorie</p>
                        <p class="text-sm font-semibold mt-1">{{ $ticket->category ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">Support Level</p>
                        <p class="text-sm font-semibold mt-1">
                            @php $lCfg = $levelColors[$ticket->support_level] ?? $levelColors['n1']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $lCfg['bg'] }} {{ $lCfg['text'] }}">
                                {{ strtoupper($ticket->support_level ?? 'N1') }}
                            </span>
                        </p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">Assigné à</p>
                        <p class="text-sm font-semibold mt-1">{{ optional($ticket->assignedAgent)->name ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">Jira</p>
                        <p class="text-sm font-semibold mt-1">{{ $ticket->jira_ticket_id ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3">
                        <p class="text-[10px] uppercase text-slate-500">SLA Deadline</p>
                        <p class="text-sm font-semibold mt-1">
                            @if($ticket->sla_deadline)
                                <span class="{{ $ticket->sla_deadline->isPast() ? 'text-red-500' : 'text-emerald-500' }}">
                                    {{ $ticket->sla_deadline->format('d/m/Y H:i') }}
                                    @if($ticket->sla_breached)
                                        <span class="text-red-500 ml-1">(Breached)</span>
                                    @elseif($ticket->sla_deadline->isPast())
                                        <span class="text-red-500 ml-1">(Overdue)</span>
                                    @else
                                        <span class="text-slate-400 ml-1">({{ $ticket->sla_deadline->diffForHumans() }})</span>
                                    @endif
                                </span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Description</p>
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        {{ $ticket->description ?: 'Aucune description' }}
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                <h2 class="font-semibold text-sm mb-4">Actions</h2>
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Status Update --}}
                    <form method="POST" action="{{ route('admin.ui.tickets.updateStatus', $ticket) }}" class="flex items-center gap-2">
                        @csrf
                        <label class="text-xs font-medium text-slate-500">Status:</label>
                        <select name="status" onchange="this.form.submit()"
                                class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 px-2 text-xs outline-none focus:border-amber-500 cursor-pointer">
                            <option value="open" {{ $ticket->status=='open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status=='closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </form>

                    {{-- Manual Escalate --}}
                    <button type="button" onclick="openEscalateModal({{ $ticket->id }}, '{{ $ticket->support_level ?? 'n1' }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 dark:border-amber-800 px-3 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                        Escalate
                    </button>

                    {{-- Reassign --}}
                    <form method="POST" action="{{ route('admin.ui.tickets.reassign', $ticket) }}" class="flex items-center gap-2">
                        @csrf
                        <label class="text-xs font-medium text-slate-500">Reassign to:</label>
                        <select name="assigned_to" onchange="this.form.submit()"
                                class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 px-2 text-xs outline-none focus:border-amber-500 cursor-pointer">
                            <option value="">Select user...</option>
                            @foreach($supportUsers as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="reason" value="Reassigned from ticket page">
                    </form>

                    {{-- Assign to me --}}
                    <form method="POST" action="{{ route('admin.ui.tickets.assignToMe', $ticket) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                            Assign to me
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: Escalation Timeline --}}
        <div class="space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                <h2 class="font-semibold text-sm mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    Escalation Timeline
                </h2>

                @if($escalationHistory->count() > 0)
                    <div class="space-y-0">
                        {{-- Initial assignment --}}
                        <div class="relative pl-6 pb-4 border-l-2 border-slate-200 dark:border-slate-700">
                            <div class="absolute left-0 -translate-x-1/2 w-3 h-3 rounded-full bg-slate-400 dark:bg-slate-600 mt-1"></div>
                            <p class="text-xs text-slate-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Ticket Created</p>
                            <p class="text-xs text-slate-400">Assigned to {{ optional($ticket->user)->name ?? 'N/A' }}</p>
                        </div>

                        @foreach($escalationHistory as $log)
                            @php
                                $lCfg = $levelColors[$log->to_level] ?? $levelColors['n1'];
                                $fromLabel = strtoupper($log->from_level);
                                $toLabel = strtoupper($log->to_level);
                            @endphp
                            <div class="relative pl-6 pb-4 border-l-2 {{ $loop->last ? 'border-transparent' : 'border-slate-200 dark:border-slate-700' }}">
                                <div class="absolute left-0 -translate-x-1/2 w-3 h-3 rounded-full {{ $lCfg['bg'] }} {{ $lCfg['border'] }} border-2 mt-1"></div>
                                <p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Escalated: {{ $fromLabel }} → {{ $toLabel }}
                                </p>
                                <p class="text-xs text-slate-500">By: {{ $log->escalatedBy?->name ?? 'System' }}</p>
                                @if($log->assignedTo)
                                    <p class="text-xs text-slate-500">To: {{ $log->assignedTo->name }}</p>
                                @endif
                                <div class="mt-1 text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 rounded-lg px-2 py-1">
                                    {{ $log->reason }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="h-8 w-8 mx-auto text-slate-300 dark:text-slate-600 mb-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <p class="text-sm text-slate-500">No escalation history</p>
                        <p class="text-xs text-slate-400 mt-1">This ticket has not been escalated yet.</p>
                    </div>
                @endif
            </div>

            {{-- Ticket Details Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                <h2 class="font-semibold text-sm mb-4">Ticket Info</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Source:</span>
                        <span class="font-medium">{{ $ticket->source ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Escalated:</span>
                        <span class="font-medium {{ $ticket->is_escalated ? 'text-amber-500' : 'text-slate-400' }}">{{ $ticket->is_escalated ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">SLA Breached:</span>
                        <span class="font-medium {{ $ticket->sla_breached ? 'text-red-500' : 'text-emerald-500' }}">{{ $ticket->sla_breached ? 'Yes' : 'No' }}</span>
                    </div>
                    @if($ticket->escalation_reason)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Last Reason:</span>
                        <span class="font-medium text-right max-w-[150px]">{{ Str::limit($ticket->escalation_reason, 50) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Assigned Team:</span>
                        <span class="font-medium">{{ $ticket->assigned_team ? str_replace('-', ' ', ucwords(str_replace('-', ' ', $ticket->assigned_team))) : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Escalate Modal --}}
<div id="escalateModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEscalateModal()"></div>
    <div class="relative bg-white dark:bg-[#16181D] rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg mx-4 p-6 z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold brand-font">Escalate Ticket #{{ $ticket->id }}</h3>
            <button onclick="closeEscalateModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>

        <form id="escalateForm" method="POST" action="{{ route('admin.ui.tickets.escalate', $ticket) }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Current Level</label>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-lg">{{ strtoupper($ticket->support_level ?? 'N1') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Target Level</label>
                    <select name="to_level" required
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200">
                        <option value="n1">N1 - First Line Support</option>
                        <option value="n2">N2 - Advanced Technical Support</option>
                        <option value="n3">N3 - Expert Support</option>
                        <option value="manager">Support Manager</option>
                    </select>
                </div>

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

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="escalateReason" rows="3" required minlength="5"
                              class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 dark:text-slate-200"
                              placeholder="e.g., Complex network issue, requires database expertise..."></textarea>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-500 mb-2">Quick reasons:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="document.getElementById('escalateReason').value='Complex network issue'" class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Complex network issue</button>
                        <button type="button" onclick="document.getElementById('escalateReason').value='Requires database expertise'" class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Requires database expertise</button>
                        <button type="button" onclick="document.getElementById('escalateReason').value='Security investigation'" class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Security investigation</button>
                        <button type="button" onclick="document.getElementById('escalateReason').value='Infrastructure problem'" class="px-2 py-1 text-[10px] rounded-full border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Infrastructure problem</button>
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
    document.getElementById('escalateModal').classList.remove('hidden');
}
function closeEscalateModal() {
    document.getElementById('escalateModal').classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEscalateModal();
});
</script>
@endpush
@endsection
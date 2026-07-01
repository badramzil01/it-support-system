@extends('client.layout')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Bienvenue dans votre espace de support informatique')

@push('styles')
<style>
    .timeline-line::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 24px;
        bottom: -8px;
        width: 2px;
        background: #e5e7eb;
    }
    .dark .timeline-line::before {
        background: #374151;
    }
    .timeline-line:last-child::before {
        display: none;
    }
    .chart-bar {
        transition: height 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
@php
$greeting = now()->format('H') < 12 ? 'Bonjour' : (now()->format('H') < 18 ? 'Bon après-midi' : 'Bonsoir');
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300', 'dot' => 'bg-blue-500', 'label' => 'Open'],
    'in_progress' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500', 'label' => 'In Progress'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-300', 'dot' => 'bg-emerald-500', 'label' => 'Resolved'],
    'closed'      => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-500 dark:text-gray-400', 'dot' => 'bg-gray-400', 'label' => 'Closed'],
];
$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-300'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300'],
    'low'      => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-300'],
];
$months = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
$totalTickets = $stats['total'] ?: 1;
$resolutionRate = $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100) : 0;
@endphp

{{-- Welcome Section --}}
<div class="mb-6 animate-fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                {{ $greeting }} {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Bienvenue dans votre espace de support informatique.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                Dernière connexion : {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'Première connexion' }}
            </span>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 animate-slide-up">
    {{-- Total Tickets --}}
    <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 md:p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</span>
        </div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mes Tickets</p>
        <p class="text-xs text-gray-400 mt-0.5">Total de vos tickets</p>
    </div>

    {{-- Resolved --}}
    <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 md:p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-2xl md:text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['resolved'] }}</span>
        </div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Résolus</p>
        <p class="text-xs text-gray-400 mt-0.5">Tickets terminés</p>
    </div>

    {{-- In Progress --}}
    <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 md:p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-2xl md:text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['in_progress'] }}</span>
        </div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">En cours</p>
        <p class="text-xs text-gray-400 mt-0.5">En traitement</p>
    </div>

    {{-- Escalated --}}
    <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 md:p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <span class="text-2xl md:text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['escalated'] }}</span>
        </div>
        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Escaladés</p>
        <p class="text-xs text-gray-400 mt-0.5">Niveau supérieur</p>
    </div>
</div>

{{-- Quick Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 animate-slide-up">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $aiResponseCount }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Conversations IA</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $resolutionRate }}%</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tickets Résolus</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $stats['open'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">En attente</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
        <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $suggestions->count() }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solutions disponibles</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 animate-slide-up">
    <a href="{{ route('chat.index') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-lg transition-all duration-200 group">
        <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">💬 Ouvrir Chat IA</p>
            <p class="text-xs text-gray-400 mt-0.5">Posez vos questions à l'IA</p>
        </div>
    </a>

    <a href="{{ route('chat.index') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-lg transition-all duration-200 group">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">➕ Nouveau Ticket</p>
            <p class="text-xs text-gray-400 mt-0.5">Créez un ticket via le chat</p>
        </div>
    </a>

    <a href="{{ route('chat.index') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-lg transition-all duration-200 group">
        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">📞 Contacter Support</p>
            <p class="text-xs text-gray-400 mt-0.5">Parlez à un agent</p>
        </div>
    </a>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Recent Tickets Table --}}
    <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden animate-slide-up">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Mes Tickets Récents
            </h3>
            @if($recentTickets->count() > 0)
                <span class="text-[11px] font-semibold text-gray-400">{{ $recentTickets->count() }} tickets</span>
            @endif
        </div>
        @if($recentTickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500">ID</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Titre</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell">Catégorie</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Priorité</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Statut</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($recentTickets as $t)
                    @php
                        $pCfg = $priorityConfig[$t->priority] ?? $priorityConfig['low'];
                        $sCfg = $statusConfig[$t->status] ?? $statusConfig['open'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <span class="text-xs font-mono text-gray-400">#{{ $t->id }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-[200px]">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-200 line-clamp-1">{{ $t->title }}</span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="text-xs text-gray-500">{{ $t->category ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">
                                {{ ucfirst($t->priority ?? 'low') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sCfg['dot'] }}"></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">
                                    {{ $sCfg['label'] }}
                                </span>
                                @if($t->is_escalated)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Esc</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-xs text-gray-400">{{ $t->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-4 py-3">
                        <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-semibold bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                                Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm font-medium">Aucun ticket pour le moment</p>
            <p class="text-xs mt-1">Utilisez le chat IA pour créer votre premier ticket</p>
        </div>
        @endif
    </div>

    {{-- Right Column --}}
    <div class="space-y-6">
        {{-- Activity Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 animate-slide-up">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Activité Récente
            </h3>
            @if($latestActivity->count() > 0)
            <div class="space-y-0">
                @foreach($latestActivity as $act)
                @php $actCfg = $statusConfig[$act->status] ?? $statusConfig['open']; @endphp
                <div class="relative flex items-start gap-3 pb-4 timeline-line">
                    <div class="relative z-10 mt-1">
                        <div class="w-5 h-5 rounded-full {{ $actCfg['dot'] }} flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                            @if($act->status === 'resolved' || $act->status === 'closed')
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ $act->title }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold {{ $actCfg['bg'] }} {{ $actCfg['text'] }}">{{ $actCfg['label'] }}</span>
                            <span class="text-[10px] text-gray-400">{{ $act->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <p class="text-xs font-medium">Aucune activité récente</p>
            </div>
            @endif
        </div>

        {{-- Monthly Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 animate-slide-up">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4">Tickets par mois</h3>
            @if(array_sum($monthlyData) > 0)
            <div class="flex items-end gap-1.5 h-24">
                @foreach($monthlyData as $i => $count)
                @php
                    $max = max($monthlyData) ?: 1;
                    $h = max(3, round(($count / $max) * 100));
                    $barColors = ['from-blue-400 to-blue-600','from-emerald-400 to-emerald-600','from-amber-400 to-amber-600','from-red-400 to-red-600','from-purple-400 to-purple-600','from-cyan-400 to-cyan-600','from-pink-400 to-pink-600','from-orange-400 to-orange-600','from-teal-400 to-teal-600','from-indigo-400 to-indigo-600','from-rose-400 to-rose-600','from-violet-400 to-violet-600'];
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[9px] font-semibold text-gray-400">{{ $count }}</span>
                    <div class="w-full rounded-sm bg-gradient-to-t {{ $barColors[$i] }} chart-bar transition-all hover:opacity-80 cursor-pointer" style="height: {{ $h }}%"></div>
                    <span class="text-[8px] text-gray-400">{{ $months[$i] }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex items-center justify-center h-24 text-gray-400 text-xs">Aucune donnée cette année</div>
            @endif
        </div>
    </div>
</div>
@endsection

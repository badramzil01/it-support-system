@extends('parts.base')

@section('content')
@php
$greeting = now()->format('H') < 12 ? 'Bonjour' : (now()->format('H') < 18 ? 'Bon après-midi' : 'Bonsoir');
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300', 'label' => 'Ouvert'],
    'in_progress' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300', 'label' => 'En cours'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-300', 'label' => 'Résolu'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-500 dark:text-slate-400', 'label' => 'Fermé'],
];
$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-300'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300'],
];
@endphp

{{-- 🎉 Welcome Banner --}}
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $greeting }} {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bienvenue sur votre espace support informatique.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/chat" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-indigo-500/20">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg>
                Ouvrir le Chat IA
            </a>
        </div>
    </div>
</div>

{{-- 📊 Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z" clip-rule="evenodd"/></svg>
            </div>
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['open'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tickets ouverts</p>
        <p class="text-xs text-slate-400 mt-0.5">En attente de traitement</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm1-12a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l2.828 2.829a1 1 0 1 0 1.415-1.415L11 9.586V6Z"/></svg>
            </div>
            <span class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['in_progress'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">En cours</p>
        <p class="text-xs text-slate-400 mt-0.5">Pris en charge par le support</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
            </div>
            <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['resolved'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Résolus</p>
        <p class="text-xs text-slate-400 mt-0.5">Tickets terminés avec succès</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
            </div>
            <span class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['escalated'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Escaladés</p>
        <p class="text-xs text-slate-400 mt-0.5">Nécessitent une expertise avancée</p>
    </div>
</div>

{{-- 🚀 Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    <a href="/chat" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-md transition-all group">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg>
        </div>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Chat IA</span>
    </a>
    <a href="#" onclick="alert('Fonctionnalité à venir : Nouveau ticket')" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-md transition-all group">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
        </div>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nouveau ticket</span>
    </a>
    <a href="#" onclick="alert('Fonctionnalité à venir : Base de connaissances')" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-md transition-all group">
        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/></svg>
        </div>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Base de connaissances</span>
    </a>
    <a href="#" onclick="alert('Fonctionnalité à venir : Contacter support')" class="flex flex-col items-center gap-2 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-700 hover:shadow-md transition-all group">
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor"><path d="M3.5 2A1.5 1.5 0 0 0 2 3.5V15c0 1.1.9 2 2 2H3.5a1.5 1.5 0 0 0 1.5 1.5h2a1.5 1.5 0 0 0 1.5-1.5H15a1.5 1.5 0 0 0 1.5-1.5V3.5A1.5 1.5 0 0 0 15 2H3.5Z"/></svg>
        </div>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Contacter support</span>
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    {{-- 📋 My Recent Tickets --}}
    <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">🆕 Mes tickets récents</h3>
            <a href="/equipeIT/ui/tickets" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">Voir tout →</a>
        </div>
        @if($recentTickets->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">ID</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Titre</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Catégorie</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Priorité</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Statut</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Date</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($recentTickets as $t)
                    @php
                        $pCfg = $priorityConfig[$t->priority] ?? $priorityConfig['low'];
                        $sCfg = $statusConfig[$t->status] ?? $statusConfig['open'];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                        <td class="px-4 py-3 text-xs font-mono text-slate-400">#{{ $t->id }}</td>
                        <td class="px-4 py-3 max-w-[200px]">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200 line-clamp-1">{{ $t->title }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $t->category ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $pCfg['bg'] }} {{ $pCfg['text'] }}">{{ ucfirst($t->priority ?? 'low') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $sCfg['bg'] }} {{ $sCfg['text'] }}">{{ $sCfg['label'] }}</span>
                            @if($t->is_escalated)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 ml-1">Escaladé</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-400">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="#" onclick="alert('Voir ticket #{{ $t->id }}')" class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1.5 text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition">Voir</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-slate-400">
            <p class="text-sm font-medium">Aucun ticket pour le moment</p>
            <p class="text-xs mt-1">Utilisez le chat IA pour créer votre premier ticket</p>
        </div>
        @endif
    </div>

    {{-- 💬 Recent Conversations --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">💬 Conversations récentes</h3>
            <a href="/chat" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">Voir tout →</a>
        </div>
        @if($recentConversations->count())
        <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
            @foreach($recentConversations as $conv)
            <a href="/chat" class="flex items-start gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition group">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $conv->title ?? 'Conversation' }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $conv->latestMessage?->content ? \Illuminate\Support\Str::limit($conv->latestMessage->content, 50) : 'Aucun message' }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $conv->updated_at->diffForHumans() }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition shrink-0 mt-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-slate-400">
            <p class="text-sm font-medium">Aucune conversation</p>
            <p class="text-xs mt-1">Commencez une conversation via le chat IA</p>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- 📈 Monthly Chart (bar graph simplified) --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">📈 Tickets par mois ({{ now()->year }})</h3>
        @if(array_sum($monthlyData) > 0)
        <div class="flex items-end gap-1.5 h-32">
            @foreach($monthlyData as $i => $count)
            @php
                $max = max($monthlyData) ?: 1;
                $h = max(4, round(($count / $max) * 100));
                $months = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
                $colors = ['bg-indigo-500','bg-blue-500','bg-cyan-500','bg-teal-500','bg-emerald-500','bg-green-500','bg-amber-500','bg-orange-500','bg-red-500','bg-rose-500','bg-pink-500','bg-purple-500'];
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-[10px] font-semibold text-slate-500">{{ $count }}</span>
                <div class="w-full rounded-md {{ $colors[$i] }} transition-all hover:opacity-80" style="height: {{ $h }}%"></div>
                <span class="text-[9px] text-slate-400">{{ $months[$i] }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex items-center justify-center h-32 text-slate-400 text-sm">Aucune donnée cette année</div>
        @endif
    </div>

    {{-- 📚 Suggestions & Activity --}}
    <div class="space-y-6">
        {{-- 🧠 AI Suggestions --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">🧠 Suggestions de la base de connaissances</h3>
            @if($suggestions->count())
            <div class="space-y-2">
                @foreach($suggestions as $s)
                <a href="#" onclick="alert('{{ addslashes($s->problem_keywords) }}')" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/30 transition group">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $s->problem_keywords }}</p>
                        @if($s->category)<p class="text-[10px] text-slate-400">{{ $s->category }}</p>@endif
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-4">Aucune suggestion pour le moment</p>
            @endif
        </div>

        {{-- ⏱ Latest Activity --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">⏱ Dernière activité</h3>
                <span class="text-[10px] font-semibold text-slate-400">{{ $aiResponseCount }} réponses IA</span>
            </div>
            @if($latestActivity->count())
            <div class="space-y-2">
                @foreach($latestActivity as $act)
                @php $actCfg = $statusConfig[$act->status] ?? $statusConfig['open']; @endphp
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <div class="w-2 h-2 rounded-full {{ $act->status === 'resolved' || $act->status === 'closed' ? 'bg-emerald-500' : ($act->status === 'in_progress' ? 'bg-amber-500' : 'bg-blue-500') }}"></div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">#{{ $act->id }} {{ $act->title }}</p>
                        <p class="text-[10px] text-slate-400">{{ $act->updated_at->diffForHumans() }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold {{ $actCfg['bg'] }} {{ $actCfg['text'] }}">{{ $actCfg['label'] }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-4">Aucune activité récente</p>
            @endif
        </div>
    </div>
</div>
@endsection
@php use Carbon\Carbon; @endphp
@extends('parts.base')

@section('title', 'Ticket #'.$ticket->id.' - N1 - Support IT')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('support-n1.dashboard') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ticket #{{ $ticket->id }}</h1>
                @php
                    $statusColors = ['open' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'pending' => 'bg-gray-100 text-gray-800', 'resolved' => 'bg-green-100 text-green-800', 'closed' => 'bg-gray-100 text-gray-800'];
                    $sColor = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ $sColor }}">
                    {{ str_replace(['open', 'in_progress', 'pending', 'resolved', 'closed'], ['Ouvert', 'En cours', 'En attente', 'Résolu', 'Fermé'], $ticket->status) }}
                </span>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">N1</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Détails du ticket --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $ticket->title }}</h2>
                <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-300">
                    {{ nl2br(e($ticket->description ?? 'Aucune description')) }}
                </div>
                @if($ticket->jira_ticket_id)
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Jira : {{ $ticket->jira_ticket_id }}</span>
                </div>
                @endif
            </div>

            {{-- Messages/Conversation --}}
            @if($ticket->conversation)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">💬 Conversation</h3>
                <a href="{{ route('chat.index') }}?conversation={{ $ticket->conversation_id }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    Voir la conversation complète →
                </a>
            </div>
            @endif

            {{-- Historique des escalades --}}
            @if($ticket->escalationHistory->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📋 Historique des escalades</h3>
                <div class="space-y-3">
                    @foreach($ticket->escalationHistory as $history)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-sm font-medium text-indigo-600 dark:text-indigo-300">
                            {{ strtoupper(substr($history->escalatedBy?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $history->escalatedBy?->name ?? 'Inconnu' }}
                                <span class="text-gray-500 font-normal">a escaladé de</span>
                                <span class="font-semibold">{{ $history->from_level }}</span>
                                <span class="text-gray-500 font-normal">vers</span>
                                <span class="font-semibold">{{ $history->to_level }}</span>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $history->reason }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Colonne latérale --}}
        <div class="space-y-6">
            {{-- Informations --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">ℹ️ Informations</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créé par</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->user?->name ?? 'Inconnu' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $ticket->user?->email ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Priorité</dt>
                        <dd class="mt-1">
                            @php
                                $pColors = ['critical' => 'text-red-600', 'high' => 'text-orange-600', 'medium' => 'text-blue-600', 'low' => 'text-gray-600'];
                            @endphp
                            <span class="text-sm font-semibold {{ $pColors[$ticket->priority] ?? 'text-gray-600' }}">{{ ucfirst($ticket->priority) }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Catégorie</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $ticket->category ?? 'Non catégorisé' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Créé le</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($ticket->assignedAgent)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Assigné à</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $ticket->assignedAgent->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Actions N1 --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⚡ Actions</h3>
                <div class="space-y-3">
                    {{-- Changer statut --}}
                    @if(!in_array($ticket->status, ['resolved', 'closed']))
                    <form method="POST" action="{{ route('support-n1.update-status', $ticket) }}" class="space-y-2">
                        @csrf
                        <select name="status" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="resolved">✅ Résoudre</option>
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                            Mettre à jour
                        </button>
                    </form>
                    @endif

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Escalader vers N2 --}}
                    @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                    <button onclick="document.getElementById('escalateModal').classList.remove('hidden')"
                        class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                        ⬆️ Escalader vers N2
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal d'escalade --}}
<div id="escalateModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('escalateModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="{{ route('support-n1.escalate', $ticket) }}">
                @csrf
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Escalader vers N2</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Expliquez pourquoi ce ticket doit être escaladé au niveau 2.</p>
                            </div>
                            <div class="mt-4">
                                <textarea name="reason" rows="4" required
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    placeholder="Raison de l'escalade..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                        ⬆️ Escalader
                    </button>
                    <button type="button" onclick="document.getElementById('escalateModal').classList.add('hidden')"
                        class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@php use Carbon\Carbon; @endphp
@extends('parts.base')

@section('title', 'Tableau de Bord N1 - Support IT')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ filterOpen: false }">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🎫 Tickets N1</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tickets attribués au support de niveau 1
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <button @click="filterOpen = !filterOpen"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtres
            </button>
            <a href="{{ route('support.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                ← Retour
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-lg bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total N1</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-lg bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgents</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['urgent'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En cours</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-lg bg-gray-100 dark:bg-gray-900">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">En attente</p>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div x-show="filterOpen" x-cloak class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('support-n1.dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                <select name="status" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Tous</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priorité</label>
                <select name="priority" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Toutes</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critique</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, description..."
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Tableau des tickets --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mes Tickets N1</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priorité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Créé le</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ $ticket->id }}</span>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400 truncate max-w-[200px]">{{ Str::limit($ticket->title, 50) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-600 dark:text-indigo-300">{{ strtoupper(substr($ticket->user?->name ?? '?', 0, 2)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->user?->name ?? 'Inconnu' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->user?->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $priorityColors = ['critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300', 'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300', 'medium' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', 'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'];
                                $color = $priorityColors[$ticket->priority] ?? $priorityColors['medium'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = ['open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300', 'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', 'pending' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', 'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', 'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'];
                                $color = $statusColors[$ticket->status] ?? $statusColors['open'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ str_replace(['open', 'in_progress', 'pending', 'resolved', 'closed'], ['Ouvert', 'En cours', 'En attente', 'Résolu', 'Fermé'], $ticket->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('support-n1.show', $ticket) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                    Voir
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-2 text-sm font-medium">Aucun ticket N1 pour le moment</p>
                                <p class="mt-1 text-xs">Tous les tickets sont traités ou résolus.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
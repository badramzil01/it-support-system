@extends('parts.base')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-3xl font-bold">
                Ticket #{{ $ticket->id }}
            </h1>

            <p class="text-gray-500">
                Créé le {{ $ticket->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ route('support.tickets.index') }}"
           class="bg-gray-700 text-white px-4 py-2 rounded-lg">
            Retour
        </a>

    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Informations Ticket --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-6">
            Informations Ticket
        </h2>

        <div class="grid md:grid-cols-2 gap-6">

            <div>
                <strong>Titre :</strong>
                <p>{{ $ticket->title }}</p>
            </div>

            <div>
                <strong>Statut :</strong>
                <p>{{ strtoupper($ticket->status) }}</p>
            </div>

            <div>
                <strong>Catégorie :</strong>
                <p>{{ $ticket->category }}</p>
            </div>

            <div>
                <strong>Priorité :</strong>
                <p>{{ strtoupper($ticket->priority) }}</p>
            </div>

            <div>
                <strong>Escalade :</strong>

                @if($ticket->is_escalated)
                    <span class="text-red-600 font-bold">Oui</span>
                @else
                    <span class="text-green-600 font-bold">Non</span>
                @endif

            </div>

            <div>
                <strong>Jira Ticket :</strong>
                <p>{{ $ticket->jira_ticket_id ?? 'Non créé' }}</p>
            </div>

        </div>

    </div>

    {{-- Utilisateur --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-6">
            Utilisateur
        </h2>

        <div class="grid md:grid-cols-2 gap-6">

            <div>
                <strong>Nom :</strong>
                <p>{{ $ticket->user?->name ?? 'Inconnu' }}</p>
            </div>

            <div>
                <strong>Email :</strong>
                <p>{{ $ticket->user?->email ?? 'Inconnu' }}</p>
            </div>

        </div>

    </div>

    {{-- Description --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4">
            Description
        </h2>

        <div class="bg-gray-50 p-4 rounded-lg">
            {{ $ticket->description }}
        </div>

    </div>

    {{-- Analyse IA --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4">
            Analyse IA
        </h2>

        <div class="grid md:grid-cols-2 gap-6">

            <div>
                <strong>Confiance :</strong>
                <p>{{ $ticket->confidence ?? 0 }} %</p>
            </div>

            <div>
                <strong>Source :</strong>
                <p>{{ $ticket->source ?? 'IA' }}</p>
            </div>

        </div>

        <div class="mt-6">

            <strong>Solution proposée :</strong>

            <div class="mt-2 p-4 bg-indigo-50 rounded-lg">
                {{ $ticket->solution ?? 'Aucune solution générée.' }}
            </div>

        </div>

    </div>

    {{-- Gestion Ticket --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4">
            Gestion du ticket
        </h2>

        <form method="POST"
              action="{{ route('support.tickets.status', $ticket->id) }}">

            @csrf
            @method('PUT')

            <div class="flex gap-4">

                <select name="status"
                        class="border rounded-lg px-4 py-2">

                    <option value="open"
                        {{ $ticket->status == 'open' ? 'selected' : '' }}>
                        Ouvert
                    </option>

                    <option value="in_progress"
                        {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>
                        En cours
                    </option>

                    <option value="resolved"
                        {{ $ticket->status == 'resolved' ? 'selected' : '' }}>
                        Résolu
                    </option>

                    <option value="closed"
                        {{ $ticket->status == 'closed' ? 'selected' : '' }}>
                        Fermé
                    </option>

                </select>

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                    Mettre à jour
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
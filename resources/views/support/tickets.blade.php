@extends('parts.base')

@section('content')

<div class="space-y-6">

```
<div class="flex items-center justify-between">
    <h1 class="text-3xl font-bold text-gray-800">
        Gestion des Tickets
    </h1>
</div>

<form method="GET"
      action="{{ route('support.tickets.index') }}"
      class="bg-white rounded-xl shadow p-4">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Rechercher un ticket..."
            class="border rounded-lg p-2">

        <select
            name="status"
            class="border rounded-lg p-2">

            <option value="">Tous les statuts</option>
            <option value="open">Ouvert</option>
            <option value="in_progress">En cours</option>
            <option value="resolved">Résolu</option>

        </select>

        <select
            name="priority"
            class="border rounded-lg p-2">

            <option value="">Toutes priorités</option>
            <option value="low">Faible</option>
            <option value="medium">Moyenne</option>
            <option value="high">Haute</option>
            <option value="critical">Critique</option>

        </select>

        <button
            type="submit"
            class="bg-indigo-600 text-white rounded-lg px-4 py-2">

            Filtrer

        </button>

    </div>

</form>

<div class="bg-white rounded-xl shadow overflow-hidden">

    <div class="p-6 border-b">
        <h2 class="text-xl font-semibold">
            Liste des Tickets
        </h2>
    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-gray-100">

            <tr>

                <th class="p-4 text-left">#</th>
                <th class="p-4 text-left">Titre</th>
                <th class="p-4 text-left">Jira</th>
                <th class="p-4 text-left">Catégorie</th>
                <th class="p-4 text-left">Priorité</th>
                <th class="p-4 text-left">Statut</th>
                <th class="p-4 text-left">IA</th>
                <th class="p-4 text-left">Escalade</th>
                <th class="p-4 text-left">Date</th>
                <th class="p-4 text-center">Action</th>

            </tr>

            </thead>

            <tbody>

            @forelse($tickets as $ticket)

                <tr class="border-b hover:bg-gray-50">

                    <td class="p-4">
                        #{{ $ticket->id }}
                    </td>

                    <td class="p-4 font-medium">
                        {{ $ticket->title }}
                    </td>

                    <td class="p-4">
                        {{ $ticket->jira_ticket_id ?? '-' }}
                    </td>

                    <td class="p-4">
                        {{ $ticket->category }}
                    </td>

                    <td class="p-4">

                        @if($ticket->priority == 'critical')
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded">
                                Critique
                            </span>

                        @elseif($ticket->priority == 'high')
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded">
                                Haute
                            </span>

                        @elseif($ticket->priority == 'medium')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                Moyenne
                            </span>

                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">
                                Faible
                            </span>
                        @endif

                    </td>

                    <td class="p-4">

                        @if($ticket->status == 'open')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                Ouvert
                            </span>

                        @elseif($ticket->status == 'in_progress')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                                En cours
                            </span>

                        @elseif($ticket->status == 'resolved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">
                                Résolu
                            </span>

                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                {{ $ticket->status }}
                            </span>
                        @endif

                    </td>

                    <td class="p-4">
                        {{ $ticket->confidence }}%
                    </td>

                    <td class="p-4">

                        @if($ticket->is_escalated)
                            <span class="text-red-600 font-semibold">
                                Oui
                            </span>
                        @else
                            <span class="text-green-600">
                                Non
                            </span>
                        @endif

                    </td>

                    <td class="p-4">
                        {{ $ticket->created_at->format('d/m/Y') }}
                    </td>

                    <td class="p-4 text-center">

                        <a href="{{ route('support.tickets.show', $ticket) }}"
                           class="bg-indigo-600 text-white px-3 py-2 rounded">

                            Voir

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="10"
                        class="text-center p-6">

                        Aucun ticket trouvé

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-4">
        {{ $tickets->links() }}
    </div>

</div>
```

</div>

@endsection

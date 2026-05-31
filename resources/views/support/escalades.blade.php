<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI IT SUPPORT | Admin Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">

<!-- SIDEBAR -->
<aside class="w-64 bg-indigo-900 text-white flex flex-col">

        <div class="p-6 border-b border-indigo-700">
            <h1 class="text-2xl font-bold"> Support Team </h1>
        </div>
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Vue générale</h3>
                <a href="#" class="block px-4 py-3 rounded-lg bg-indigo-600 text-white">Dashboard</a>
            </div>
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Tickets</h3>
                <a href="{{ route('support.tickets.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Mes tickets</a>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Escalades</a>
            </div>
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Conversations</h3>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800"> Conversations</a>
            </div>
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Knowledge Base</h3>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Base de connaissance</a>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Ajouter une solution</a>
            </div>
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Configuration</h3>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800"> Notifications</a>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Paramètres</a>
            </div>
        </nav>

        <div class="p-4 border-t border-indigo-700">
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
             @csrf
             <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg">Déconnexion</button>
            </form>
        </div>

</aside>


    <!-- MAIN -->
    <main class="flex-1 overflow-y-auto">
        <!-- TOPBAR -->
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div><h2 class="text-2xl font-bold text-gray-800">Dashboard Administrateur</h2></div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500">ADMINISTRATEUR</p>
                </div>
            </div>

        </header>

        <!-- CONTENT -->
        <div class="p-8">
            
            <!-- Colonne DataTable -->
            <div class="bg-white p-6 shadow rounded">
                <h2 class="text-xl font-semibold mb-4">Liste des Tickets</h2>

                <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Titre</th>
                            <th class="px-4 py-3 text-left">Jira</th>
                            <th class="px-4 py-3 text-left">Catégorie</th>
                            <th class="px-4 py-3 text-left">Priorité</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-left">IA</th>
                            <th class="px-4 py-3 text-left">Escalade</th>
                            <th class="px-4 py-3 text-left">Créé le</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="px-4 py-3">#{{ $ticket->id }}</td>
                                <td class="px-4 py-3 font-medium">{{ $ticket->title }}</td>
                                <td class="px-4 py-3">{{ $ticket->jira_ticket_id }}</td>
                                <td class="px-4 py-3">{{ $ticket->category }}</td>
                                <td class="px-4 py-3">
                                @if($ticket->priority == 'critical')
                                <span class="px-2 py-1 text-red-700 bg-red-100 rounded">Critique</span>
                                @elseif($ticket->priority == 'high')
                                <span class="px-2 py-1 text-orange-700 bg-orange-100 rounded"> Haute</span>
                                @elseif($ticket->priority == 'medium')
                                <span class="px-2 py-1 text-blue-700 bg-blue-100 rounded">Moyenne</span>
                                @else
                                <span class="px-2 py-1 text-green-700 bg-green-100 rounded">Faible</span>
                                @endif
                                </td>
                                <td class="px-4 py-3">
                                    @switch($ticket->status)
                                    @case('open')
                                    <span class="px-2 py-1 text-blue-700 bg-blue-100 rounded">Ouvert</span>@break
                                    @case('in_progress')
                                    <span class="px-2 py-1 text-yellow-700 bg-yellow-100 rounded">En cours</span>@break
                                    @case('resolved')<span class="px-2 py-1 text-green-700 bg-green-100 rounded">Résolu</span>@break
                                    @default
                                    <span class="px-2 py-1 text-gray-700 bg-gray-100 rounded">{{ $ticket->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-4 py-3">{{ $ticket->confidence }}%</td>
                                <td class="px-4 py-3">
                                @if($ticket->is_escalated)
                                    <span class="text-red-600 font-semibold">Oui</span>
                                @else
                                    <span class="text-green-600"> Non</span>
                                @endif
                                </td>
                                <td class="px-4 py-3">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-center">
                                        <a href="#" class="px-3 py-1 text-white bg-blue-600 rounded">Ouvrir</a>
                                        <a href="#" class="px-3 py-1 text-white bg-green-600 rounded">Répondre</a>
                                        <a href="#" class="px-3 py-1 text-white bg-yellow-600 rounded">Réassigner</a>
                                        <a href="#" class="px-3 py-1 text-white bg-red-600 rounded">Résoudre</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-6 text-center">
                                    Aucun ticket trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </main>

</div>

</body>
</html>
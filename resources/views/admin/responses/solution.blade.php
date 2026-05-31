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
    <aside class="w-64 bg-slate-900 text-white flex flex-col">

        <div class="p-6 border-b border-slate-700">
            <h1 class="text-2xl font-bold">IT Support AI</h1>
        </div>
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Vue globale</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white">Dashboard</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400"> Gestion des tickets</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Tous les tickets</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">💬 Conversations</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Intelligence artificielle</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Réponses IA</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Messages analysés</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Configuration IA</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Knowledge Base</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Base de connaissances</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Ajouter une solution</a>
            <a href="#"class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Catégories</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Administration</h3>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Utilisateurs</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Rôles & Permissions</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Priorités</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400"> Rapports</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Statistiques tickets</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Activité agents IT</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Système</h3>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Notifications</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Intégration Jira</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Paramètres système</a>
        </div>
        </nav>

        <div class="p-4 border-t border-slate-700">
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
            <div class="bg-white p-6 shadow rounded mb-6">
                <h2 class="text-xl font-semibold mb-4">Formulaire</h2>
                @if($kb)
                <form method="POST" action="{{ route('admin.base.update', $kb->id) }}">
                    @csrf
                    @method('PUT')
                @else
                <form method="POST" action="{{ route('admin.base.store') }}">
                    @csrf
                @endif
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom</label>
                        <input type="text" value="{{ old('problem_keywords', $kb->problem_keywords ?? '') }}" name="problem_keywords" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keywords">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="text" value="{{ old('solution', $kb->solution ?? '') }}" name="solution" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="solution">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ $kb ? 'Mettre à jour' : 'Enregistrer' }}</button>
                </form>
            </div>

            <!-- Colonne DataTable -->
            <div class="bg-white p-6 shadow rounded">
                <h2 class="text-xl font-semibold mb-4">Knowledge Base</h2>

                <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2">ID</th>
                            <th class="border p-2">Keywords</th>
                            <th class="border p-2">Solution</th>
                            <th class="border p-2">Source</th>
                            <th class="border p-2">Usage</th>
                            <th class="border p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kbs as $kb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border">{{ $kb->id }}</td>
                                <td class="px-4 py-3 border">{{ $kb->problem_keywords }}</td>
                                <td class="px-4 py-3 border">{{ $kb->solution }}</td>
                                <td class="px-4 py-3 border">{{ $kb->source }}</td>
                                <td class="px-4 py-3 border">{{ $kb->usage }}</td>                                
                                <td class="px-4 py-3 border">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.base.index', ['edit' => $kb->id]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Modifier</a>
                                        <form method="POST"
                                            action="{{route('admin.base.delete', $kb->id) }}"
                                            onsubmit="return confirm('Supprimer cette base ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">Aucun utilisateur trouvé</td>
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
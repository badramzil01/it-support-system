 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Dashboard</title>

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
                <a href="{{ route('support.discussions.index')}}" class="block px-4 py-3 rounded-lg hover:bg-slate-800"> Conversations</a>
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
        <!-- HEADER -->
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div><h2 class="text-2xl font-bold text-gray-800">Dashboard Support</h2></div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500">Support IT</p>
                </div>
            </div>

        </header>

        <div class="h-[calc(100vh-4rem)] flex">
            <!-- Colonne discussions -->
            <div class="w-80 border-r bg-grey flex flex-col">

                <div class="p-4 border-b">
                    <h2 class="font-bold text-lg">Discussions</h2>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($conversations as $conv)
                    <div class="p-4 border-b hover:bg-white-100 cursor-pointer">
                        <h3 class="font-semibold">{{$conv->user->name}}</h3>
                        <p class="text-sm text-gray-500">{{$conv->title}}</p>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-12 h-12 text-gray-400 mb-3"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M21 12c0 4.97-4.03 9-9 9a9.97 9.97 0 01-4.9-1.28L3 21l1.28-4.1A9.97 9.97 0 013 12c0-4.97 4.03-9 9-9s9 4.03 9 9z"/>
                        </svg>

                        <h3 class="font-semibold text-gray-700">
                            Aucune discussion
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Aucun ticket ou conversation n'est disponible pour le moment.
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Colonne messages -->
            <div class="flex-1 flex flex-col">
                <div class="p-4 border-b bg-white">
                    <h2 class="font-bold">Ticket #154 - VPN</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-6 bg-gray-100">
                    <div class="mb-4">
                        <div class="inline-block bg-white p-3 rounded-lg shadow">
                            Bonjour, mon VPN ne fonctionne plus.
                        </div>
                    </div>

                    <div class="mb-4 text-right">
                        <div class="inline-block bg-blue-600 text-white p-3 rounded-lg">
                            Pouvez-vous redémarrer le client VPN ?
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t bg-white">
                    <form class="flex gap-2">
                        <input
                            type="text"
                            class="flex-1 border rounded-lg px-4 py-2"
                            placeholder="Votre réponse..."
                        >
                        <button
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                            Envoyer
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </main>
</div>
</body>
</html>

@role('admin')
   <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">

        <div class="p-6 border-b border-slate-700">
            <h1 class="text-2xl font-bold">IT Support AI</h1>
        </div>
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Vue globale</h3>
            <a href="{{route('admin.dashboard')}}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white">Dashboard</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">  Reporting Center</a>

        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Knowledge Base</h3>
            <a href="{{ route('admin.base.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Base de connaissances</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Administration</h3>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Utilisateurs</a>
            <a href="{{ route('admin.role_permissions') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Rôles & Permissions</a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Support Team</a>
        </div>
        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Système</h3>
            <a href="{{route('admin.notifications')}}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">Notifications</a>
            <a href="{{route('admin.settings.index')}}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800"> Paramètres système</a>
            <a href="{{route('admin.logs')}}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800">  Audit Log</a>

        </div>
        </nav>

        <div class="p-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                @csrf
                <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg">Déconnexion</button>
            </form>
        </div>

    </aside>
@endrole
@role('support')
<!-- SIDEBAR -->
<aside class="w-64 bg-indigo-900 text-white flex flex-col">

        <div class="p-6 border-b border-indigo-700">
            <h1 class="text-2xl font-bold"> Support Team </h1>
        </div>
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
            <div>
                <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">Vue générale</h3>
                <a href="{{route('support.dashbord')}}" class="block px-4 py-3 rounded-lg bg-indigo-600 text-white">Dashboard</a>
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
                <a href="{{route('support.notifications')}}" class="block px-4 py-3 rounded-lg hover:bg-slate-800"> Notifications</a>
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

@endrole
@extends('parts.base')

@section('content')
@role('admin')
<div class="space-y-8">
    <!-- Informations générales -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="border-b px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-800">
                Informations générales
            </h2>
        </div>
        <div class="p-6">
            <form action="#" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Nom de l'application
                        </label>
                        <input type="text" name="app_name" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    value="IT SUPPORT ">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Email administrateur
                        </label>
                        <input type="email" name="email_admin" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    value="admin@app.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Fuseau horaire
                        </label>
                        <select name="fuseau" class="w-full rounded-lg border-slate-300">
                            <option value="casablanca" >Africa/Casablanca</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Langue
                        </label>
                            <select name="langage" class="w-full rounded-lg border-slate-300">
                                <option value="FR">Français</option>
                                <option value="ENG">English</option>
                            </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- API Keys & Intégrations -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-800">
                API Keys & Intégrations
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-5">
                <!-- JIRA -->
                <div class="border rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">Jira</h3>
                            <p class="text-sm text-slate-500">Gestion des tickets</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-sm font-medium">
                            Clé API
                        </label>
                        <input name="jira_key" type="password"
                            value="_xxxxxxxxxxxxx"
                            class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                </div>
                <div class="border rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">n8n</h3>
                            <p class="text-sm text-slate-500">workflow monitoring</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-sm font-medium">
                            Clé API
                        </label>
                        <input name="api_key" type="password"
                            value="_xxxxxxxxxxxxx"
                            class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                </div>
                <div class="border rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">AI</h3>
                            <p class="text-sm text-slate-500">service AI monitoring</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-sm font-medium">
                            Clé API
                        </label>
                        <input name="AI_key" type="password"
                            value="_xxxxxxxxxxxxx"
                            class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endrole
@role('support')
<div class="space-y-8">
    <!-- Informations générales -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="border-b px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-800">
                Informations
            </h2>
        </div>
        <div class="p-6">
            
        </div>
    </div>
</div>
@endrole
@endsection
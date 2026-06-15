@extends('admin.layouts.app')
@section('title','Gestion des Utilisateurs')
@section('content')
<div class="min-h-screen bg-slate-50 p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">

        <div>
            <h1 class="text-3xl font-bold text-slate-800">
                Gestion des utilisateurs
            </h1>

            <p class="text-slate-500 mt-1">
                Administration des comptes utilisateurs et des rôles
            </p>
        </div>

        <a href="{{ route('admin.ui.roles') }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
            Gérer les rôles
        </a>

    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total utilisateurs</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-2">
                {{ $users->count() }}
            </h3>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Administrateurs</p>
            <h3 class="text-3xl font-bold text-red-600 mt-2">
                {{ $users->filter(fn($u) => $u->hasRole('admin'))->count() }}
            </h3>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Clients</p>
            <h3 class="text-3xl font-bold text-emerald-600 mt-2">
                {{ $users->filter(fn($u) => $u->hasRole('client'))->count() }}
            </h3>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- FORMULAIRE -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">

            <h2 class="text-xl font-semibold text-slate-800 mb-6">
                {{ isset($editUser) ? 'Modifier utilisateur' : 'Ajouter utilisateur' }}
            </h2>

            @php
                $selectedRole = isset($editUser)
                    ? $editUser->getRoleNames()->first()
                    : old('role');
            @endphp

            <form method="POST"
                  action="{{ isset($editUser)
                        ? route('admin.ui.users.update', $editUser->id)
                        : route('admin.ui.users.store') }}">

                @csrf

                @if(isset($editUser))
                    @method('PUT')
                @endif

                <!-- NOM -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nom
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $editUser->name ?? '') }}"
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <!-- EMAIL -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $editUser->email ?? '') }}"
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <!-- PASSWORD -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">

                    @if(isset($editUser))
                        <p class="text-xs text-slate-500 mt-2">
                            Laisser vide pour conserver le mot de passe actuel.
                        </p>
                    @endif
                </div>

                <!-- ROLE -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Rôle
                    </label>

                    <select
                        name="role"
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">

                        @foreach($roles as $r)
                            <option value="{{ $r->name }}"
                                {{ $selectedRole === $r->name ? 'selected' : '' }}>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-xl transition">

                    {{ isset($editUser) ? 'Mettre à jour' : 'Créer l’utilisateur' }}

                </button>

            </form>

        </div>

        <!-- TABLE -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 xl:col-span-2">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-slate-800">
                    Liste des utilisateurs
                </h2>

                <span class="text-sm text-slate-500">
                    {{ $users->count() }} utilisateur(s)
                </span>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="bg-slate-100 text-slate-600 uppercase text-xs tracking-wider">
                            <th class="p-4 text-left">ID</th>
                            <th class="p-4 text-left">Nom</th>
                            <th class="p-4 text-left">Email</th>
                            <th class="p-4 text-left">Rôle</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($users as $user)

                        @php
                            $role = $user->getRoleNames()->first();

                            $roleClasses = match($role) {
                                'admin' => 'bg-red-50 text-red-700 border border-red-200',
                                'support' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                'client' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                default => 'bg-slate-50 text-slate-700 border border-slate-200',
                            };
                        @endphp

                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">

                            <td class="p-4 font-medium text-slate-700">
                                #{{ $user->id }}
                            </td>

                            <td class="p-4 font-medium text-slate-800">
                                {{ $user->name }}
                            </td>

                            <td class="p-4 text-slate-600">
                                {{ $user->email }}
                            </td>

                            <td class="p-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $roleClasses }}">
                                    {{ ucfirst($role) }}
                                </span>
                            </td>

                            <td class="p-4">

                                <div class="flex items-center justify-center gap-2">

                                    <a href="{{ route('admin.ui.users.index', ['edit' => $user->id]) }}"
                                    title="Modifier"
                                    class="p-2 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>

                                    </a>

                                <!-- Supprimer -->
                                <form method="POST"
                                    action="{{ route('admin.ui.users.delete', $user) }}"
                                    onsubmit="return confirm('Supprimer cet utilisateur ?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Supprimer"
                                            class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-7 3h10" />
                                        </svg>

                                    </button>

                                </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection
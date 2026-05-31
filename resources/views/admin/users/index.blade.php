@extends('parts.base')
@section('content')
        <!-- CONTENT -->
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Colonne Formulaire -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Formulaire</h2>
                @if($user)
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                @else
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                @endif
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom</label>
                        <input type="text" value="{{ old('name', $user->name ?? '') }}" name="name" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Votre nom">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" value="{{ old('email', $user->email ?? '') }}" name="email" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Votre email">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="**********">
                    @if($user)<small>Laisser vide pour conserver le mot de passe actuel</small>@endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <select name="roles[]" multiple class="w-full border rounded p-2">
                        @foreach($roles as $role)
                        @if($user && $user->roles->contains('name', $role->name))
                        <option value="{{ $role->name }}" selected >{{ $role->name }}</option>
                        @else
                        <option value="{{ $role->name }}" >{{ $role->name }}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ $user ? 'Mettre à jour' : 'Enregistrer' }}</button>
                </form>
            </div>

            <!-- Colonne DataTable -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Liste des utilisateurs</h2>

                <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 border text-left">ID</th>
                            <th class="px-4 py-3 border text-left">Nom</th>
                            <th class="px-4 py-3 border text-left">Email</th>
                            <th class="px-4 py-3 border text-left">Rôles</th>
                            <th class="px-4 py-3 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border">{{ $user->id }}</td>
                                <td class="px-4 py-3 border">{{ $user->name }}</td>
                                <td class="px-4 py-3 border">{{ $user->email }}</td>
                                <td class="px-4 py-3 border">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 border">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.users.index', ['edit' => $user->id]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Modifier</a>
                                        <form method="POST"
                                            action="{{route('admin.users.delete', $user) }}"
                                            onsubmit="return confirm('Supprimer cet utilisateur ?')">
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
        </div>
@endsection
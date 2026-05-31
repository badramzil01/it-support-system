@extends('parts.base')
@section('content')
<div class="p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Formulaire Role -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                {{ isset($role) ? 'Modifier un rôle' : 'Créer un rôle' }}
            </h2>

            @if(isset($role))
                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')
            @else
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
            @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Nom du rôle
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $role->name ?? '') }}"
                        class="w-full border rounded-md px-3 py-2"
                        placeholder="Ex: Administrateur">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Permissions
                    </label>

                    <select
                        name="permissions[]"
                        multiple
                        class="w-full border rounded-md p-2 h-48">

                        @foreach($permissions as $permission)
                            <option
                                value="{{ $permission->name }}"
                                @if(
                                    isset($role)
                                    && $role->permissions->contains('name', $permission->name)
                                )
                                    selected
                                @endif
                            >
                                {{ $permission->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ isset($role) ? 'Mettre à jour' : 'Créer le rôle' }}
                </button>

            </form>
        </div>

        <!-- Liste des rôles -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                Liste des rôles
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-3">ID</th>
                            <th class="border px-4 py-3">Rôle</th>
                            <th class="border px-4 py-3">Permissions</th>
                            <th class="border px-4 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="border px-4 py-3">
                                    {{ $role->id }}
                                </td>

                                <td class="border px-4 py-3 font-semibold">
                                    {{ strtoupper($role->name) }}
                                </td>

                                <td class="border px-4 py-3">
                                    @foreach($role->permissions as $permission)
                                        <span class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mr-1 mb-1">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </td>

                                <td class="border px-4 py-3">
                                    <div class="flex gap-2">

                                        <a
                                            href="{{ route('admin.role_permissions', ['edit' => $role->id]) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded">
                                            Modifier
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.roles.destroy', $role->id) }}"
                                            onsubmit="return confirm('Supprimer ce rôle ?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded">
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    Aucun rôle trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Formulaire Permission -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                {{ isset($permission) ? 'Modifier une permission' : 'Créer permission' }}
            </h2>

            @if(isset($permission))
                <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                    @csrf
                    @method('PUT')
            @else
                <form method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf
            @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Permission :
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $permission->name ?? '') }}"
                        class="w-full border rounded-md px-3 py-2"
                        placeholder="Ex: create">
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ isset($permission) ? 'Mettre à jour' : 'Créer permission' }}
                </button>

            </form>
        </div>

        <!-- Liste des rôles -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                Liste des permission
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-3">ID</th>
                            <th class="border px-4 py-3">Permissions</th>
                            <th class="border px-4 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($permissions as $p)
                            <tr>
                                <td class="border px-4 py-3">
                                    {{ $p->id }}
                                </td>

                                <td class="border px-4 py-3 font-semibold">
                                    {{ strtoupper($p->name) }}
                                </td>

                                <td class="border px-4 py-3">
                                    <div class="flex gap-2">

                                        <a
                                            href="{{ route('admin.role_permissions', ['editp' => $p->id]) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded">
                                            Modifier
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.permission.destroy', $p->id) }}"
                                            onsubmit="return confirm('Supprimer cette permission ?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded">
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    Aucune permission trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>
@endsection
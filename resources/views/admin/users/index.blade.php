@extends('admin.layouts.app')
@section('title','Gestion des Utilisateurs')
@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="min-h-screen bg-slate-50 dark:bg-[#0D0F14] p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white brand-font">Gestion des utilisateurs</h1>
            <p class="text-slate-500 mt-1">Administration des comptes, rôles et permissions</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ui.roles') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition text-sm">
                <svg class="w-4 h-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                Rôles & Permissions
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total utilisateurs</p>
            <h3 class="text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ $users->count() }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Administrateurs</p>
            <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $users->filter(fn($u) => $u->hasRole('admin'))->count() }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Support</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $users->filter(fn($u) => $u->hasRole('support'))->count() }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Clients</p>
            <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $users->filter(fn($u) => $u->hasRole('client'))->count() }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ═══ FORMULAIRE ═══ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-6 brand-font">
                {{ isset($editUser) ? 'Modifier utilisateur' : 'Ajouter utilisateur' }}
            </h2>

            @php
                $selectedRole = isset($editUser)
                    ? $editUser->getRoleNames()->first()
                    : old('role');
                $editPermissions = isset($editUser)
                    ? $editUser->getAllPermissions()->pluck('name')->toArray()
                    : [];
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
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $editUser->name ?? '') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- EMAIL -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editUser->email ?? '') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- PASSWORD -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Mot de passe</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
                    @if(isset($editUser))
                        <p class="text-xs text-slate-500 mt-1">Laisser vide pour conserver le mot de passe actuel.</p>
                    @endif
                </div>

                <!-- ROLE -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Rôle</label>
                    <select name="role" id="roleSelect"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm"
                            onchange="togglePermissionPanel()">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ $selectedRole === $r->name ? 'selected' : '' }}>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ═══ PERMISSION PANEL ═══ -->
                <div id="permissionPanel" class="{{ ($selectedRole === 'admin' || !$selectedRole) ? 'hidden' : '' }}">
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Permissions</h3>
                            <button type="button" onclick="toggleAllPermissions()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Tout sélectionner / Désélectionner
                            </button>
                        </div>

                        <!-- SEARCH -->
                        <div class="mb-3">
                            <input type="text" id="permissionSearch" placeholder="Rechercher une permission..."
                                   oninput="filterPermissions()"
                                   class="w-full px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        @foreach($groupedPermissions as $group => $perms)
                            <div class="permission-group mb-3" data-group="{{ $group }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide">{{ $group }}</label>
                                    <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}')" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-medium">
                                        Toggle
                                    </button>
                                </div>
                                <div class="space-y-1 permission-group-items" id="group-{{ Str::slug($group) }}">
                                    @foreach($perms as $perm)
                                        <label class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer permission-item" data-permission="{{ $perm }}">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                                   {{ in_array($perm, $editPermissions) ? 'checked' : '' }}
                                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', ucfirst($perm)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                    {{ isset($editUser) ? 'Mettre à jour' : 'Créer l\'utilisateur' }}
                </button>
            </form>
        </div>

        <!-- ═══ TABLE ═══ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6 xl:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-slate-800 dark:text-white brand-font">Liste des utilisateurs</h2>
                <span class="text-sm text-slate-500">{{ $users->count() }} utilisateur(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wider">
                            <th class="p-3 text-left">ID</th>
                            <th class="p-3 text-left">Nom</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Rôle</th>
                            <th class="p-3 text-center">Permissions</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        @php
                            $role = $user->getRoleNames()->first();
                            $roleClasses = match($role) {
                                'admin' => 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800',
                                'support' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800',
                                'client' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
                                default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
                            };
                            $userPerms = $user->getAllPermissions()->pluck('name')->toArray();
                        @endphp
                        <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-3 font-medium text-slate-700 dark:text-slate-300">#{{ $user->id }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-xs font-bold text-white">
                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-white text-sm">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-slate-600 dark:text-slate-400 text-sm">{{ $user->email }}</td>
                            <td class="p-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleClasses }}">
                                    {{ ucfirst($role) }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                @if($role === 'admin')
                                    <span class="text-xs text-slate-500 italic">All permissions</span>
                                @else
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ count($userPerms) }} perm(s)</span>
                                    <button onclick="openPermissionModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="ml-1 text-indigo-600 hover:text-indigo-800 text-xs underline"
                                            title="Gérer les permissions">
                                        gérer
                                    </button>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.ui.users.index', ['edit' => $user->id]) }}"
                                       title="Modifier" class="p-1.5 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.ui.users.delete', $user) }}"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Supprimer" class="p-1.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-7 3h10"/></svg>
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

<!-- ═══ PERMISSION MODAL ═══ -->
<div id="permissionModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePermissionModal()"></div>
    <div class="absolute inset-4 md:inset-10 lg:inset-20 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white brand-font">Permissions</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="modalUserName">—</p>
            </div>
            <button onclick="closePermissionModal()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mb-4">
                <input type="text" id="modalPermissionSearch" placeholder="Rechercher une permission..."
                       oninput="filterModalPermissions()"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div class="mb-4">
                <button type="button" onclick="toggleModalAllPermissions()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    Tout sélectionner / Désélectionner
                </button>
            </div>
            @foreach($groupedPermissions as $group => $perms)
                <div class="mb-4 modal-perm-group" data-group="{{ $group }}">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide">{{ $group }}</h4>
                        <button type="button" onclick="toggleModalGroup('{{ Str::slug($group) }}')" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-medium">Toggle</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-1 modal-group-items" id="modal-group-{{ Str::slug($group) }}">
                        @foreach($perms as $perm)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer modal-permission-item" data-permission="{{ $perm }}">
                                <input type="checkbox" name="modal_permissions[]" value="{{ $perm }}"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 modal-perm-checkbox">
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', ucfirst($perm)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            <button onclick="closePermissionModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Annuler
            </button>
            <button onclick="savePermissions()" id="savePermissionsBtn" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
                Enregistrer les permissions
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentModalUserId = null;

// ═══ Form: toggle permission panel based on role ═══
function togglePermissionPanel() {
    const role = document.getElementById('roleSelect').value;
    const panel = document.getElementById('permissionPanel');
    if (role === 'admin') {
        panel.classList.add('hidden');
    } else {
        panel.classList.remove('hidden');
    }
}

// ═══ Form: filter permissions ═══
function filterPermissions() {
    const search = document.getElementById('permissionSearch').value.toLowerCase();
    document.querySelectorAll('.permission-item').forEach(el => {
        const perm = el.getAttribute('data-permission').replace(/_/g, ' ').toLowerCase();
        el.style.display = perm.includes(search) ? '' : 'none';
    });
}

// ═══ Form: toggle all permissions ═══
function toggleAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// ═══ Form: toggle group ═══
function toggleGroup(slug) {
    const groupEl = document.getElementById('group-' + slug);
    if (!groupEl) return;
    const checkboxes = groupEl.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// ═══ Modal: open ═══
function openPermissionModal(userId, userName) {
    currentModalUserId = userId;
    document.getElementById('modalUserName').textContent = 'Utilisateur : ' + userName;
    document.getElementById('permissionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Fetch current permissions
    fetch(`/admin/users/${userId}/permissions`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Uncheck all first
        document.querySelectorAll('.modal-perm-checkbox').forEach(cb => cb.checked = false);
        // Check user's permissions
        if (data.permissions) {
            data.permissions.forEach(perm => {
                const cb = document.querySelector(`.modal-perm-checkbox[value="${perm}"]`);
                if (cb) cb.checked = true;
            });
        }
    })
    .catch(() => {
        // If GET not supported, use stored values
    });
}

// ═══ Modal: close ═══
function closePermissionModal() {
    document.getElementById('permissionModal').classList.add('hidden');
    document.body.style.overflow = '';
    currentModalUserId = null;
}

// ═══ Modal: filter ═══
function filterModalPermissions() {
    const search = document.getElementById('modalPermissionSearch').value.toLowerCase();
    document.querySelectorAll('.modal-permission-item').forEach(el => {
        const perm = el.getAttribute('data-permission').replace(/_/g, ' ').toLowerCase();
        el.style.display = perm.includes(search) ? '' : 'none';
    });
}

// ═══ Modal: toggle all ═══
function toggleModalAllPermissions() {
    const checkboxes = document.querySelectorAll('.modal-perm-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// ═══ Modal: toggle group ═══
function toggleModalGroup(slug) {
    const groupEl = document.getElementById('modal-group-' + slug);
    if (!groupEl) return;
    const checkboxes = groupEl.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// ═══ Modal: save permissions ═══
function savePermissions() {
    if (!currentModalUserId) return;

    const permissions = [];
    document.querySelectorAll('.modal-perm-checkbox:checked').forEach(cb => {
        permissions.push(cb.value);
    });

    const btn = document.getElementById('savePermissionsBtn');
    btn.textContent = 'Enregistrement...';
    btn.disabled = true;

    // Use PUT method via form data
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    permissions.forEach(p => formData.append('permissions[]', p));

    fetch(`/admin/users/${currentModalUserId}/permissions`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.textContent = 'Enregistrer les permissions';
        btn.disabled = false;
        closePermissionModal();
        // Show success message
        const msg = document.createElement('div');
        msg.className = 'fixed top-4 right-4 z-50 rounded-xl bg-emerald-500 text-white px-4 py-3 text-sm font-medium shadow-lg';
        msg.textContent = 'Permissions mises à jour avec succès';
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 3000);
        // Reload page after short delay
        setTimeout(() => location.reload(), 1500);
    })
    .catch(err => {
        btn.textContent = 'Enregistrer les permissions';
        btn.disabled = false;
        alert('Erreur lors de la mise à jour des permissions.');
    });
}
</script>
@endpush
@endsection
@extends('admin.layouts.app')
@section('title','Rôles & Permissions')
@php use Illuminate\Support\Str; @endphp
@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-[#0D0F14] p-6">

    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-800 dark:text-white brand-font">Rôles & Permissions</h1>
        <p class="text-slate-500 mt-1">Gestion des rôles et des permissions du système</p>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- $groupedPermissions is passed from controller --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ═══ ROLE FORM ═══ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-4 brand-font">
                {{ isset($role) ? 'Modifier un rôle' : 'Créer un rôle' }}
            </h2>

            @if(isset($role))
                <form method="POST" action="{{ route('admin.ui.roles.update', $role->id) }}">
                    @csrf @method('PUT')
            @else
                <form method="POST" action="{{ route('admin.ui.roles.store') }}">
                    @csrf
            @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nom du rôle</label>
                    <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="Ex: moderator">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Permissions</label>
                        <button type="button" onclick="toggleAllRolePerms()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Tout sélectionner / Désélectionner
                        </button>
                    </div>

                    <!-- Search -->
                    <input type="text" id="rolePermSearch" placeholder="Rechercher..."
                           oninput="filterRolePerms()"
                           class="w-full px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500 outline-none mb-3">

                    @php
                        $rolePermissions = isset($role)
                            ? $role->permissions->pluck('name')->toArray()
                            : old('permissions', []);
                    @endphp

                    @foreach($groupedPermissions as $group => $perms)
                        <div class="mb-3 role-perm-group">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide">{{ $group }}</span>
                                <button type="button" onclick="toggleRolePermGroup('{{ Str::slug($group) }}')" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-medium">Toggle</button>
                            </div>
                            <div class="space-y-1" id="role-group-{{ Str::slug($group) }}">
                                @foreach($perms as $perm)
                                    <label class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer role-perm-item">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                               {{ in_array($perm, $rolePermissions) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', ucfirst($perm)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                    {{ isset($role) ? 'Mettre à jour' : 'Créer le rôle' }}
                </button>
            </form>
        </div>

        <!-- ═══ ROLES LIST ═══ -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-4 brand-font">Liste des rôles</h2>

            <div class="space-y-3">
                @forelse($roles as $r)
                    @php
                        $roleBadge = match($r->name) {
                            'admin' => 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300',
                            'support' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300',
                            'client' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
                        };
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $roleBadge }}">
                                {{ ucfirst($r->name) }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $r->permissions->count() }} permission(s)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.ui.roles', ['edit' => $r->id]) }}"
                               class="p-1.5 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.ui.roles.destroy', $r->id) }}"
                                  onsubmit="return confirm('Supprimer ce rôle ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-7 3h10"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-4">Aucun rôle trouvé</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ═══ PERMISSIONS MANAGEMENT ═══ -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Create Permission Form -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-4 brand-font">
                {{ isset($p) ? 'Modifier une permission' : 'Créer une permission' }}
            </h2>

            @if(isset($p))
                <form method="POST" action="{{ route('admin.ui.permissions.update', $p->id) }}">
                    @csrf @method('PUT')
            @else
                <form method="POST" action="{{ route('admin.ui.permissions.store') }}">
                    @csrf
            @endif
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nom de la permission</label>
                    <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="Ex: create_ticket">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                    {{ isset($p) ? 'Mettre à jour' : 'Créer la permission' }}
                </button>
            </form>
        </div>

        <!-- Permissions List -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-4 brand-font">Liste des permissions</h2>

            <div class="space-y-1 max-h-96 overflow-y-auto">
                @forelse($permissions as $perm)
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $perm->name }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.ui.roles', ['editp' => $perm->id]) }}"
                               class="p-1 rounded bg-amber-100 text-amber-600 hover:bg-amber-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.ui.permission.destroy', $perm->id) }}"
                                  onsubmit="return confirm('Supprimer cette permission ?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 rounded bg-red-100 text-red-600 hover:bg-red-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-7 3h10"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-4">Aucune permission</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterRolePerms() {
    const search = document.getElementById('rolePermSearch').value.toLowerCase();
    document.querySelectorAll('.role-perm-item').forEach(el => {
        const text = el.textContent.toLowerCase();
        el.style.display = text.includes(search) ? '' : 'none';
    });
}
function toggleAllRolePerms() {
    const cbs = document.querySelectorAll('input[name="permissions[]"]');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
function toggleRolePermGroup(slug) {
    const el = document.getElementById('role-group-' + slug);
    if (!el) return;
    const cbs = el.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
</script>
@endpush
@endsection
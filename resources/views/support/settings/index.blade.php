@extends('support.layouts.app')
@section('title','Settings')
@section('content')
<div class="bg-white p-6 rounded shadow max-w-3xl">
    <h4 class="font-semibold">Mon Compte</h4>
    @if(session('success'))
        <div class="mt-3 px-3 py-2 rounded bg-emerald-50 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mt-3 px-3 py-2 rounded bg-red-50 text-red-800">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('support.ui.settings.profile.update') }}" class="mt-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium">Nom</label>
                <input name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', auth()->user()->name) }}" />
            </div>
            <div>
                <label class="text-xs font-medium">Email</label>
                <input name="email" class="w-full border rounded px-3 py-2" value="{{ old('email', auth()->user()->email) }}" />
            </div>
        </div>
        <div class="mt-4">
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Enregistrer</button>
        </div>
    </form>

    <div class="mt-8">
        <h4 class="font-semibold">Changer le mot de passe</h4>
        <form method="POST" action="{{ route('support.ui.settings.password.update') }}" class="mt-4 max-w-md">
            @csrf
            @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="text-xs">Mot de passe actuel</label>
                    <input name="current_password" type="password" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="text-xs">Nouveau mot de passe</label>
                    <input name="password" type="password" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="text-xs">Confirmer le mot de passe</label>
                    <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <button class="px-4 py-2 rounded bg-blue-600 text-white">Mettre à jour le mot de passe</button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8">
        <h4 class="font-semibold">Apparence</h4>
        <div class="mt-3 flex items-center gap-3">
            <label class="flex items-center gap-2">
                <input id="settingsThemeToggle" type="checkbox" class="h-4 w-4" />
                <span class="text-sm">Mode sombre</span>
            </label>
            <p class="text-xs text-slate-500">Basculer le thème de l'application (persisté en local)</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // initialize toggle according to localStorage
    (function(){
        const toggle = document.getElementById('settingsThemeToggle');
        const saved = localStorage.getItem('theme');
        const dark = (saved === 'dark') || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (toggle) { toggle.checked = dark; }
        if (dark) document.documentElement.classList.add('dark');
        toggle?.addEventListener('change', (e) => {
            document.documentElement.classList.toggle('dark', e.target.checked);
            localStorage.setItem('theme', e.target.checked ? 'dark' : 'light');
        });
    })();
</script>
@endpush
@endsection

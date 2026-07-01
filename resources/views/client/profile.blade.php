@extends('client.layout')

@section('page_title', 'Mon Profil')
@section('page_subtitle', 'Gérez vos informations personnelles')

@php
$user = auth()->user();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Profile Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden animate-fade-in">
        <div class="bg-gradient-to-r from-primary-500 to-primary-700 px-6 py-8 md:px-8">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="relative flex-shrink-0">
                    <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white text-4xl font-bold shadow-xl ring-4 ring-white/30">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full rounded-2xl object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->name, -1, 1)) }}
                        @endif
                    </div>
                    <label for="photoUpload" class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-md flex items-center justify-center cursor-pointer hover:scale-110 transition-transform border-2 border-white dark:border-gray-800">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                    <form id="photoForm" action="{{ route('client.profile.photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" id="photoUpload" name="photo" accept="image/*" onchange="document.getElementById('photoForm').submit()">
                    </form>
                </div>
                <div class="text-white">
                    <h2 class="text-xl md:text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-primary-100 text-sm mt-1">{{ $user->email }}</p>
                    <div class="flex items-center gap-4 mt-3 text-primary-100 text-xs">
                        <span>Membre depuis {{ $user->created_at->format('d/m/Y') }}</span>
                        <span class="w-1 h-1 rounded-full bg-primary-300"></span>
                        <span>{{ $user->tickets()->count() }} tickets créés</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="p-6 md:p-8">
            @if(session('status'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-medium">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
                <button onclick="showTab('info')" id="tabInfoBtn" class="px-4 py-2.5 text-sm font-semibold text-primary-600 dark:text-primary-400 border-b-2 border-primary-500 dark:border-primary-400 transition-colors">
                    Informations
                </button>
                <button onclick="showTab('password')" id="tabPasswordBtn" class="px-4 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    Mot de passe
                </button>
            </div>

            {{-- Tab: Info --}}
            <div id="tabInfo" class="space-y-6">
                <form action="{{ route('client.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Téléphone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Membre depuis</label>
                            <input type="text" value="{{ $user->created_at->format('d/m/Y H:i') }}" disabled
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm cursor-not-allowed">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/20 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/30">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab: Password --}}
            <div id="tabPassword" class="space-y-6 hidden">
                <form action="{{ route('client.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mot de passe actuel</label>
                            <input type="password" name="current_password" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nouveau mot de passe</label>
                            <input type="password" name="new_password" required minlength="8"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirmer le mot de passe</label>
                            <input type="password" name="new_password_confirmation" required minlength="8"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/20 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/30">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Account Info Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 animate-fade-in">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Informations du compte
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Statut</p>
                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Actif
                    </span>
                </p>
            </div>
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Rôle</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ ucfirst($user->getRoleNames()->first() ?? 'Client') }}</p>
            </div>
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Email vérifié</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">
                    @if($user->email_verified_at)
                        <span class="text-emerald-600 dark:text-emerald-400">Oui</span>
                    @else
                        <span class="text-amber-600 dark:text-amber-400">Non</span>
                    @endif
                </p>
            </div>
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Dernière connexion</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Première fois' }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tab) {
        document.getElementById('tabInfo').classList.toggle('hidden', tab !== 'info');
        document.getElementById('tabPassword').classList.toggle('hidden', tab !== 'password');
        document.getElementById('tabInfoBtn').classList.toggle('border-primary-500', tab === 'info');
        document.getElementById('tabInfoBtn').classList.toggle('text-primary-600', tab === 'info');
        document.getElementById('tabInfoBtn').classList.toggle('dark:text-primary-400', tab === 'info');
        document.getElementById('tabInfoBtn').classList.toggle('border-transparent', tab !== 'info');
        document.getElementById('tabInfoBtn').classList.toggle('text-gray-500', tab !== 'info');
        document.getElementById('tabInfoBtn').classList.toggle('dark:text-gray-400', tab !== 'info');
        document.getElementById('tabPasswordBtn').classList.toggle('border-primary-500', tab === 'password');
        document.getElementById('tabPasswordBtn').classList.toggle('text-primary-600', tab === 'password');
        document.getElementById('tabPasswordBtn').classList.toggle('dark:text-primary-400', tab === 'password');
        document.getElementById('tabPasswordBtn').classList.toggle('border-transparent', tab !== 'password');
        document.getElementById('tabPasswordBtn').classList.toggle('text-gray-500', tab !== 'password');
        document.getElementById('tabPasswordBtn').classList.toggle('dark:text-gray-400', tab !== 'password');
    }
</script>
@endpush
@endsection

@php
$currentRoute = request()->route() ? request()->route()->getName() : '';
$isActive = function($route) use ($currentRoute) {
    return str_contains($currentRoute, $route) ? true : false;
};
$user = auth()->user();
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col">
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-primary-500/20">
            S
        </div>
        <div>
            <h1 class="text-sm font-bold text-gray-800 dark:text-white">Support IT</h1>
            <p class="text-[10px] text-gray-400 dark:text-gray-500">Espace Client</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        {{-- Dashboard --}}
        <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('client.dashboard') ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        {{-- Chat Support IA (redirige vers /chat existant) --}}
        <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('chat.index') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('client.chat') ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </span>
            <span>Chat Support IA</span>
            @if(isset($aiResponseCount) && $aiResponseCount > 0)
                <span class="ml-auto text-[11px] font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-2 py-0.5 rounded-full">{{ $aiResponseCount }}</span>
            @endif
        </a>

        {{-- Mon Profil --}}
        <a href="{{ route('client.profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('client.profile') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('client.profile') ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            <span>Mon Profil</span>
        </a>
    </nav>

    {{-- User Footer --}}
    <div class="flex-shrink-0 p-4 border-t border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->name, -1, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $user->name }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate">{{ $user->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

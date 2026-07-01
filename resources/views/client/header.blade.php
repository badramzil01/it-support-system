@php
$user = auth()->user();
$unreadCount = 0;
try {
    $unreadNotifications = $user->unreadNotifications ?? collect();
    $unreadCount = $unreadNotifications->count();
} catch (\Exception $e) {
    $unreadNotifications = collect();
}
@endphp

<header class="h-16 flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 md:px-6 lg:px-8">
    {{-- Left: Mobile hamburger + page title --}}
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div>
            <h2 class="text-sm font-bold text-gray-800 dark:text-white">
                @yield('page_title', 'Dashboard')
            </h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500">
                @yield('page_subtitle', 'Bienvenue dans votre espace de support informatique')
            </p>
        </div>
    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-2">
        {{-- Dark mode toggle --}}
        <button onclick="toggleDarkMode()" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Mode sombre">
            <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        {{-- Notifications --}}
        <div class="relative">
            <button id="notifBtn" class="relative w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Notifications">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800 min-w-[18px] h-[18px] px-1">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </button>

            {{-- Notifications Dropdown --}}
            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl z-50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">Notifications</h3>
                    @if($unreadCount > 0)
                        @if(Route::has('notifications.readAll'))
                        <form method="POST" action="{{ route('notifications.readAll') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700">Tout marquer lu</button>
                        </form>
                        @endif
                    @endif
                </div>
                <div class="max-h-72 overflow-y-auto">
                    @if($unreadNotifications->count() > 0)
                        @foreach($unreadNotifications->take(8) as $notification)
                            <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                                <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-xs font-medium">Aucune notification</p>
                        </div>
                    @endif
                </div>
                <a href="#" class="block text-center text-[11px] font-semibold text-primary-600 dark:text-primary-400 py-2.5 border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    Voir toutes les notifications
                </a>
            </div>
        </div>

        {{-- User Menu --}}
        <div class="relative">
            <button id="userMenuBtn" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- User Dropdown --}}
            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-gray-400 truncate">{{ $user->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('client.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Mon Profil
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        Chat IA
                    </a>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700 py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

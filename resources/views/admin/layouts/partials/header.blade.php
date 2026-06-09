<header class="sticky top-0 z-10 flex items-center gap-3 px-4 md:px-6 bg-white/80 dark:bg-[#0D0F14]/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80" style="height: var(--header-h);">
    <button onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
    </button>

    <div class="flex items-center gap-2 min-w-0">
        <span class="text-slate-400 dark:text-slate-600 text-sm hidden sm:block">Admin</span>
        <svg class="h-3.5 w-3.5 text-slate-300 dark:text-slate-700 hidden sm:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
        <h1 class="brand-font text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="ml-auto flex items-center gap-1.5">
        @if(session('success'))
        <div class="hidden sm:flex items-center gap-1.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1.5 text-xs text-emerald-700 dark:text-emerald-400 font-medium">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <button onclick="openCmdPalette()" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
            Rechercher…
            <kbd class="text-[9px] px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-400 rounded font-mono">⌘K</kbd>
        </button>

        <button id="darkToggle" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Basculer le thème">
            <svg class="h-4 w-4 hidden dark:block" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1Zm4 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-.464 4.95.707.707a1 1 0 0 0 1.414-1.414l-.707-.707a1 1 0 0 0-1.414 1.414Zm2.12-10.607a1 1 0 0 1 0 1.414l-.706.707a1 1 0 1 1-1.414-1.414l.707-.707a1 1 0 0 1 1.414 0ZM17 11a1 1 0 1 0 0-2h-1a1 1 0 1 0 0 2h1Zm-7 4a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0v-1a1 1 0 0 1 1-1ZM5.05 6.464A1 1 0 1 0 6.465 5.05l-.708-.707a1 1 0 0 0-1.414 1.414l.707.707Zm1.414 8.486-.707.707a1 1 0 0 1-1.414-1.414l.707-.707a1 1 0 0 1 1.414 1.414ZM4 11a1 1 0 1 0 0-2H3a1 1 0 0 0 0 2h1Z"/></svg>
            <svg class="h-4 w-4 block dark:hidden" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/></svg>
        </button>

        <div class="relative">
            <button onclick="toggleDropdown('notifMenu')" class="relative p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Zm0 14.5a2 2 0 0 1-1.95-1.557 33.54 33.54 0 0 0 3.9 0A2 2 0 0 1 10 16.5Z" clip-rule="evenodd"/></svg>
                @php
                    use Illuminate\Support\Facades\Schema;
                    use App\Models\Notification as AppNotification;
                    $unreadCount = 0;
                    if (auth()->check()) {
                        if (Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id')) {
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        } else {
                            try { $unreadCount = AppNotification::where('status', 'pending')->count(); }
                            catch (\Throwable $e) { $unreadCount = 0; }
                        }
                    }
                @endphp
                <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 min-w-[20px] px-1 text-[11px] font-semibold rounded-full {{ $unreadCount > 0 ? 'bg-red-500 text-white' : 'bg-transparent text-transparent' }}">{{ $unreadCount > 0 ? $unreadCount : '' }}</span>
            </button>
            <div id="notifMenu" class="dropdown-menu absolute right-0 mt-1.5 w-80 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-black/10 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Notifications</p>
                    <span class="badge bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ $unreadCount }} nouvelles</span>
                </div>
                <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('admin.notifications') }}" class="text-xs font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">Voir tout →</a>
                </div>
            </div>
        </div>

        <div class="relative pl-1.5 border-l border-slate-200 dark:border-slate-700">
            <button onclick="toggleDropdown('userMenu')" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-red-600 text-[10px] font-bold text-white">
                    {{ Str::upper(Str::substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="hidden sm:block text-xs font-medium text-slate-700 dark:text-slate-300">{{ auth()->user()->name ?? 'Admin' }}</span>
                <svg class="hidden sm:block h-3 w-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
            </button>
            <div id="userMenu" class="dropdown-menu absolute right-0 mt-1.5 w-52 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-black/10 overflow-hidden py-1">
                <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700 mb-1">
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-500">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <a href="{{ route('admin.ui.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.34 1.804A1 1 0 0 1 9.32 1h1.36a1 1 0 0 1 .98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 0 1 1.262.125l.962.962a1 1 0 0 1 .125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 0 1 .804.98v1.361a1 1 0 0 1-.804.98l-1.473.295a6.95 6.95 0 0 1-.587 1.416l.834 1.25a1 1 0 0 1-.125 1.262l-.962.962a1 1 0 0 1-1.262.125l-1.25-.834a6.953 6.953 0 0 1-1.416.587l-.294 1.473a1 1 0 0 1-.98.804H9.32a1 1 0 0 1-.98-.804l-.295-1.473a6.957 6.957 0 0 1-1.416-.587l-1.25.834a1 1 0 0 1-1.262-.125l-.962-.962a1 1 0 0 1-.125-1.262l.834-1.25a6.957 6.957 0 0 1-.587-1.416l-1.473-.294A1 1 0 0 1 1 10.68V9.32a1 1 0 0 1 .804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 0 1 .125-1.262l.962-.962A1 1 0 0 1 5.38 3.03l1.25.834a6.957 6.957 0 0 1 1.416-.587L8.34 1.804Z" clip-rule="evenodd"/></svg>
                    Paramètres
                </a>
                <div class="border-t border-slate-100 dark:border-slate-700 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 4.25A2.25 2.25 0 0 0 14.75 2h-5.5A2.25 2.25 0 0 0 7 4.25v2a.75.75 0 0 0 1.5 0v-2a.75.75 0 0 1 .75-.75h5.5a.75.75 0 0 1 .75.75v11.5a.75.75 0 0 1-.75.75h-5.5a.75.75 0 0 1-.75-.75v-2a.75.75 0 0 0-1.5 0v2A2.25 2.25 0 0 0 9.25 18h5.5A2.25 2.25 0 0 0 17 15.75V4.25Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M14 10a.75.75 0 0 0-.75-.75H3.704l1.048-1.08a.75.75 0 1 0-1.08-1.04l-2.5 2.59a.75.75 0 0 0 0 1.04l2.5 2.59a.75.75 0 1 0 1.08-1.04l-1.048-1.08H13.25A.75.75 0 0 0 14 10Z" clip-rule="evenodd"/></svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

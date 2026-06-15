<!doctype html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — Support IT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <?php if(class_exists(\Illuminate\Foundation\Vite::class)): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php else: ?>
        <link href="/build/assets/app-DONBQu_T.css" rel="stylesheet">
        <script src="/build/assets/app-DsIK1Lmc.js" defer></script>
    <?php endif; ?>
    <style>
        :root {
            --sidebar-w: 256px;
            --header-h: 56px;
            --accent: #4F6EF7;
            --accent-light: #EEF1FE;
            --accent-dark: #3B56D9;
        }
        * { font-family: 'Inter', system-ui, sans-serif; }
        .brand-font { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Sidebar */
        #sidebar { width: var(--sidebar-w); }
        .main-wrap { margin-left: var(--sidebar-w); }
        @media (max-width: 768px) { .main-wrap { margin-left: 0; } }

        /* Nav item animations */
        .nav-item {
            position: relative;
            transition: all 0.15s ease;
        }
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: var(--accent);
            transform: scaleY(0);
            transition: transform 0.15s ease;
        }
        .nav-item.active::before { transform: scaleY(1); }
        .nav-item.active { background: rgba(79,110,247,0.12); color: #fff; }
        .nav-item:not(.active):hover { background: rgba(255,255,255,0.06); }

        /* Search */
        .search-bar { transition: all 0.2s ease; }
        .search-bar:focus-within { box-shadow: 0 0 0 3px rgba(79,110,247,0.2); }

        /* Cards */
        .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); }

        /* Table rows */
        .data-row { transition: background 0.1s ease; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; letter-spacing: 0.02em; padding: 2px 8px; border-radius: 20px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100,116,139,0.3); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100,116,139,0.5); }

        /* Dropdown */
        .dropdown-menu {
            opacity: 0;
            transform: translateY(6px) scale(0.97);
            pointer-events: none;
            transition: all 0.15s ease;
        }
        .dropdown-menu.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        /* Command palette */
        #cmdPalette { transition: all 0.2s ease; }
        #cmdOverlay { transition: opacity 0.2s ease; }

        /* Sidebar collapse */
        #sidebar { transition: transform 0.2s ease, width 0.2s ease; }

        /* Pulse dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse { animation: pulse-dot 2s ease-in-out infinite; }

        /* Dark mode */
        html.dark { color-scheme: dark; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0D0F14] text-slate-900 dark:text-slate-100 min-h-full antialiased">


<div id="cmdOverlay" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none" onclick="closeCmdPalette()"></div>
<div id="cmdPalette" class="fixed top-[15%] left-1/2 -translate-x-1/2 z-50 w-full max-w-xl pointer-events-none opacity-0 scale-95">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <svg class="h-4 w-4 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
            <input id="cmdInput" type="text" placeholder="Rechercher tickets, articles, utilisateurs…"
                   class="flex-1 text-sm bg-transparent border-0 outline-none text-slate-800 dark:text-slate-100 placeholder-slate-400">
            <kbd class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 rounded font-mono">ESC</kbd>
        </div>
        <div class="p-2 max-h-80 overflow-y-auto">
            <p class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Navigation rapide</p>
            <a href="<?php echo e(route('support.dashboard')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd"/></svg>
                </span>
                Dashboard
            </a>
            <a href="<?php echo e(route('support.ui.tickets.index')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z" clip-rule="evenodd"/></svg>
                </span>
                Mes Tickets
            </a>
            <a href="<?php echo e(route('support.ui.knowledge.index')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 16.82A7.462 7.462 0 0 1 10 17c-.314 0-.62-.02-.922-.06A7.5 7.5 0 0 1 2.5 10a7.5 7.5 0 0 1 15 0 7.5 7.5 0 0 1-3.5 6.386v-1.606a6 6 0 0 0-3.25-.573v2.613Z"/></svg>
                </span>
                Base de connaissance
            </a>
        </div>
    </div>
</div>

<div class="flex min-h-screen">

    
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 flex flex-col border-r border-slate-800/80
                  bg-[#111318] transform -translate-x-full md:translate-x-0">

        
        <div class="h-14 flex items-center gap-3 px-4 border-b border-slate-800/80 shrink-0">
            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5Zm14 1a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM2 13a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2Zm14 1a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="brand-font block text-sm font-bold text-white leading-none">Support IT</span>
                <span class="block text-[10px] text-slate-500 leading-none mt-0.5 font-medium">Plateforme intelligente</span>
            </div>
        </div>

        
        <div class="px-3 py-3 border-b border-slate-800/60">
            <button onclick="openCmdPalette()"
                    class="search-bar w-full flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-400 text-xs hover:border-slate-600 hover:bg-slate-800 transition">
                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                </svg>
                <span class="flex-1 text-left">Rechercher…</span>
                <kbd class="text-[9px] px-1.5 py-0.5 bg-slate-700 text-slate-500 rounded font-mono">⌘K</kbd>
            </button>
        </div>

        
        <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-5">

            <div>
                <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Vue générale</p>
                <a href="<?php echo e(route('support.dashboard')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('support.dashboard') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            <div>
                <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Tickets & Support</p>
                <a href="<?php echo e(route('support.ui.tickets.index')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('support.ui.tickets.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Zm1 3.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1 0-1.5Zm0 3A.75.75 0 0 1 7.25 9h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1 0-1.5Zm0 3a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 0 1.5h-3a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Tickets</span>
                </a>
                <a href="<?php echo e(route('support.ui.conversations.index')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('support.ui.conversations.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Conversations Clients</span>
                    <span id="sidebarClientMessagesBadge" class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500 text-white" style="display: none;"></span>
                </a>
                <a href="<?php echo e(route('support.ui.internal.index')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('support.ui.internal.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    <span>Communication Interne</span>
                    <span id="sidebarInternalMessagesBadge" class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500 text-white" style="display: none;"></span>
                </a>
                <a href="<?php echo e(route('support.ui.tickets.index', ['is_escalated' => 1])); ?>"
                   class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495ZM10 5.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm0 8.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Escalades</span>
                    <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500/20 text-[10px] font-bold text-red-400">3</span>
                </a>
            </div>

            <div>
                <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Ressources</p>
                <a href="<?php echo e(route('support.ui.knowledge.index')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('support.ui.knowledge.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/>
                    </svg>
                    <span>Base de connaissance</span>
                </a>
            </div>

            <div>
                <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Configuration</p>
                     <a href="<?php echo e(route('support.ui.notifications.index')); ?>#"
                         class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Zm0 14.5a2 2 0 0 1-1.95-1.557 33.54 33.54 0 0 0 3.9 0A2 2 0 0 1 10 16.5Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Notifications</span>
                    <span id="sidebarNotifDot" class="ml-auto h-2 w-2 rounded-full bg-blue-500 pulse" style="display: none;"></span>
                </a>
                     <a href="<?php echo e(route('support.ui.settings.index')); ?>"
                         class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.34 1.804A1 1 0 0 1 9.32 1h1.36a1 1 0 0 1 .98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 0 1 1.262.125l.962.962a1 1 0 0 1 .125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 0 1 .804.98v1.361a1 1 0 0 1-.804.98l-1.473.295a6.95 6.95 0 0 1-.587 1.416l.834 1.25a1 1 0 0 1-.125 1.262l-.962.962a1 1 0 0 1-1.262.125l-1.25-.834a6.953 6.953 0 0 1-1.416.587l-.294 1.473a1 1 0 0 1-.98.804H9.32a1 1 0 0 1-.98-.804l-.295-1.473a6.957 6.957 0 0 1-1.416-.587l-1.25.834a1 1 0 0 1-1.262-.125l-.962-.962a1 1 0 0 1-.125-1.262l.834-1.25a6.957 6.957 0 0 1-.587-1.416l-1.473-.294A1 1 0 0 1 1 10.68V9.32a1 1 0 0 1 .804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 0 1 .125-1.262l.962-.962A1 1 0 0 1 5.38 3.03l1.25.834a6.957 6.957 0 0 1 1.416-.587L8.34 1.804ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                    </svg>
                    <span>Paramètres</span>
                </a>
            </div>
        </nav>

        
        <div class="shrink-0 p-3 border-t border-slate-800/80">
            <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-slate-800/60 transition cursor-pointer group">
                <div class="relative shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-violet-600 text-xs font-bold text-white ring-2 ring-slate-700">
                        <?php echo e(Str::upper(Str::substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                    </div>
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-[#111318]"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-slate-200"><?php echo e(auth()->user()->name ?? 'Agent'); ?></p>
                    <p class="truncate text-[10px] text-slate-500"><?php echo e(auth()->user()->email ?? 'support@it.com'); ?></p>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" title="Déconnexion"
                            class="flex items-center justify-center h-7 w-7 rounded-lg text-slate-600 hover:text-red-400 hover:bg-red-500/10 transition opacity-0 group-hover:opacity-100">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 4.25A2.25 2.25 0 0 0 14.75 2h-5.5A2.25 2.25 0 0 0 7 4.25v2a.75.75 0 0 0 1.5 0v-2a.75.75 0 0 1 .75-.75h5.5a.75.75 0 0 1 .75.75v11.5a.75.75 0 0 1-.75.75h-5.5a.75.75 0 0 1-.75-.75v-2a.75.75 0 0 0-1.5 0v2A2.25 2.25 0 0 0 9.25 18h5.5A2.25 2.25 0 0 0 17 15.75V4.25Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M14 10a.75.75 0 0 0-.75-.75H3.704l1.048-1.08a.75.75 0 1 0-1.08-1.04l-2.5 2.59a.75.75 0 0 0 0 1.04l2.5 2.59a.75.75 0 1 0 1.08-1.04l-1.048-1.08H13.25A.75.75 0 0 0 14 10Z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    
    <div id="sidebarOverlay"
         class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm hidden md:hidden"
         onclick="toggleSidebar()"></div>

    
    <div class="main-wrap flex flex-1 flex-col min-w-0">

        
        <header class="sticky top-0 z-10 flex items-center gap-3 px-4 md:px-6 bg-white/80 dark:bg-[#0D0F14]/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80"
                style="height: var(--header-h);">

            
            <button onclick="toggleSidebar()"
                    class="md:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                </svg>
            </button>

            
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-slate-400 dark:text-slate-600 text-sm hidden sm:block">Support IT</span>
                <svg class="h-3.5 w-3.5 text-slate-300 dark:text-slate-700 hidden sm:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                <h1 class="brand-font text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                    <?php echo $__env->yieldContent('title', 'Dashboard'); ?>
                </h1>
            </div>

            
            <div class="ml-auto flex items-center gap-1.5">

                
                <?php if(session('success')): ?>
                    <div class="hidden sm:flex items-center gap-1.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1.5 text-xs text-emerald-700 dark:text-emerald-400 font-medium">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                
                <button onclick="openCmdPalette()"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 transition">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                    Rechercher…
                    <kbd class="text-[9px] px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-400 rounded font-mono">⌘K</kbd>
                </button>

                
                <button id="darkToggle"
                        class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        title="Basculer le thème">
                    <svg class="h-4 w-4 hidden dark:block" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1Zm4 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-.464 4.95.707.707a1 1 0 0 0 1.414-1.414l-.707-.707a1 1 0 0 0-1.414 1.414Zm2.12-10.607a1 1 0 0 1 0 1.414l-.706.707a1 1 0 1 1-1.414-1.414l.707-.707a1 1 0 0 1 1.414 0ZM17 11a1 1 0 1 0 0-2h-1a1 1 0 1 0 0 2h1Zm-7 4a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0v-1a1 1 0 0 1 1-1ZM5.05 6.464A1 1 0 1 0 6.465 5.05l-.708-.707a1 1 0 0 0-1.414 1.414l.707.707Zm1.414 8.486-.707.707a1 1 0 0 1-1.414-1.414l.707-.707a1 1 0 0 1 1.414 1.414ZM4 11a1 1 0 1 0 0-2H3a1 1 0 0 0 0 2h1Z"/>
                    </svg>
                    <svg class="h-4 w-4 block dark:hidden" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"/>
                    </svg>
                </button>

                
                <div class="relative">
                    <button onclick="toggleDropdown('notifMenu')"
                            class="relative p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Zm0 14.5a2 2 0 0 1-1.95-1.557 33.54 33.54 0 0 0 3.9 0A2 2 0 0 1 10 16.5Z" clip-rule="evenodd"/>
                        </svg>
                        <?php
                            use Illuminate\Support\Facades\Schema;
                            use App\Models\Notification as AppNotification;
                            $unreadCount = 0;
                            if (auth()->check()) {
                                if (Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id')) {
                                    $unreadCount = auth()->user()->unreadNotifications()->count();
                                } else {
                                    try {
                                        $unreadCount = AppNotification::where('status', 'pending')->count();
                                    } catch (\Throwable $e) {
                                        $unreadCount = 0;
                                    }
                                }
                            }
                        ?>
                        <?php if($unreadCount > 0): ?>
                            <span id="notifBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 min-w-[20px] px-1 text-[11px] font-semibold rounded-full bg-red-500 text-white"><?php echo e($unreadCount); ?></span>
                        <?php else: ?>
                            <span id="notifBadge" class="absolute top-0.5 right-0.5 h-2 w-2 rounded-full bg-transparent"></span>
                        <?php endif; ?>
                    </button>
                    <div id="notifMenu" class="dropdown-menu absolute right-0 mt-1.5 w-80 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-black/10 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Notifications</p>
                            <span class="badge bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400"><?php echo e($unreadCount); ?> nouvelles</span>
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-64 overflow-y-auto">
                            <a href="#" class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-slate-800 dark:text-slate-200 truncate">Nouveau ticket #1042</p>
                                    <p class="text-xs text-slate-500 truncate">Problème VPN — il y a 5 min</p>
                                </div>
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                            </a>
                            <a href="#" class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 text-red-600">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495Z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-slate-800 dark:text-slate-200 truncate">Escalade critique #1039</p>
                                    <p class="text-xs text-slate-500 truncate">Serveur down — il y a 12 min</p>
                                </div>
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                            </a>
                        </div>
                        <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-700">
                            <a href="<?php echo e(route('support.ui.notifications.index')); ?>" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">Voir tout →</a>
                        </div>
                    </div>
                </div>

                
                <div class="relative pl-1.5 border-l border-slate-200 dark:border-slate-700">
                    <button onclick="toggleDropdown('userMenu')"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-violet-600 text-[10px] font-bold text-white">
                            <?php echo e(Str::upper(Str::substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                        </div>
                        <span class="hidden sm:block text-xs font-medium text-slate-700 dark:text-slate-300">
                            <?php echo e(auth()->user()->name ?? 'Agent'); ?>

                        </span>
                        <svg class="hidden sm:block h-3 w-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <div id="userMenu" class="dropdown-menu absolute right-0 mt-1.5 w-52 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-black/10 overflow-hidden py-1">
                        <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700 mb-1">
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200"><?php echo e(auth()->user()->name ?? 'Agent'); ?></p>
                            <p class="text-[10px] text-slate-500"><?php echo e(auth()->user()->email ?? ''); ?></p>
                        </div>
                        <a href="<?php echo e(route('support.ui.settings.index')); ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd"/></svg>
                            Mon profil
                        </a>
                        <a href="#" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.34 1.804A1 1 0 0 1 9.32 1h1.36a1 1 0 0 1 .98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 0 1 1.262.125l.962.962a1 1 0 0 1 .125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 0 1 .804.98v1.361a1 1 0 0 1-.804.98l-1.473.295a6.95 6.95 0 0 1-.587 1.416l.834 1.25a1 1 0 0 1-.125 1.262l-.962.962a1 1 0 0 1-1.262.125l-1.25-.834a6.953 6.953 0 0 1-1.416.587l-.294 1.473a1 1 0 0 1-.98.804H9.32a1 1 0 0 1-.98-.804l-.295-1.473a6.957 6.957 0 0 1-1.416-.587l-1.25.834a1 1 0 0 1-1.262-.125l-.962-.962a1 1 0 0 1-.125-1.262l.834-1.25a6.957 6.957 0 0 1-.587-1.416l-1.473-.294A1 1 0 0 1 1 10.68V9.32a1 1 0 0 1 .804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 0 1 .125-1.262l.962-.962A1 1 0 0 1 5.38 3.03l1.25.834a6.957 6.957 0 0 1 1.416-.587L8.34 1.804ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
                            Paramètres
                        </a>
                        <div class="border-t border-slate-100 dark:border-slate-700 mt-1 pt-1">
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
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

        
        <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 dark:bg-[#0D0F14]">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>

<script>
// Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isOpen  = !sidebar.classList.contains('-translate-x-full');
    sidebar.classList.toggle('-translate-x-full', isOpen);
    overlay.classList.toggle('hidden', isOpen);
}

// Dark mode
document.getElementById('darkToggle')?.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    // sync settings page toggle if present
    const settingsToggle = document.getElementById('settingsThemeToggle');
    if (settingsToggle) settingsToggle.checked = isDark;
});
(function() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) document.documentElement.classList.add('dark');
})();

// Dropdowns
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const isOpen = menu.classList.contains('open');
    // Close all
    document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) menu.classList.add('open');
}
document.addEventListener('click', (e) => {
    if (!e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    }
});

// Command Palette
function openCmdPalette() {
    const overlay = document.getElementById('cmdOverlay');
    const palette = document.getElementById('cmdPalette');
    overlay.classList.remove('pointer-events-none');
    overlay.style.opacity = '1';
    palette.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
    palette.style.opacity = '1';
    setTimeout(() => document.getElementById('cmdInput')?.focus(), 50);
}
function closeCmdPalette() {
    const overlay = document.getElementById('cmdOverlay');
    const palette = document.getElementById('cmdPalette');
    overlay.style.opacity = '0';
    palette.style.opacity = '0';
    setTimeout(() => {
        overlay.classList.add('pointer-events-none');
        palette.classList.add('pointer-events-none');
    }, 200);
}
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); openCmdPalette(); }
    if (e.key === 'Escape') closeCmdPalette();
});

// Poll unread notifications count and update header badge + sidebar dot
(function(){
    async function fetchUnread() {
        try {
            const res = await fetch('<?php echo e(url("equipeIT/ui/notifications/unread-count")); ?>', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const j = await res.json();
            const count = parseInt(j.unread || 0, 10);
            const badge = document.getElementById('notifBadge');
            const dot = document.getElementById('sidebarNotifDot');
            if (badge) {
                if (count > 0) { badge.textContent = count; badge.classList.remove('bg-transparent'); }
                else { badge.textContent = ''; badge.classList.add('bg-transparent'); }
            }
            if (dot) dot.style.display = count > 0 ? '' : 'none';
        } catch (e) {
            // ignore
        }
    }
    // initial fetch + periodic
    fetchUnread();
    setInterval(fetchUnread, 10000);
})();
</script>
</body>
</html><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/support/layouts/app.blade.php ENDPATH**/ ?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 flex flex-col border-r border-slate-800/80 bg-[#111318] transform -translate-x-full md:translate-x-0">
    
    <div class="h-14 flex items-center gap-3 px-4 border-b border-slate-800/80 shrink-0">
        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-red-600 shadow-lg shadow-amber-500/30">
            <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
        </div>
        <div class="min-w-0">
            <span class="brand-font block text-sm font-bold text-white leading-none">Admin Console</span>
            <span class="block text-[10px] text-slate-500 leading-none mt-0.5 font-medium">Support IT · v2.0</span>
        </div>
    </div>

    
    <div class="px-3 py-3 border-b border-slate-800/60">
        <button onclick="openCmdPalette()" class="search-bar w-full flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-400 text-xs hover:border-slate-600 hover:bg-slate-800 transition">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
            <span class="flex-1 text-left">Rechercher…</span>
            <kbd class="text-[9px] px-1.5 py-0.5 bg-slate-700 text-slate-500 rounded font-mono">⌘K</kbd>
        </button>
    </div>

    
    <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-5">

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Vue générale</p>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 1 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Support & Communication</p>

            <a href="<?php echo e(route('admin.ui.conversations.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.conversations.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg>
                <span>Conversations Clients</span>
                <span id="sidebarClientMessagesBadge" class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950" style="display: none;"></span>
            </a>

            <a href="<?php echo e(route('admin.ui.internal.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.internal.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                <span>Communication Interne</span>
                <span id="sidebarInternalMessagesBadge" class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950" style="display: none;"></span>
            </a>

            <a href="<?php echo e(route('admin.ui.tickets.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.tickets.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Zm1 3.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1-.75-.75Zm0 3.25A.75.75 0 0 1 7.25 9.25h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 10Zm.75 2.5h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1 0-1.5Z" clip-rule="evenodd"/></svg>
                <span>Tickets</span>
            </a>
        </div>

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Ressources</p>
            <a href="<?php echo e(route('admin.ui.knowledge.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.knowledge.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/></svg>
                <span>Base de connaissance</span>
            </a>
        </div>

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Système</p>
            <a href="<?php echo e(route('admin.notifications')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.notifications*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Zm0 14.5a2 2 0 0 1-1.95-1.557 33.54 33.54 0 0 0 3.9 0A2 2 0 0 1 10 16.5Z" clip-rule="evenodd"/></svg>
                <span>Notifications</span>
                <span id="sidebarNotifDot" class="ml-auto h-2 w-2 rounded-full bg-amber-500 pulse" style="display: none;"></span>
            </a>

            <a href="<?php echo e(route('admin.ui.monitoring.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.monitoring.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h11.5a.75.75 0 0 0 .75-.75v-8.5a.75.75 0 0 0-.75-.75H4.25ZM3 6.25A1.75 1.75 0 0 1 4.75 4.5h10.5A1.75 1.75 0 0 1 17 6.25v8.5A1.75 1.75 0 0 1 15.25 16.5H4.75A1.75 1.75 0 0 1 3 14.75v-8.5ZM6.5 8.25A.75.75 0 0 1 7.25 7.5h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 8.25Zm0 3A.75.75 0 0 1 7.25 10.5h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
                <span>Monitoring système</span>
            </a>
        </div>

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Administration</p>
            <a href="<?php echo e(route('admin.ui.users.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.users.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                <span>Utilisateurs</span>
            </a>

            <a href="<?php echo e(route('admin.ui.settings.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.ui.settings.*') ? 'active' : ''); ?> flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.34 1.804A1 1 0 0 1 9.32 1h1.36a1 1 0 0 1 .98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 0 1 1.262.125l.962.962a1 1 0 0 1 .125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 0 1 .804.98v1.361a1 1 0 0 1-.804.98l-1.473.295a6.95 6.95 0 0 1-.587 1.416l.834 1.25a1 1 0 0 1-.125 1.262l-.962.962a1 1 0 0 1-1.262.125l-1.25-.834a6.953 6.953 0 0 1-1.416.587l-.294 1.473a1 1 0 0 1-.98.804H9.32a1 1 0 0 1-.98-.804l-.295-1.473a6.957 6.957 0 0 1-1.416-.587l-1.25.834a1 1 0 0 1-1.262-.125l-.962-.962a1 1 0 0 1-.125-1.262l.834-1.25a6.957 6.957 0 0 1-.587-1.416l-1.473-.294A1 1 0 0 1 1 10.68V9.32a1 1 0 0 1 .804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 0 1 .125-1.262l.962-.962A1 1 0 0 1 5.38 3.03l1.25.834a6.957 6.957 0 0 1 1.416-.587L8.34 1.804ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
                <span>Paramètres</span>
            </a>
        </div>

        
        <div>
            <p class="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Session</p>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-red-400 transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 4.25A2.25 2.25 0 0 0 14.75 2h-5.5A2.25 2.25 0 0 0 7 4.25v2a.75.75 0 0 0 1.5 0v-2a.75.75 0 0 1 .75-.75h5.5a.75.75 0 0 1 .75.75v11.5a.75.75 0 0 1-.75.75h-5.5a.75.75 0 0 1-.75-.75v-2a.75.75 0 0 0-1.5 0v2A2.25 2.25 0 0 0 9.25 18h5.5A2.25 2.25 0 0 0 17 15.75V4.25Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M14 10a.75.75 0 0 0-.75-.75H3.704l1.048-1.08a.75.75 0 1 0-1.08-1.04l-2.5 2.59a.75.75 0 0 0 0 1.04l2.5 2.59a.75.75 0 1 0 1.08-1.04l-1.048-1.08H13.25A.75.75 0 0 0 14 10Z" clip-rule="evenodd"/></svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </nav>

    
    <div class="shrink-0 p-3 border-t border-slate-800/80">
        <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-slate-800/60 transition cursor-pointer group">
            <div class="relative shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-red-600 text-xs font-bold text-white ring-2 ring-slate-700">
                    <?php echo e(Str::upper(Str::substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                </div>
                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-[#111318]"></span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-semibold text-slate-200"><?php echo e(auth()->user()->name ?? 'Admin'); ?></p>
                <p class="truncate text-[10px] text-slate-500"><?php echo e(auth()->user()->email ?? 'admin@it.com'); ?></p>
            </div>
        </div>
    </div>
</aside>
<?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/admin/layouts/partials/sidebar.blade.php ENDPATH**/ ?>
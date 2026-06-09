<div id="cmdOverlay" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none" onclick="closeCmdPalette()"></div>
<div id="cmdPalette" class="fixed top-[15%] left-1/2 -translate-x-1/2 z-50 w-full max-w-xl pointer-events-none opacity-0 scale-95">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <svg class="h-4 w-4 text-slate-400 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
            <input id="cmdInput" type="text" placeholder="Rechercher…" class="flex-1 text-sm bg-transparent border-0 outline-none text-slate-800 dark:text-slate-100 placeholder-slate-400">
            <kbd class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 rounded font-mono">ESC</kbd>
        </div>
        <div class="p-2 max-h-80 overflow-y-auto">
            <p class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Navigation rapide</p>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600"><svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 1 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z"/></svg></span>
                Dashboard
            </a>
            <a href="<?php echo e(route('admin.ui.tickets.index')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600"><svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z" clip-rule="evenodd"/></svg></span>
                Tickets
            </a>
            <a href="<?php echo e(route('admin.ui.conversations.index')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600"><svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg></span>
                Conversations
            </a>
            <a href="<?php echo e(route('admin.ui.knowledge.index')); ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 text-sm text-slate-700 dark:text-slate-200 transition">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600"><svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg></span>
                Base de connaissance
            </a>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/admin/layouts/partials/cmd-palette.blade.php ENDPATH**/ ?>
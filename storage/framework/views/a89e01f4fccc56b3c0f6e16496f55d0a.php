<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">
            <?php echo e(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')); ?>

        </p>
        <h2 class="brand-font text-2xl font-bold text-slate-900 dark:text-white">
            Bonjour, <?php echo e(auth()->user()->name ?? 'Agent'); ?> 👋
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Voici un aperçu de l'activité support d'aujourd'hui.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo e(route('support.ui.tickets.index')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-500/30 transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
            Nouveau ticket
        </a>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

    
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Zm1 3.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1-.75-.75Zm0 3.25A.75.75 0 0 1 7.25 9.25h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 10Zm.75 2.5h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1 0-1.5Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd"/></svg>
                +12%
            </span>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">248</p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Total tickets</p>
        <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width: 72%"></div>
        </div>
    </div>

    
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">
                En attente
            </span>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">43</p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">En cours</p>
        <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-amber-400 rounded-full" style="width: 43%"></div>
        </div>
    </div>

    
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd"/></svg>
                +8%
            </span>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">185</p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Résolus ce mois</p>
        <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-500 rounded-full" style="width: 85%"></div>
        </div>
    </div>

    
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/30">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495ZM10 5.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm0 8.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">
                Urgent
            </span>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">3</p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Escalades critiques</p>
        <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-red-500 rounded-full" style="width: 15%"></div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-5">

    
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Tickets récents</h3>
            <a href="<?php echo e(route('support.ui.tickets.index')); ?>" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 transition">Voir tout →</a>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            <?php
            $mockTickets = [
                ['id' => '#1042', 'title' => 'Connexion VPN impossible depuis le bureau', 'status' => 'open', 'priority' => 'high', 'user' => 'Marie D.', 'time' => 'il y a 5 min'],
                ['id' => '#1041', 'title' => 'Imprimante HP ne répond plus', 'status' => 'in_progress', 'priority' => 'medium', 'user' => 'Thomas L.', 'time' => 'il y a 23 min'],
                ['id' => '#1040', 'title' => 'Outlook ne synchronise pas les emails', 'status' => 'open', 'priority' => 'low', 'user' => 'Sarah M.', 'time' => 'il y a 1h'],
                ['id' => '#1039', 'title' => 'Serveur de fichiers inaccessible — CRITIQUE', 'status' => 'escalated', 'priority' => 'critical', 'user' => 'Pierre V.', 'time' => 'il y a 2h'],
                ['id' => '#1038', 'title' => 'Mise à jour Windows bloquée à 35%', 'status' => 'resolved', 'priority' => 'low', 'user' => 'Ana R.', 'time' => 'il y a 3h'],
            ];
            $statusMap = [
                'open' => ['label' => 'Ouvert', 'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                'in_progress' => ['label' => 'En cours', 'class' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
                'escalated' => ['label' => 'Escalade', 'class' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
                'resolved' => ['label' => 'Résolu', 'class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
            ];
            $priorityMap = [
                'critical' => 'bg-red-500',
                'high' => 'bg-orange-500',
                'medium' => 'bg-amber-400',
                'low' => 'bg-slate-300 dark:bg-slate-600',
            ];
            ?>
            <?php $__currentLoopData = $mockTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">
                <span class="h-2 w-2 shrink-0 rounded-full <?php echo e($priorityMap[$t['priority']]); ?>"></span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs font-mono font-medium text-slate-400 dark:text-slate-500 shrink-0"><?php echo e($t['id']); ?></span>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate"><?php echo e($t['title']); ?></p>
                    </div>
                    <div class="flex items-center gap-3 mt-0.5">
                        <span class="text-[11px] text-slate-400"><?php echo e($t['user']); ?></span>
                        <span class="text-[11px] text-slate-300 dark:text-slate-600">·</span>
                        <span class="text-[11px] text-slate-400"><?php echo e($t['time']); ?></span>
                    </div>
                </div>
                <span class="badge <?php echo e($statusMap[$t['status']]['class']); ?> shrink-0"><?php echo e($statusMap[$t['status']]['label']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="space-y-4">

        
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Activité récente</h3>
            </div>
            <div class="p-4 space-y-3">
                <?php
                $activities = [
                    ['icon' => 'check', 'color' => 'emerald', 'text' => 'Ticket #1038 résolu', 'time' => '14:32'],
                    ['icon' => 'comment', 'color' => 'blue', 'text' => 'Nouveau commentaire sur #1041', 'time' => '14:10'],
                    ['icon' => 'alert', 'color' => 'red', 'text' => 'Escalade créée pour #1039', 'time' => '12:55'],
                    ['icon' => 'plus', 'color' => 'violet', 'text' => 'Ticket #1042 créé', 'time' => '12:30'],
                    ['icon' => 'book', 'color' => 'amber', 'text' => 'Article KB mis à jour', 'time' => '11:15'],
                ];
                $actColors = ['emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600', 'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600', 'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600', 'violet' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-600', 'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600'];
                ?>
                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full <?php echo e($actColors[$a['color']]); ?>">
                        <?php if($a['icon'] === 'check'): ?>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        <?php elseif($a['icon'] === 'comment'): ?>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z" clip-rule="evenodd"/></svg>
                        <?php elseif($a['icon'] === 'alert'): ?>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495Z" clip-rule="evenodd"/></svg>
                        <?php elseif($a['icon'] === 'plus'): ?>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                        <?php else: ?>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate"><?php echo e($a['text']); ?></p>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400 shrink-0"><?php echo e($a['time']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
            <p class="text-xs font-semibold opacity-70 uppercase tracking-wider mb-3">Temps de réponse moyen</p>
            <p class="brand-font text-4xl font-bold mb-1">1h 42m</p>
            <p class="text-xs opacity-70 mb-4">Objectif SLA : 2 heures</p>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full" style="width: 85%"></div>
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-[10px] opacity-60">85% dans les délais</span>
                <span class="text-[10px] opacity-60">SLA</span>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/dashboard.blade.php ENDPATH**/ ?>
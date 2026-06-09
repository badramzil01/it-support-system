
<?php $__env->startSection('title', 'Monitoring système'); ?>
<?php $__env->startSection('content'); ?>

<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="brand-font text-xl font-bold text-slate-900 dark:text-white">État des services</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Surveillance en temps réel · <?php echo e(now()->format('d/m/Y H:i')); ?></p>
        </div>
        <a href="<?php echo e(route('admin.ui.monitoring.index')); ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.433a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd"/></svg>
            Rafraîchir
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $statusColor = match($s['status']) {
                'online' => 'bg-emerald-500',
                'degraded' => 'bg-amber-500',
                'offline' => 'bg-red-500',
                default => 'bg-slate-400',
            };
            $statusLabel = match($s['status']) {
                'online' => ['text' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'En ligne'],
                'degraded' => ['text' => 'text-amber-600 dark:text-amber-400', 'label' => 'Dégradé'],
                'offline' => ['text' => 'text-red-600 dark:text-red-400', 'label' => 'Hors ligne'],
                default => ['text' => 'text-slate-500 dark:text-slate-400', 'label' => 'Inconnu'],
            };
            $bgColor = match($s['status']) {
                'online' => 'bg-emerald-50 dark:bg-emerald-900/20',
                'degraded' => 'bg-amber-50 dark:bg-amber-900/20',
                'offline' => 'bg-red-50 dark:bg-red-900/20',
                default => 'bg-slate-50 dark:bg-slate-800',
            };
        ?>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl <?php echo e($bgColor); ?>">
                        <?php if($s['icon'] === 'server'): ?>
                        <svg class="h-5 w-5 <?php echo e($statusLabel['text']); ?>" viewBox="0 0 20 20" fill="currentColor"><path d="M4.464 3.162A2 2 0 0 1 6.279 2h7.442a2 2 0 0 1 1.815 1.162l1.484 3.183A2 2 0 0 1 17 7.755V15a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7.755a2 2 0 0 1 .02-1.41l1.444-3.183Z"/></svg>
                        <?php elseif($s['icon'] === 'database'): ?>
                        <svg class="h-5 w-5 <?php echo e($statusLabel['text']); ?>" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1c3.3 0 6 1 6 2.5V15c0 1.5-2.7 2.5-6 2.5s-6-1-6-2.5V3.5C4 2 6.7 1 10 1Z"/><path d="M4 7.5c0 1.5 2.7 2.5 6 2.5s6-1 6-2.5M4 12c0 1.5 2.7 2.5 6 2.5s6-1 6-2.5"/></svg>
                        <?php elseif($s['icon'] === 'flow'): ?>
                        <svg class="h-5 w-5 <?php echo e($statusLabel['text']); ?>" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.577 4.878a.75.75 0 0 1 .919-.53l4.78 1.281a.75.75 0 0 1 .531.919l-1.281 4.78a.75.75 0 0 1-1.449-.387l.81-3.022a19.407 19.407 0 0 0-5.594 5.203.75.75 0 0 1-1.139.093L7 10.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06l5.25-5.25a.75.75 0 0 1 1.06 0l3.074 3.073a20.923 20.923 0 0 1 5.545-4.931l-3.042.815a.75.75 0 0 1-.53-.919Z" clip-rule="evenodd"/></svg>
                        <?php elseif($s['icon'] === 'mail'): ?>
                        <svg class="h-5 w-5 <?php echo e($statusLabel['text']); ?>" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                        <?php else: ?>
                        <svg class="h-5 w-5 <?php echo e($statusLabel['text']); ?>" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.535 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.636-.544.298-1.584-.535-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/></svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white"><?php echo e($s['name']); ?></p>
                        <p class="text-[11px] <?php echo e($statusLabel['text']); ?> font-medium flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full <?php echo e($statusColor); ?> <?php echo e($s['status']==='online' ? 'pulse' : ''); ?>"></span>
                            <?php echo e($statusLabel['label']); ?>

                        </p>
                    </div>
                </div>
            </div>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Version</dt><dd class="font-medium text-slate-700 dark:text-slate-200"><?php echo e($s['version']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Temps de réponse</dt><dd class="font-medium text-slate-700 dark:text-slate-200"><?php echo e($s['response']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Uptime</dt><dd class="font-medium text-slate-700 dark:text-slate-200"><?php echo e($s['uptime']); ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Dernière sync</dt><dd class="font-medium text-slate-700 dark:text-slate-200"><?php echo e($s['last_sync']); ?></dd></div>
                <?php if($s['last_error']): ?>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <dt class="text-slate-500 dark:text-slate-400 mb-1">Dernière erreur</dt>
                    <dd class="text-[11px] text-red-600 dark:text-red-400 font-mono break-all"><?php echo e($s['last_error']); ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/admin/monitoring/index.blade.php ENDPATH**/ ?>
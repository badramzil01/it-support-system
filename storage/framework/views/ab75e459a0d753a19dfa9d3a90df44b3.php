
<?php $__env->startSection('title', 'Ticket #'.$ticket->id); ?>
<?php use Illuminate\Support\Str; ?>
<?php $__env->startSection('content'); ?>

<?php
$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950', 'text' => 'text-red-700 dark:text-red-300', 'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-950', 'text' => 'text-blue-700 dark:text-blue-300'],
    'in_progress' => ['bg' => 'bg-violet-100 dark:bg-violet-950', 'text' => 'text-violet-700 dark:text-violet-300'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950', 'text' => 'text-emerald-700 dark:text-emerald-300'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-500 dark:text-slate-400'],
];
$pCfg = $priorityConfig[$ticket->priority] ?? $priorityConfig['low'];
$sCfg = $statusConfig[$ticket->status] ?? $statusConfig['closed'];
?>

<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="<?php echo e(route('admin.ui.tickets.index')); ?>" class="hover:text-amber-600 dark:hover:text-amber-400">← Retour aux tickets</a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-mono text-slate-400 mb-1">#<?php echo e($ticket->id); ?></p>
                <h1 class="brand-font text-2xl font-bold text-slate-900 dark:text-white"><?php echo e($ticket->title); ?></h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Créé <?php echo e($ticket->created_at->diffForHumans()); ?> · <?php echo e($ticket->created_at->format('d/m/Y H:i')); ?></p>
            </div>
            <div class="flex flex-col items-end gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium <?php echo e($pCfg['bg']); ?> <?php echo e($pCfg['text']); ?>">
                    <span class="h-1.5 w-1.5 rounded-full <?php echo e($pCfg['dot']); ?>"></span><?php echo e(ucfirst($ticket->priority ?? 'low')); ?>

                </span>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?php echo e($sCfg['bg']); ?> <?php echo e($sCfg['text']); ?>"><?php echo e(ucfirst(str_replace('_',' ',$ticket->status))); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
            <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3"><p class="text-[10px] uppercase text-slate-500">Utilisateur</p><p class="text-sm font-semibold mt-1"><?php echo e(optional($ticket->user)->name ?? '—'); ?></p></div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3"><p class="text-[10px] uppercase text-slate-500">Catégorie</p><p class="text-sm font-semibold mt-1"><?php echo e($ticket->category ?? '—'); ?></p></div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3"><p class="text-[10px] uppercase text-slate-500">Jira</p><p class="text-sm font-semibold mt-1"><?php echo e($ticket->jira_ticket_id ?? '—'); ?></p></div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-3"><p class="text-[10px] uppercase text-slate-500">Assigné à</p><p class="text-sm font-semibold mt-1"><?php echo e(optional($ticket->assignedAgent)->name ?? '—'); ?></p></div>
        </div>

        <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/50">
            <p class="text-[10px] uppercase text-slate-500 mb-2">Description</p>
            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line"><?php echo e($ticket->description ?: 'Aucune description.'); ?></p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-5 pt-5 border-t border-slate-200 dark:border-slate-800">
            <form method="POST" action="<?php echo e(route('admin.ui.tickets.updateStatus', $ticket)); ?>" class="flex items-center gap-2">
                <?php echo csrf_field(); ?>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 pl-2 pr-6 text-xs outline-none focus:border-amber-500 cursor-pointer">
                    <option value="open" <?php echo e($ticket->status=='open' ? 'selected' : ''); ?>>Open</option>
                    <option value="in_progress" <?php echo e($ticket->status=='in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="resolved" <?php echo e($ticket->status=='resolved' ? 'selected' : ''); ?>>Resolved</option>
                    <option value="closed" <?php echo e($ticket->status=='closed' ? 'selected' : ''); ?>>Closed</option>
                </select>
            </form>
            <?php if($ticket->jira_ticket_id): ?>
                <a href="<?php echo e(rtrim(config('services.jira.url','#'),'/').'/browse/'.$ticket->jira_ticket_id); ?>" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition">Ouvrir dans Jira</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/admin/tickets/show.blade.php ENDPATH**/ ?>
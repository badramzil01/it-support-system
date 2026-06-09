
<?php $__env->startSection('title', 'Tickets'); ?>
<?php $__env->startSection('content'); ?>
<?php
use Illuminate\Support\Str;

$priorityConfig = [
    'critical' => ['bg' => 'bg-red-100 dark:bg-red-950',    'text' => 'text-red-700 dark:text-red-300',    'dot' => 'bg-red-500'],
    'high'     => ['bg' => 'bg-orange-100 dark:bg-orange-950', 'text' => 'text-orange-700 dark:text-orange-300', 'dot' => 'bg-orange-500'],
    'medium'   => ['bg' => 'bg-amber-100 dark:bg-amber-950',  'text' => 'text-amber-700 dark:text-amber-300',  'dot' => 'bg-amber-500'],
    'low'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',  'text' => 'text-slate-600 dark:text-slate-300',  'dot' => 'bg-slate-400'],
];
$statusConfig = [
    'open'        => ['bg' => 'bg-blue-100 dark:bg-blue-950',    'text' => 'text-blue-700 dark:text-blue-300'],
    'in_progress' => ['bg' => 'bg-violet-100 dark:bg-violet-950','text' => 'text-violet-700 dark:text-violet-300'],
    'resolved'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950','text' => 'text-emerald-700 dark:text-emerald-300'],
    'closed'      => ['bg' => 'bg-slate-100 dark:bg-slate-800',  'text' => 'text-slate-500 dark:text-slate-400'],
];

function sortUrl($col, $sort, $direction) {
    $dir = ($sort === $col && $direction === 'desc') ? 'asc' : 'desc';
    return url()->current() . '?' . http_build_query(array_merge(request()->query(), ['sort' => $col, 'direction' => $dir]));
}
function sortIcon($col, $sort, $direction) {
    if ($sort !== $col) return '';
    return $direction === 'desc' ? '↓' : '↑';
}
?>

<div class="space-y-4">

    
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
        <form method="GET" action="<?php echo e(route('support.ui.tickets.index')); ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">

                
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Recherche</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                               placeholder="Titre, description…"
                               class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
                    </div>
                </div>

                
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Statut</label>
                    <select name="status" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                        <option value="">Tous</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>" <?php echo e(request('status') == $s ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$s))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Priorité</label>
                    <select name="priority" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                        <option value="">Toutes</option>
                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p); ?>" <?php echo e(request('priority') == $p ? 'selected' : ''); ?>><?php echo e(ucfirst($p)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Catégorie</label>
                    <select name="category" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                        <option value="">Toutes</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c); ?>" <?php echo e(request('category') == $c ? 'selected' : ''); ?>><?php echo e($c); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Depuis</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                           class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                </div>

                
                <div>
                    <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Jusqu'au</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                           class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                </div>

                
                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-6 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="is_urgent" value="1" <?php echo e(request('is_urgent') ? 'checked' : ''); ?>

                                   class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <span class="text-slate-600 dark:text-slate-300">Urgent</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="is_escalated" value="1" <?php echo e(request('is_escalated') ? 'checked' : ''); ?>

                                   class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-slate-600 dark:text-slate-300">Escaladé</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" name="assigned_to_me" value="1" <?php echo e(request('assigned_to_me') ? 'checked' : ''); ?>

                                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-600 dark:text-slate-300">Assigné à moi</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('support.ui.tickets.index')); ?>"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Réinitialiser
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 active:scale-95 transition">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
                            </svg>
                            Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">

        
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 dark:border-slate-800">
            <p class="text-sm font-semibold">
                <?php echo e($tickets->total()); ?> ticket<?php echo e($tickets->total() > 1 ? 's' : ''); ?>

                <?php if(request()->hasAny(['search','status','priority','category','is_urgent','is_escalated','assigned_to_me','date_from','date_to'])): ?>
                    <span class="ml-2 rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300">Filtrés</span>
                <?php endif; ?>
            </p>
            <p class="text-xs text-slate-400">Page <?php echo e($tickets->currentPage()); ?> / <?php echo e($tickets->lastPage()); ?></p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <?php
                            $cols = [
                                'id'             => 'ID',
                                'title'          => 'Titre',
                                'user'           => 'Utilisateur',
                                'category'       => 'Catégorie',
                                'priority'       => 'Priorité',
                                'status'         => 'Statut',
                                'jira_ticket_id' => 'Jira',
                                'created_at'     => 'Date',
                            ];
                        ?>
                        <?php $__currentLoopData = $cols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                <a href="<?php echo e(sortUrl($col, $sort ?? '', $direction ?? 'desc')); ?>"
                                   class="inline-flex items-center gap-1 hover:text-slate-900 dark:hover:text-slate-100 transition">
                                    <?php echo e($label); ?>

                                    <?php $icon = sortIcon($col, $sort ?? '', $direction ?? 'desc'); ?>
                                    <?php if($icon): ?>
                                        <span class="text-blue-600 dark:text-blue-400"><?php echo e($icon); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 dark:text-slate-600">↕</span>
                                    <?php endif; ?>
                                </a>
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Flags</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pCfg = $priorityConfig[$t->priority] ?? $priorityConfig['low'];
                            $sCfg = $statusConfig[$t->status]   ?? $statusConfig['closed'];
                        ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs font-mono text-slate-400">#<?php echo e($t->id); ?></span>
                            </td>

                            
                            <td class="px-4 py-3 max-w-xs">
                                <a href="<?php echo e(route('support.ui.tickets.show', $t)); ?>"
                                   class="font-medium text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition line-clamp-2">
                                    <?php echo e(Str::limit($t->title, 65)); ?>

                                </a>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if($t->user): ?>
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                            <?php echo e(Str::upper(Str::substr($t->user->name, 0, 1))); ?>

                                        </div>
                                        <span class="text-xs text-slate-700 dark:text-slate-200"><?php echo e($t->user->name); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">—</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-600 dark:text-slate-300"><?php echo e($t->category ?? '—'); ?></span>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium <?php echo e($pCfg['bg']); ?> <?php echo e($pCfg['text']); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo e($pCfg['dot']); ?>"></span>
                                    <?php echo e(ucfirst($t->priority ?? 'low')); ?>

                                </span>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium <?php echo e($sCfg['bg']); ?> <?php echo e($sCfg['text']); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $t->status ?? 'open'))); ?>

                                </span>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if($t->jira_ticket_id): ?>
                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300">
                                        <?php echo e($t->jira_ticket_id); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">—</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($t->created_at->format('d/m/Y')); ?></span>
                                <br>
                                <span class="text-[10px] text-slate-400"><?php echo e($t->created_at->format('H:i')); ?></span>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <?php if($t->is_urgent): ?>
                                        <span class="rounded-full bg-red-100 dark:bg-red-950 px-2 py-0.5 text-[10px] font-medium text-red-700 dark:text-red-300">Urgent</span>
                                    <?php endif; ?>
                                    <?php if($t->is_escalated): ?>
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2 py-0.5 text-[10px] font-medium text-amber-800 dark:text-amber-300">Escaladé</span>
                                    <?php endif; ?>
                                    <?php if(!$t->is_urgent && !$t->is_escalated): ?>
                                        <span class="text-[11px] text-slate-400">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <a href="<?php echo e(route('support.ui.tickets.show', $t)); ?>"
                                       class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-2.5 py-1.5 text-[11px] font-medium text-white hover:bg-blue-700 transition">
                                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41Z" clip-rule="evenodd"/></svg>
                                        Voir
                                    </a>

                                    <?php if($t->conversation_id): ?>
                                        <a href="<?php echo e(route('support.ui.conversations.index', ['conversation_id' => $t->conversation_id])); ?>"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5Zm3.293 1.293a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L7.586 10 5.293 7.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                                            Chat
                                        </a>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('support.ui.tickets.assignToMe', $t)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                                            Assigner
                                        </button>
                                    </form>

                                    <form method="POST" action="<?php echo e(route('support.ui.tickets.updateStatus', $t)); ?>" class="flex items-center gap-1">
                                        <?php echo csrf_field(); ?>
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 pl-2 pr-6 text-[11px] outline-none focus:border-blue-500 cursor-pointer">
                                            <option value="open"        <?php echo e($t->status=='open'        ? 'selected' : ''); ?>>Open</option>
                                            <option value="in_progress" <?php echo e($t->status=='in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                            <option value="resolved"    <?php echo e($t->status=='resolved'    ? 'selected' : ''); ?>>Resolved</option>
                                            <option value="closed"      <?php echo e($t->status=='closed'      ? 'selected' : ''); ?>>Closed</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        <svg class="h-6 w-6 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Zm1 3.75A.75.75 0 0 1 7.25 6h5.5a.75.75 0 0 1 0 1.5h-5.5a.75.75 0 0 1-.75-.75Zm0 3.25A.75.75 0 0 1 7.25 9.25h5.5a.75.75 0 0 1 0 1.5h-5.5A.75.75 0 0 1 6.5 10Zm.75 2.5h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1 0-1.5Z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun ticket trouvé</p>
                                    <p class="text-xs text-slate-400">Essayez de modifier les filtres</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if($tickets->hasPages()): ?>
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Affichage <?php echo e($tickets->firstItem()); ?>–<?php echo e($tickets->lastItem()); ?> sur <?php echo e($tickets->total()); ?></span>
                    <div class="flex items-center gap-1">
                        <?php if($tickets->onFirstPage()): ?>
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo e($tickets->previousPageUrl()); ?>"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </a>
                        <?php endif; ?>

                        <?php $__currentLoopData = $tickets->getUrlRange(max(1, $tickets->currentPage()-2), min($tickets->lastPage(), $tickets->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($url); ?>"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-xs font-medium transition
                                      <?php echo e($page == $tickets->currentPage()
                                          ? 'border-blue-600 bg-blue-600 text-white'
                                          : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300'); ?>">
                                <?php echo e($page); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($tickets->hasMorePages()): ?>
                            <a href="<?php echo e($tickets->nextPageUrl()); ?>"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/tickets/index.blade.php ENDPATH**/ ?>
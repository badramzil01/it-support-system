<?php $__env->startSection('title', 'Base de connaissance'); ?>
<?php use Illuminate\Support\Str; ?>
<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">Base de connaissance</p>
        <h2 class="brand-font text-2xl font-bold text-slate-900 dark:text-white">Gestion des solutions</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Articles, FAQ et bonnes pratiques pour l'équipe support.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <button onclick="openKbModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-500/30 transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
            Nouvelle solution
        </button>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none"><?php echo e($stats['total']); ?></p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Total solutions</p>
    </div>

    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none"><?php echo e($stats['active']); ?></p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Solutions actives</p>
    </div>

    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-900/30">
                <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4a7 7 0 0 1 11.05 3.05.75.75 0 0 1-1.299.79A5.5 5.5 0 0 0 6.5 4H8a.75.75 0 0 1 0 1.5H4.25a.75.75 0 0 1-.75-.75V1A.75.75 0 0 1 4.25 0h1.5ZM2 10a8 8 0 0 1 12.65-6.49.75.75 0 0 1-.9 1.2A6.5 6.5 0 0 0 3.5 10a.75.75 0 0 1-1.5 0Z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none"><?php echo e($stats['this_month']); ?></p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Créées ce mois</p>
    </div>

    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none"><?php echo e($stats['authors']); ?></p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Auteurs</p>
    </div>
</div>


<?php if($stats['top_contributor']): ?>
<div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-violet-600 rounded-2xl p-5 mb-6 text-white shadow-lg shadow-blue-500/20">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 ring-2 ring-white/30 text-lg font-bold">
            <?php echo e(Str::upper(Str::substr($stats['top_contributor']->name, 0, 1))); ?>

        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Top contributeur</p>
            <p class="brand-font text-lg font-bold truncate"><?php echo e($stats['top_contributor']->name); ?></p>
            <p class="text-xs opacity-80 truncate"><?php echo e($stats['top_contributor']->email); ?></p>
        </div>
        <a href="<?php echo e(route('support.ui.knowledge.author', $stats['top_contributor']->id)); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-sm font-semibold transition">
            Voir profil
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
        </a>
    </div>
</div>
<?php endif; ?>


<div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 mb-4">
    <form method="GET" action="<?php echo e(route('support.ui.knowledge.index')); ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            <div class="sm:col-span-2">
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Recherche</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Question, réponse, auteur, email…" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 pl-9 pr-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
                </div>
            </div>
            <div>
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Catégorie</label>
                <select name="category" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                    <option value="">Toutes</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>" <?php echo e(request('category') == $c ? 'selected' : ''); ?>><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Statut</label>
                <select name="status" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                    <option value="">Tous</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e(request('status') == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Auteur</label>
                <select name="author_id" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
                    <option value="">Tous</option>
                    <?php $__currentLoopData = $authors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>" <?php echo e(request('author_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Depuis</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
            </div>
            <div>
                <label class="block mb-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">Jusqu'au</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950">
            </div>
            <div class="sm:col-span-2 lg:col-span-4 xl:col-span-6 flex flex-wrap items-center justify-end gap-2">
                <a href="<?php echo e(route('support.ui.knowledge.index')); ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">Réinitialiser</a>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 active:scale-95 transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
                    Filtrer
                </button>
            </div>
        </div>
    </form>
</div>


<div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 dark:border-slate-800">
        <p class="text-sm font-semibold">
            <?php echo e($items->total()); ?> solution<?php echo e($items->total() > 1 ? 's' : ''); ?>

            <?php if(request()->hasAny(['search','category','status','author_id','date_from','date_to'])): ?>
                <span class="ml-2 rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300">Filtrés</span>
            <?php endif; ?>
        </p>
        <p class="text-xs text-slate-400">Page <?php echo e($items->currentPage()); ?> / <?php echo e($items->lastPage()); ?></p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 whitespace-nowrap">ID</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Question / Solution</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Catégorie</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Auteur</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Statut</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Confiance</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Dates</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $statusBg = match($item->status) {
                        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'draft' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        'archived' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                        default => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                    };
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">
                    <td class="px-4 py-3 whitespace-nowrap"><span class="text-xs font-mono text-slate-400">#<?php echo e($item->id); ?></span></td>
                    <td class="px-4 py-3 max-w-md">
                        <p class="font-medium text-slate-900 dark:text-slate-100 line-clamp-1"><?php echo e($item->problem_keywords); ?></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5"><?php echo e(Str::limit(strip_tags($item->solution), 80)); ?></p>
                        <?php if($item->tags && count($item->tags) > 0): ?>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            <?php $__currentLoopData = array_slice($item->tags, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center rounded bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[9px] font-medium text-slate-600 dark:text-slate-300">#<?php echo e($tag); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($item->tags) > 3): ?><span class="text-[9px] text-slate-400">+<?php echo e(count($item->tags) - 3); ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <?php if($item->category): ?><span class="text-xs text-slate-600 dark:text-slate-300"><?php echo e($item->category); ?></span><?php else: ?><span class="text-xs text-slate-400">—</span><?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if($item->author): ?>
                        <a href="<?php echo e(route('support.ui.knowledge.author', $item->author->id)); ?>" class="flex items-center gap-2 hover:underline">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-violet-600 text-[10px] font-bold text-white"><?php echo e(Str::upper(Str::substr($item->author->name, 0, 1))); ?></div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate"><?php echo e($item->author->name); ?></p>
                                <p class="text-[10px] text-slate-400 truncate"><?php echo e($item->author->email); ?></p>
                            </div>
                        </a>
                        <?php else: ?>
                        <span class="text-xs text-slate-400">Anonyme</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap"><span class="badge <?php echo e($statusBg); ?>"><?php echo e($item->status_label); ?></span></td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <?php if($item->confidence !== null): ?>
                            <?php $pct = round($item->confidence); ?>
                            <div class="flex items-center gap-2">
                                <div class="w-12 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full <?php echo e($pct >= 80 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-red-500')); ?>" style="width: <?php echo e($pct); ?>%"></div>
                                </div>
                                <span class="text-[10px] font-mono text-slate-500"><?php echo e($pct); ?>%</span>
                            </div>
                        <?php else: ?>
                            <span class="text-[10px] text-slate-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-[11px] text-slate-500 dark:text-slate-400">
                        <p>Créé <?php echo e($item->created_at->format('d/m/Y')); ?></p>
                        <p class="text-[10px] text-slate-400">Maj <?php echo e($item->updated_at->diffForHumans()); ?></p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="<?php echo e(route('support.ui.knowledge.show', $item)); ?>" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-2.5 py-1.5 text-[11px] font-medium text-white hover:bg-blue-700 transition" title="Voir">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41Z" clip-rule="evenodd"/></svg>
                            </a>
                            <?php if($item->author_id === auth()->id()): ?>
                            <button onclick='openKbModal(<?php echo json_encode($item, 15, 512) ?>)' class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition" title="Modifier">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="m2.695 14.762-1.97 1.97a.75.75 0 0 0 1.06 1.06l1.97-1.97a4.5 4.5 0 0 0 5.61-5.61l1.97-1.97a.75.75 0 0 0-1.06-1.06l-1.97 1.97a4.5 4.5 0 0 0-5.61 5.61Z"/><path d="M12.94 7.06 16 4l-1.06-1.06-3.06 3.06 1.06 1.06Z"/></svg>
                            </button>
                            <form method="POST" action="<?php echo e(route('support.ui.knowledge.destroy', $item)); ?>" onsubmit="return confirm('Confirmer la suppression de cette solution ?')" class="inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 dark:border-red-900 px-2.5 py-1.5 text-[11px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 transition" title="Supprimer">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                <svg class="h-6 w-6 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune solution trouvée</p>
                            <p class="text-xs text-slate-400">Commencez par en ajouter une nouvelle</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($items->hasPages()): ?>
    <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span>Affichage <?php echo e($items->firstItem()); ?>–<?php echo e($items->lastItem()); ?> sur <?php echo e($items->total()); ?></span>
            <div class="flex items-center gap-1">
                <?php if($items->onFirstPage()): ?>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg></span>
                <?php else: ?>
                    <a href="<?php echo e($items->previousPageUrl()); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg></a>
                <?php endif; ?>
                <?php $__currentLoopData = $items->getUrlRange(max(1,$items->currentPage()-2), min($items->lastPage(),$items->currentPage()+2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($url); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-xs font-medium transition <?php echo e($page == $items->currentPage() ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300'); ?>"><?php echo e($page); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($items->hasMorePages()): ?>
                    <a href="<?php echo e($items->nextPageUrl()); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg></a>
                <?php else: ?>
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-600"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php echo $__env->make('support.knowledge.partials.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openKbModal(item = null) {
    const modal = document.getElementById('kbModal');
    const form  = document.getElementById('kbForm');
    const title = document.getElementById('kbModalTitle');
    const method = document.getElementById('kbFormMethod');
    const tagsField = document.getElementById('kbTags');
    const statusField = document.getElementById('kbStatus');
    const confidenceField = document.getElementById('kbConfidence');
    const sourceField = document.getElementById('kbSource');
    const authorField = document.getElementById('kbAuthorId');

    if (item) {
        title.textContent = 'Modifier la solution #' + item.id;
        form.action = '<?php echo e(url("equipeIT/ui/knowledge")); ?>/' + item.id;
        method.value = 'PUT';
        document.getElementById('kbProblemKeywords').value = item.problem_keywords || '';
        document.getElementById('kbSolution').value = item.solution || '';
        document.getElementById('kbCategory').value = item.category || '';
        tagsField.value = Array.isArray(item.tags) ? item.tags.join(', ') : (item.tags || '');
        statusField.value = item.status || 'active';
        confidenceField.value = item.confidence ?? '';
        sourceField.value = item.source || 'DB';
        authorField.value = item.author_id || '';
    } else {
        title.textContent = 'Nouvelle solution';
        form.action = '<?php echo e(route("support.ui.knowledge.store")); ?>';
        method.value = 'POST';
        form.reset();
        statusField.value = 'active';
        sourceField.value = 'DB';
        authorField.value = '';
    }
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeKbModal() {
    document.getElementById('kbModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeKbModal(); });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/knowledge/index.blade.php ENDPATH**/ ?>
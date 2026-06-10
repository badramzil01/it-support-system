<?php $__env->startSection('title', 'Dashboard'); ?>
<?php use Illuminate\Support\Str; ?>
<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5"><?php echo e(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')); ?></p>
        <h2 class="brand-font text-2xl font-bold text-slate-900 dark:text-white">Bonjour, <?php echo e(auth()->user()->name ?? 'Admin'); ?> 👋</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Voici la vue d'ensemble de la plateforme Support IT.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo e(route('admin.ui.tickets.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-sm shadow-amber-500/30 transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z" clip-rule="evenodd"/></svg>
            Voir les tickets
        </a>
        <a href="<?php echo e(route('admin.ui.users.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold transition">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
            Utilisateurs
        </a>
    </div>
</div>

<?php
$kpis = [
    ['title' => 'Utilisateurs', 'value' => $totalUsers, 'icon' => 'M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z', 'bg' => 'bg-blue-50 dark:bg-blue-900/30', 'color' => 'text-blue-600 dark:text-blue-400', 'progress' => 100, 'pc' => 'bg-blue-500', 'badge' => null, 'bc' => null],
    ['title' => 'Total tickets', 'value' => $totalTickets, 'icon' => 'M5.5 3A2.5 2.5 0 0 0 3 5.5v9A2.5 2.5 0 0 0 5.5 17h9a2.5 2.5 0 0 0 2.5-2.5v-9A2.5 2.5 0 0 0 14.5 3h-9Z', 'bg' => 'bg-violet-50 dark:bg-violet-900/30', 'color' => 'text-violet-600 dark:text-violet-400', 'progress' => 100, 'pc' => 'bg-violet-500', 'badge' => null, 'bc' => null],
    ['title' => 'Tickets ouverts', 'value' => $openTickets, 'icon' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z', 'bg' => 'bg-amber-50 dark:bg-amber-900/30', 'color' => 'text-amber-600 dark:text-amber-400', 'progress' => $totalTickets > 0 ? ($openTickets / $totalTickets) * 100 : 0, 'pc' => 'bg-amber-500', 'badge' => 'En attente', 'bc' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30'],
    ['title' => 'Tickets escaladés', 'value' => $escalated, 'icon' => 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495Z', 'bg' => 'bg-red-50 dark:bg-red-900/30', 'color' => 'text-red-600 dark:text-red-400', 'progress' => $totalTickets > 0 ? ($escalated / $totalTickets) * 100 : 0, 'pc' => 'bg-red-500', 'badge' => 'Critique', 'bc' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30'],
    ['title' => 'Tickets urgents', 'value' => $urgent, 'icon' => 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495ZM10 5.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm0 8.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z', 'bg' => 'bg-orange-50 dark:bg-orange-900/30', 'color' => 'text-orange-600 dark:text-orange-400', 'progress' => $totalTickets > 0 ? ($urgent / $totalTickets) * 100 : 0, 'pc' => 'bg-orange-500', 'badge' => 'Urgent', 'bc' => 'text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/30'],
    ['title' => 'Conversations', 'value' => $conversations, 'icon' => 'M3.43 2.524A41.29 41.29 0 0 1 10 2c2.236 0 4.43.18 6.57.524 1.437.231 2.43 1.49 2.43 2.902v5.148c0 1.413-.993 2.67-2.43 2.902a41.202 41.202 0 0 1-5.183.501.78.78 0 0 0-.528.224l-3.579 3.58A.75.75 0 0 1 6 17.25v-3.443a41.033 41.033 0 0 1-2.57-.257C1.993 13.322 1 12.065 1 10.652V5.426c0-1.413.993-2.67 2.43-2.902Z', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'color' => 'text-emerald-600 dark:text-emerald-400', 'progress' => 100, 'pc' => 'bg-emerald-500', 'badge' => null, 'bc' => null],
    ['title' => 'Résolutions IA', 'value' => $aiResolutions, 'icon' => 'M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.535 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.636-.544.298-1.584-.535-1.65l-4.752-.382-1.831-4.401Z', 'bg' => 'bg-pink-50 dark:bg-pink-900/30', 'color' => 'text-pink-600 dark:text-pink-400', 'progress' => 100, 'pc' => 'bg-pink-500', 'badge' => null, 'bc' => null],
    ['title' => 'Satisfaction', 'value' => $satisfaction.'%', 'icon' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'color' => 'text-emerald-600 dark:text-emerald-400', 'progress' => $satisfaction, 'pc' => 'bg-emerald-500', 'badge' => 'Clients', 'bc' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30'],
];
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
    <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
        <div class="flex items-start justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl <?php echo e($k['bg']); ?>">
                <svg class="h-5 w-5 <?php echo e($k['color']); ?>" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="<?php echo e($k['icon']); ?>" clip-rule="evenodd"/></svg>
            </div>
            <?php if($k['badge']): ?>
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full <?php echo e($k['bc']); ?>"><?php echo e($k['badge']); ?></span>
            <?php endif; ?>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none"><?php echo e($k['value']); ?></p>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5"><?php echo e($k['title']); ?></p>
        <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full <?php echo e($k['pc']); ?> rounded-full" style="width: <?php echo e(min(100, max(0, $k['progress']))); ?>%"></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-5 mb-6">
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Tickets par jour</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">7 derniers jours</p>
            </div>
            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Total : <?php echo e(array_sum($ticketsPerDayData)); ?></span>
        </div>
        <div class="h-64"><canvas id="chartTicketsPerDay"></canvas></div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
        <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white mb-4">Priorités</h3>
        <div class="h-64"><canvas id="chartPriorities"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-5 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
        <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white mb-4">Catégories</h3>
        <div class="h-64"><canvas id="chartCategories"></canvas></div>
    </div>
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Derniers tickets</h3>
            <a href="<?php echo e(route('admin.ui.tickets.index')); ?>" class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline">Voir tout →</a>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            <?php $__empty_1 = true; $__currentLoopData = $latestTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                <span class="h-2 w-2 shrink-0 rounded-full <?php echo e(match($t->priority) { 'critical'=>'bg-red-500', 'high'=>'bg-orange-500', 'medium'=>'bg-amber-400', default=>'bg-slate-300' }); ?> dark:<?php echo e(match($t->priority) { 'critical'=>'bg-red-400', 'high'=>'bg-orange-400', 'medium'=>'bg-amber-300', default=>'bg-slate-600' }); ?>"></span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs font-mono font-medium text-slate-400 shrink-0">#<?php echo e($t->id); ?></span>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate"><?php echo e(Str::limit($t->title, 50)); ?></p>
                    </div>
                    <div class="flex items-center gap-3 mt-0.5">
                        <span class="text-[11px] text-slate-400"><?php echo e(optional($t->user)->name ?? '—'); ?></span>
                        <span class="text-[11px] text-slate-300 dark:text-slate-600">·</span>
                        <span class="text-[11px] text-slate-400"><?php echo e($t->created_at->diffForHumans()); ?></span>
                    </div>
                </div>
                <span class="badge shrink-0 <?php echo e(match($t->status) { 'resolved'=>'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'in_progress'=>'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400', 'closed'=>'bg-slate-50 text-slate-500 dark:bg-slate-800 dark:text-slate-400', default=>'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }); ?>"><?php echo e(ucfirst(str_replace('_',' ',$t->status))); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-10 text-center text-sm text-slate-400">Aucun ticket pour le moment.</div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(100,116,139,0.2)' : 'rgba(100,116,139,0.15)';
const textColor = isDark ? '#cbd5e1' : '#475569';

const ticketsPerDayLabels = <?php echo json_encode($ticketsPerDayLabels, 15, 512) ?>;
const ticketsPerDayData = <?php echo json_encode($ticketsPerDayData, 15, 512) ?>;
const priorityLabels = <?php echo json_encode($priorityLabels, 15, 512) ?>;
const priorityData = <?php echo json_encode($priorityData, 15, 512) ?>;
const categoryLabels = <?php echo json_encode($categoryLabels, 15, 512) ?>;
const categoryData = <?php echo json_encode($categoryData, 15, 512) ?>;

new Chart(document.getElementById('chartTicketsPerDay'), {
    type: 'line',
    data: {
        labels: ticketsPerDayLabels,
        datasets: [{
            label: 'Tickets',
            data: ticketsPerDayData,
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245,158,11,0.12)',
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#F59E0B',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }, x: { grid: { display: false }, ticks: { color: textColor } } }
    }
});

new Chart(document.getElementById('chartPriorities'), {
    type: 'doughnut',
    data: {
        labels: priorityLabels,
        datasets: [{
            data: priorityData,
            backgroundColor: ['#EF4444', '#F97316', '#F59E0B', '#94A3B8', '#10B981'],
            borderWidth: 0,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 } } } }, cutout: '65%' }
});

new Chart(document.getElementById('chartCategories'), {
    type: 'bar',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryData,
            backgroundColor: '#F59E0B',
            borderRadius: 6,
            maxBarThickness: 28,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }, y: { grid: { display: false }, ticks: { color: textColor } } }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
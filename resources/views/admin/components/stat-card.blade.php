@props([
    'title',
    'value',
    'icon' => null,
    'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
    'iconColor' => 'text-blue-600 dark:text-blue-400',
    'progress' => null,
    'progressColor' => 'bg-blue-500',
    'badge' => null,
    'badgeColor' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30',
])

<div {{ $attributes->merge(['class' => 'stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between mb-3">
        @if($icon)
        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $iconBg }}">
            <svg class="h-5 w-5 {{ $iconColor }}" viewBox="0 0 20 20" fill="currentColor">{!! $icon !!}</svg>
        </div>
        @endif
        @if($badge)
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeColor }}">{{ $badge }}</span>
        @endif
    </div>
    <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">{{ $value }}</p>
    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">{{ $title }}</p>
    @if($progress !== null)
    <div class="mt-3 h-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
        <div class="h-full {{ $progressColor }} rounded-full" style="width: {{ min(100, max(0, $progress)) }}%"></div>
    </div>
    @endif
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $title }}</div>
            <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $value }}</div>
        </div>
        <div class="text-gray-400">@isset($icon) {!! $icon !!} @endisset</div>
    </div>
</div>

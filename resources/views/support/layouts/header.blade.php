<header class="bg-white border-b p-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <button class="md:hidden p-2 bg-gray-100 rounded">☰</button>
        <h2 class="text-lg font-semibold">@yield('title', 'Support')</h2>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-600">{{ auth()->user()?->name ?? 'Guest' }}</div>
    </div>
</header>

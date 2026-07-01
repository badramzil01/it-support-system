<!DOCTYPE html>
<html lang="fr" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Support IT') }} - Espace Client</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Notification badge pulse */
        .notif-pulse::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 10px;
            height: 10px;
            background: #ef4444;
            border: 2px solid white;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .dark .notif-pulse::after { border-color: #1e293b; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        /* Mobile menu */
        .mobile-menu-open { transform: translateX(0) !important; }
        .mobile-overlay { display: none; }
        .mobile-overlay.active { display: block; }

        /* Dropdown animations */
        .dropdown-enter { opacity: 0; transform: translateY(-8px) scale(0.96); }
        .dropdown-enter-active { opacity: 1; transform: translateY(0) scale(1); transition: all 0.15s ease-out; }

        /* Stat card hover */
        .stat-card { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.1); }
        .dark .stat-card:hover { box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.3); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <div class="flex h-screen overflow-hidden">
        {{-- Mobile Overlay --}}
        <div class="mobile-overlay fixed inset-0 bg-black/40 z-20 lg:hidden" id="mobileOverlay" onclick="toggleSidebar()"></div>

        {{-- SIDEBAR --}}
        @include('client.sidebar')

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
            {{-- HEADER --}}
            @include('client.header')

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-menu-open');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }

        // Close sidebar on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('sidebar').classList.remove('mobile-menu-open');
                document.getElementById('mobileOverlay').classList.remove('active');
            }
        });

        // Notification dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const notifBtn = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');

            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                    if (userDropdown) userDropdown.classList.add('hidden');
                });
            }

            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    if (notifDropdown) notifDropdown.classList.add('hidden');
                });
            }

            // Close dropdowns on outside click
            document.addEventListener('click', function() {
                if (notifDropdown) notifDropdown.classList.add('hidden');
                if (userDropdown) userDropdown.classList.add('hidden');
            });
        });

        // Dark mode toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            fetch('/client/toggle-dark', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        }
    </script>
    @stack('scripts')
</body>
</html>
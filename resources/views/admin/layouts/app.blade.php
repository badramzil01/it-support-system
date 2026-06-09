<!doctype html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin · Support IT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --sidebar-w:256px; --header-h:56px; --accent:#F59E0B; --accent-light:#FEF3C7; --accent-dark:#D97706; }
        * { font-family: 'Inter', system-ui, sans-serif; }
        .brand-font { font-family: 'Plus Jakarta Sans', sans-serif; }
        #sidebar { width: var(--sidebar-w); }
        .main-wrap { margin-left: var(--sidebar-w); }
        @media (max-width: 768px) { .main-wrap { margin-left: 0; } }
        .nav-item { position: relative; transition: all 0.15s ease; }
        .nav-item::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; border-radius:0 4px 4px 0; background: var(--accent); transform: scaleY(0); transition: transform 0.15s ease; }
        .nav-item.active::before { transform: scaleY(1); }
        .nav-item.active { background: rgba(245,158,11,0.12); color:#fff; }
        .nav-item:not(.active):hover { background: rgba(255,255,255,0.06); }
        .search-bar { transition: all 0.2s ease; }
        .search-bar:focus-within { box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
        .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); }
        .data-row { transition: background 0.1s ease; }
        .badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; }
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100,116,139,0.3); border-radius:99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100,116,139,0.5); }
        .dropdown-menu { opacity:0; transform: translateY(6px) scale(0.97); pointer-events:none; transition: all 0.15s ease; }
        .dropdown-menu.open { opacity:1; transform: translateY(0) scale(1); pointer-events:all; }
        #cmdPalette { transition: all 0.2s ease; }
        #cmdOverlay { transition: opacity 0.2s ease; }
        #sidebar { transition: transform 0.2s ease, width 0.2s ease; }
        @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:.4;} }
        .pulse { animation: pulse-dot 2s ease-in-out infinite; }
        html.dark { color-scheme: dark; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0D0F14] text-slate-900 dark:text-slate-100 min-h-full antialiased">
@include('admin.layouts.partials.cmd-palette')
<div class="flex min-h-screen">
@include('admin.layouts.partials.sidebar')
<div id="sidebarOverlay" class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm hidden md:hidden" onclick="toggleSidebar()"></div>
<div class="main-wrap flex flex-1 flex-col min-w-0">
@include('admin.layouts.partials.header')
<main class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 dark:bg-[#0D0F14]">
@yield('content')
</main>
</div>
</div>
@stack('scripts')
@include('admin.layouts.partials.scripts')
</body>
</html>

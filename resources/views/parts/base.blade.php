<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI IT SUPPORT |  Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="flex h-screen">
    @include('parts.sidebar')
    <!-- MAIN -->
    <main class="flex-1 overflow-y-auto">
        <!-- TOPBAR -->
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div><h2 class="text-2xl font-bold text-gray-800">Dashboard Administrateur</h2></div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500">{{ strtoupper(auth()->user()->roles->first()->name) }} </p>
                </div>
            </div>

        </header>

        <!-- CONTENT -->
        <div class="p-8">
            @yield('content')

        </div>

    </main>

</div>

</body>
</html>
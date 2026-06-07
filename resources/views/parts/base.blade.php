<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI IT SUPPORT</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-hidden">

<div class="flex h-screen">

    {{-- SIDEBAR --}}
    @include('parts.sidebar')

    {{-- CONTENU PRINCIPAL --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">

                    @if(request()->is('admin/*'))
                        Dashboard Administrateur

                    @elseif(request()->is('equipeIT/*'))
                        Dashboard Support

                    @else
                        Dashboard
                    @endif

                </h2>

            </div>

            <div class="flex items-center gap-4">

                <div class="text-right">

                    <p class="font-semibold">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-sm text-gray-500">

                        @if(request()->is('admin/*'))
                            ADMIN

                        @elseif(request()->is('equipeIT/*'))
                            SUPPORT

                        @else
                            {{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'USER') }}

                        @endif

                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">

                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}

                </div>

            </div>

        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8">

            @yield('content')

        </main>

    </div>

</div>

</body>

</html>
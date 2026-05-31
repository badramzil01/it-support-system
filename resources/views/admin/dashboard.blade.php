@extends('parts.base')
@section('content')
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Tickets</p>
            <h2 class="text-3xl font-bold text-gray-800">
                {{ $totalTickets }}
            </h2>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Tickets Ouverts</p>
            <h2 class="text-3xl font-bold text-yellow-600">
                {{ $openTickets }}
            </h2>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Tickets Résolus</p>
            <h2 class="text-3xl font-bold text-green-600">
                {{ $resolvedTickets }}
            </h2>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Tickets Escaladés</p>
            <h2 class="text-3xl font-bold text-red-600">
                {{ $escalatedTickets }}
            </h2>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm">Tickets Urgents</p>
            <h2 class="text-3xl font-bold text-orange-600">
                {{ $urgentTickets }}
            </h2>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border-l-4 border-purple-500">
            <p class="text-gray-500 text-sm">Confiance Moyenne IA</p>
            <h2 class="text-3xl font-bold text-purple-600">
                {{ number_format($avgConfidence, 2) }}%
            </h2>
        </div>

    </div>

    <!-- Répartition par catégorie -->
    <div class="bg-white shadow rounded-xl p-6">

        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Répartition par Catégorie
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Catégorie</th>
                        <th class="px-4 py-3 text-center">Nombre</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($byCategory as $category)
                        <tr class="border-t">
                            <td class="px-4 py-3">
                                {{ $category->category ?? 'Non définie' }}
                            </td>

                            <td class="px-4 py-3 text-center font-semibold">
                                {{ $category->total }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2"
                                class="px-4 py-4 text-center text-gray-500">
                                Aucune donnée disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>    
@endsection
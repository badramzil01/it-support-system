@extends('parts.base')
@section('content')
        <!-- CONTENT -->
        <div class="p-8">
            <div class="grid grid-cols-3 gap-4 mt-4">

                <div class="p-4 bg-blue-100 rounded">
                    Open Tickets: {{ $tickets_open }}
                </div>

                <div class="p-4 bg-red-100 rounded">
                    Escalated: {{ $tickets_escalated }}
                </div>

                <div class="p-4 bg-green-100 rounded">
                    My Tickets: {{ $my_tickets }}
                </div>

            </div>
            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-2xl shadow">
                    <h3 class="text-gray-500">
                        Tickets Assignés
                    </h3>

                    <p class="text-4xl font-bold mt-3 text-indigo-600">
                        24
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow">
                    <h3 class="text-gray-500">
                        Tickets Résolus
                    </h3>

                    <p class="text-4xl font-bold mt-3 text-green-500">
                        180
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow">
                    <h3 class="text-gray-500">
                        En Attente
                    </h3>

                    <p class="text-4xl font-bold mt-3 text-orange-500">
                        8
                    </p>
                </div>

            </div>

            <!-- TICKETS -->
            <div class="bg-white rounded-2xl shadow mt-8">

                <div class="p-6 border-b flex justify-between items-center">

                    <h3 class="text-xl font-bold">
                        Tickets Récents
                    </h3>

                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        Nouveau Ticket
                    </button>

                </div>

                <div class="divide-y">

                    <!-- ITEM -->
                    <div class="p-6 flex justify-between items-center">

                        <div>

                            <h4 class="font-bold text-lg">
                                Erreur connexion VPN
                            </h4>

                            <p class="text-gray-500 mt-1">
                                Ahmed • il y a 10 min
                            </p>

                        </div>

                        <div class="flex items-center gap-4">

                            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm">
                                Pending
                            </span>

                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
                                Ouvrir
                            </button>

                        </div>

                    </div>

                    <!-- ITEM -->
                    <div class="p-6 flex justify-between items-center">

                        <div>

                            <h4 class="font-bold text-lg">
                                Réinitialisation mot de passe
                            </h4>

                            <p class="text-gray-500 mt-1">
                                Sara • il y a 30 min
                            </p>

                        </div>

                        <div class="flex items-center gap-4">

                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                                Résolu
                            </span>

                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
                                Voir
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

@endsection
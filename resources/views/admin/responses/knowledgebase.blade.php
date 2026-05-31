@extends('parts.base')
@section('content')
        <!-- CONTENT -->
        <div class="p-8">
            <div class="bg-white p-6 shadow rounded mb-6">
                <h2 class="text-xl font-semibold mb-4">Formulaire</h2>
                @if($kb)
                <form method="POST" action="{{ route('admin.base.update', $kb->id) }}">
                    @csrf
                    @method('PUT')
                @else
                <form method="POST" action="{{ route('admin.base.store') }}">
                    @csrf
                @endif
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom</label>
                        <input type="text" value="{{ old('problem_keywords', $kb->problem_keywords ?? '') }}" name="problem_keywords" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keywords">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="text" value="{{ old('solution', $kb->solution ?? '') }}" name="solution" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="solution">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ $kb ? 'Mettre à jour' : 'Enregistrer' }}</button>
                </form>
            </div>

            <!-- Colonne DataTable -->
            <div class="bg-white p-6 shadow rounded">
                <h2 class="text-xl font-semibold mb-4">Knowledge Base</h2>

                <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2">ID</th>
                            <th class="border p-2">Keywords</th>
                            <th class="border p-2">Solution</th>
                            <th class="border p-2">Source</th>
                            <th class="border p-2">Usage</th>
                            <th class="border p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kbs as $kb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border">{{ $kb->id }}</td>
                                <td class="px-4 py-3 border">{{ $kb->problem_keywords }}</td>
                                <td class="px-4 py-3 border">{{ $kb->solution }}</td>
                                <td class="px-4 py-3 border">{{ $kb->source }}</td>
                                <td class="px-4 py-3 border">{{ $kb->usage }}</td>                                
                                <td class="px-4 py-3 border">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.base.index', ['edit' => $kb->id]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Modifier</a>
                                        <form method="POST"
                                            action="{{route('admin.base.delete', $kb->id) }}"
                                            onsubmit="return confirm('Supprimer cette base ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
@endsection
@extends('parts.base')
@section('content')
<!-- CONTENT -->
<div class="p-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-800">
                Logs système
            </h2>
            <a href="{{ route('admin.logs.download') }}"
            class="px-4 py-2 text-sm border rounded-lg hover:bg-slate-100 inline-block">
                Télécharger les logs
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-500">
                            Date
                        </th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-500">
                                Niveau
                        </th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-500">
                                Message
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
@forelse($logs as $log)
                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                {{ $log->user?->name ?? 'Système' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-700 text-xs font-medium">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $log->model ?? '-' }}

                                @if($log->model_id)
                                    #{{ $log->model_id }}
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $log->description ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $log->ip_address }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Aucun log trouvé.
                            </td>
                        </tr>
                    @endforelse

                        

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
@extends('support.layouts.app')
@section('title', 'Solution #'.$item->id)
@php use Illuminate\Support\Str; @endphp
@section('content')

@php
    $statusBg = match($item->status) {
        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'draft' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'archived' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
        default => 'bg-slate-100 text-slate-500',
    };
@endphp

<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('support.ui.knowledge.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">← Retour à la base</a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-mono text-slate-400 mb-1">#{{ $item->id }}</p>
                        <h1 class="brand-font text-2xl font-bold text-slate-900 dark:text-white leading-tight">{{ $item->problem_keywords }}</h1>
                    </div>
                    <span class="badge {{ $statusBg }} shrink-0">{{ $item->status_label }}</span>
                </div>
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-[10px] uppercase text-slate-500 mb-2">Solution</p>
                    <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $item->solution }}</p>
                </div>
                @if($item->tags && count($item->tags) > 0)
                <div class="mt-4">
                    <p class="text-[10px] uppercase text-slate-500 mb-2">Tags</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($item->tags as $tag)
                        <span class="inline-flex items-center rounded bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-xs text-slate-600 dark:text-slate-300">#{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @if($related->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Articles liés</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($related as $r)
                    <a href="{{ route('support.ui.knowledge.show', $r) }}" class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 shrink-0">
                            <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{{ $r->problem_keywords }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ Str::limit(strip_tags($r->solution), 90) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Auteur --}}
            @if($item->author)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-3">Auteur</p>
                <a href="{{ route('support.ui.knowledge.author', $item->author->id) }}" class="flex items-center gap-3 group">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-violet-600 text-base font-bold text-white ring-2 ring-blue-200 dark:ring-blue-900">
                        {{ Str::upper(Str::substr($item->author->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition truncate">{{ $item->author->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $item->author->email }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Support</p>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-600 transition" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
                </a>
            </div>
            @endif

            {{-- Métadonnées --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-3">Métadonnées</p>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between"><dt class="text-slate-500">Catégorie</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $item->category ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Source</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ strtoupper($item->source ?? '—') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Utilisations</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $item->usage_count }}</dd></div>
                    @if($item->confidence !== null)
                    <div class="flex justify-between items-center"><dt class="text-slate-500">Confiance IA</dt>
                        <dd class="flex items-center gap-2">
                            <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $item->confidence >= 80 ? 'bg-emerald-500' : ($item->confidence >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ round($item->confidence) }}%"></div>
                            </div>
                            <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300">{{ round($item->confidence) }}%</span>
                        </dd>
                    </div>
                    @endif
                    <div class="flex justify-between"><dt class="text-slate-500">Créé le</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $item->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Modifié le</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $item->updated_at->format('d/m/Y H:i') }}</dd></div>
                    @if($item->lastEditor)
                    <div class="flex justify-between"><dt class="text-slate-500">Modifié par</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $item->lastEditor->name }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            @if($item->author_id === auth()->id())
            <div class="flex flex-wrap gap-2">
                <button onclick="openKbModal(@json($item))" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="m2.695 14.762-1.97 1.97a.75.75 0 0 0 1.06 1.06l1.97-1.97a4.5 4.5 0 0 0 5.61-5.61l1.97-1.97a.75.75 0 0 0-1.06-1.06l-1.97 1.97a4.5 4.5 0 0 0-5.61 5.61Z"/></svg>
                    Modifier
                </button>
                <form method="POST" action="{{ route('support.ui.knowledge.destroy', $item) }}" onsubmit="return confirm('Supprimer ?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 dark:border-red-900 px-3 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 transition">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5Z" clip-rule="evenodd"/></svg>
                        Supprimer
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@include('support.knowledge.partials.modal')
@endsection

@push('scripts')
<script>
function openKbModal(item = null) {
    const modal = document.getElementById('kbModal');
    const form  = document.getElementById('kbForm');
    const title = document.getElementById('kbModalTitle');
    const method = document.getElementById('kbFormMethod');
    const tagsField = document.getElementById('kbTags');
    const statusField = document.getElementById('kbStatus');
    const confidenceField = document.getElementById('kbConfidence');
    const sourceField = document.getElementById('kbSource');
    const authorField = document.getElementById('kbAuthorId');

    if (item) {
        title.textContent = 'Modifier la solution #' + item.id;
        form.action = '{{ url("equipeIT/ui/knowledge") }}/' + item.id;
        method.value = 'PUT';
        document.getElementById('kbProblemKeywords').value = item.problem_keywords || '';
        document.getElementById('kbSolution').value = item.solution || '';
        document.getElementById('kbCategory').value = item.category || '';
        tagsField.value = Array.isArray(item.tags) ? item.tags.join(', ') : (item.tags || '');
        statusField.value = item.status || 'active';
        confidenceField.value = item.confidence ?? '';
        sourceField.value = item.source || 'DB';
        authorField.value = item.author_id || '';
    } else {
        title.textContent = 'Nouvelle solution';
        form.action = '{{ route("support.ui.knowledge.store") }}';
        method.value = 'POST';
        form.reset();
        statusField.value = 'active';
        sourceField.value = 'DB';
        authorField.value = '';
    }
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeKbModal() {
    document.getElementById('kbModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeKbModal(); });
</script>
@endpush

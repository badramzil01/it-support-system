@extends('admin.layouts.app')
@section('title', $user->name . ' — Auteur')
@php use Illuminate\Support\Str; @endphp
@section('content')

<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.ui.knowledge.index') }}" class="hover:text-amber-600 dark:hover:text-amber-400">← Retour à la base</a>
        <span>/</span>
        <span class="text-slate-700 dark:text-slate-200">Auteur</span>
    </div>

    {{-- HEADER PROFIL --}}
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 rounded-2xl p-6 text-white shadow-lg shadow-amber-500/20">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/30 text-3xl font-bold">
                {{ Str::upper(Str::substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Profil auteur</p>
                <h1 class="brand-font text-3xl font-bold truncate">{{ $user->name }}</h1>
                <p class="text-sm opacity-90 truncate">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20 text-xs font-semibold">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path fill-rule="evenodd" d="M10 1a4 4 0 0 0-4 4v1H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-1V5a4 4 0 0 0-4-4ZM8 5a2 2 0 1 1 4 0v1H8V5Z" clip-rule="evenodd"/></svg>
                        {{ ucfirst($role ?? 'User') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20 text-xs">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4a7 7 0 0 1 11.05 3.05.75.75 0 0 1-1.299.79A5.5 5.5 0 0 0 6.5 4H8a.75.75 0 0 1 0 1.5H4.25a.75.75 0 0 1-.75-.75V1A.75.75 0 0 1 4.25 0h1.5Z" clip-rule="evenodd"/></svg>
                        Inscrit {{ $user->created_at?->format('d/m/Y') }}
                    </span>
                    @if($lastActivity)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20 text-xs">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/></svg>
                        Active {{ $lastActivity->diffForHumans() }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">{{ $user->knowledge_base_items_count ?? 0 }}</p>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Solutions créées</p>
        </div>

        <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.061l1.224-1.225a4 4 0 0 0-5.656-5.656L8.929 5.414a.75.75 0 1 0 1.06 1.061l2.243-2.243Z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">{{ $modificationsCount }}</p>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Modifications</p>
        </div>

        <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">{{ $statusBreakdown['active'] ?? 0 }}</p>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Actives</p>
        </div>

        <div class="stat-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-900/30">
                    <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white leading-none">{{ $topCategories->count() }}</p>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1.5">Catégories</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- TOP CATÉGORIES --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Catégories les plus utilisées</h3>
            </div>
            <div class="p-4 space-y-2">
                @forelse($topCategories as $cat)
                <div class="flex items-center gap-2">
                    <div class="flex-1">
                        <div class="flex justify-between mb-1 text-xs">
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $cat->category }}</span>
                            <span class="text-slate-500">{{ $cat->total }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            @php $max = $topCategories->max('total') ?: 1; @endphp
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ ($cat->total / $max) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Aucune catégorie</p>
                @endforelse
            </div>
        </div>

        {{-- HISTORIQUE --}}
        <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="brand-font text-sm font-semibold text-slate-900 dark:text-white">Historique des solutions</h3>
                <span class="text-[10px] font-medium text-slate-500 uppercase">{{ $items->total() }} au total</span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($items as $i)
                <a href="{{ route('admin.ui.knowledge.show', $i) }}" class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition group">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804Z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">{{ $i->problem_keywords }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ Str::limit(strip_tags($i->solution), 80) }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($i->category)<span class="inline-flex items-center rounded bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[10px] text-slate-600 dark:text-slate-300">{{ $i->category }}</span>@endif
                            <span class="text-[10px] text-slate-400">{{ $i->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <span class="badge shrink-0 {{ match($i->status) { 'active'=>'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'draft'=>'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'archived'=>'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400', default=>'bg-slate-100 text-slate-500' } }}">{{ $i->status_label }}</span>
                </a>
                @empty
                <div class="p-8 text-center text-sm text-slate-400">Aucune solution créée par cet utilisateur</div>
                @endforelse
            </div>
            @if($items->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

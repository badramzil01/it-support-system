@extends('support.layouts.app')
@section('title', 'Base de connaissance')

@section('content')

{{-- Page header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Base de connaissance</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Gérez les solutions, procédures et articles IT</p>
    </div>
    <a href="{{ route('support.ui.knowledge.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-semibold shadow-sm shadow-blue-500/30 transition-all shrink-0">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
        </svg>
        Nouvel article
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    <div class="flex items-center gap-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
            <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $items->total() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Articles</p>
        </div>
    </div>
    <div class="flex items-center gap-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30">
            <svg class="h-4 w-4 text-violet-600 dark:text-violet-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v.944a1 1 0 0 1-.445.832l-3.668 2.445a1 1 0 0 0 0 1.558l3.668 2.445A1 1 0 0 1 16 11.056V16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4.944a1 1 0 0 1 .445-.832l3.668-2.445a1 1 0 0 0 0-1.558L5.445 3.776A1 1 0 0 1 5 2.944V2Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $categories->count() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Catégories</p>
        </div>
    </div>
    <div class="flex items-center gap-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30">
            <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $items->currentPage() }}/{{ $items->lastPage() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Page</p>
        </div>
    </div>
    <div class="flex items-center gap-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
            <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-bold text-slate-900 dark:text-white leading-none">{{ $items->perPage() }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Par page</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-sm mb-4 p-3">
    <form method="GET" class="flex flex-col sm:flex-row gap-2.5">
        {{-- Search --}}
        <div class="relative flex-1 min-w-0">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher un article, mot-clé, solution…"
                   class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition">
        </div>

        {{-- Category --}}
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v.944a1 1 0 0 1-.445.832l-3.668 2.445a1 1 0 0 0 0 1.558l3.668 2.445A1 1 0 0 1 16 11.056V16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4.944a1 1 0 0 1 .445-.832l3.668-2.445a1 1 0 0 0 0-1.558L5.445 3.776A1 1 0 0 1 5 2.944V2Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <select name="category"
                    class="pl-9 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 appearance-none transition">
                <option value="">Toutes catégories</option>
                @foreach($categories as $c)
                    <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-sm font-semibold hover:bg-slate-700 dark:hover:bg-slate-300 transition shrink-0">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
            Filtrer
        </button>

        @if(request('search') || request('category'))
            <a href="{{ route('support.ui.knowledge.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                </svg>
                Réinitialiser
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Article / Problème
                    </th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden md:table-cell">
                        Solution
                    </th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden lg:table-cell">
                        Catégorie
                    </th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden lg:table-cell">
                        Auteur
                    </th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden xl:table-cell">
                        Date
                    </th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($items as $i)
                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">

                    {{-- Article --}}
                    <td class="px-4 py-3.5 max-w-xs">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 mt-0.5">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900 dark:text-slate-100 truncate text-sm leading-snug">
                                    {{ \Illuminate\Support\Str::limit($i->problem_keywords, 65) }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 md:hidden truncate">
                                    {{ \Illuminate\Support\Str::limit($i->solution, 55) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Solution --}}
                    <td class="px-4 py-3.5 hidden md:table-cell max-w-sm">
                        <p class="text-sm text-slate-600 dark:text-slate-300 truncate">
                            {{ \Illuminate\Support\Str::limit($i->solution, 100) }}
                        </p>
                    </td>

                    {{-- Category --}}
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($i->category)
                            @php
                                $catColors = [
                                    'Réseau'    => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'Sécurité'  => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'Matériel'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'Logiciel'  => 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                                    'Email'     => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                ];
                                $color = $catColors[$i->category] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                            @endphp
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $color }}">
                                {{ $i->category }}
                            </span>
                        @else
                            <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                        @endif
                    </td>

                    {{-- Author --}}
                    <td class="px-4 py-3.5 hidden lg:table-cell">
                        @if($i->author)
                            <div class="flex items-center gap-2">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-violet-500 text-[9px] font-bold text-white shrink-0">
                                    {{ Str::upper(Str::substr($i->author->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate max-w-[100px]">
                                    {{ $i->author->name }}
                                </span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="px-4 py-3.5 hidden xl:table-cell whitespace-nowrap">
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $i->created_at->format('d M Y') }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('support.ui.knowledge.edit', $i) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-amber-900/30 hover:text-amber-700 dark:hover:text-amber-400 transition-colors">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/>
                                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z"/>
                                </svg>
                                Modifier
                            </a>
                            <form method="POST" action="{{ route('support.ui.knowledge.destroy', $i) }}" class="inline"
                                  onsubmit="return confirm('Supprimer cet article définitivement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-400 transition-colors">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400">
                                <svg class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Aucun article trouvé</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    @if(request('search') || request('category'))
                                        Essayez d'autres filtres ou
                                        <a href="{{ route('support.ui.knowledge.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">réinitialisez</a>.
                                    @else
                                        Commencez par créer votre premier article.
                                    @endif
                                </p>
                            </div>
                            @unless(request('search') || request('category'))
                                <a href="{{ route('support.ui.knowledge.create') }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition mt-1">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                                    </svg>
                                    Créer un article
                                </a>
                            @endunless
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Affichage de <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $items->firstItem() }}</span>
            à <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $items->lastItem() }}</span>
            sur <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $items->total() }}</span> articles
        </p>
        <div class="kb-pagination">
            {{ $items->links() }}
        </div>
    </div>
    @endif
</div>

<style>
.kb-pagination nav > div { display: flex; align-items: center; gap: 4px; }
.kb-pagination nav > div > div:first-child { display: none; }
.kb-pagination span[aria-current="page"] span,
.kb-pagination button,
.kb-pagination a {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 32px; padding: 0 8px;
    border-radius: 8px; font-size: 13px; font-weight: 500;
    border: 1px solid transparent; transition: all 0.15s ease;
}
.kb-pagination span[aria-current="page"] span {
    background: #2563eb; color: #fff; border-color: #2563eb;
}
.kb-pagination a { color: #64748b; border-color: #e2e8f0; }
.kb-pagination a:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
html.dark .kb-pagination a { color: #94a3b8; border-color: #334155; }
html.dark .kb-pagination a:hover { background: #1e293b; border-color: #475569; color: #e2e8f0; }
</style>

@endsection
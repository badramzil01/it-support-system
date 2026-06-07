@extends('support.layouts.app')
@section('title', $item ? 'Modifier l\'article' : 'Nouvel article')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

:root {
    --ink:        #0d1117;
    --ink-2:      #3b4557;
    --ink-3:      #7a8499;
    --surface:    #ffffff;
    --surface-2:  #f5f6f8;
    --surface-3:  #eef0f4;
    --border:     #e4e7ed;
    --accent:     #2563eb;
    --accent-dim: #dbeafe;
    --accent-glow:#2563eb22;
    --radius:     14px;
    --radius-sm:  8px;
    --shadow-sm:  0 1px 3px 0 rgba(0,0,0,.06), 0 1px 2px -1px rgba(0,0,0,.04);
    --shadow:     0 4px 16px -4px rgba(0,0,0,.08), 0 1px 4px -1px rgba(0,0,0,.05);
    --danger:     #dc2626;
}
html.dark {
    --ink:        #f0f2f7;
    --ink-2:      #9aa3b5;
    --ink-3:      #5c6679;
    --surface:    #111318;
    --surface-2:  #181c24;
    --surface-3:  #1f242f;
    --border:     #252b38;
    --accent-dim: #1e3a5f;
    --accent-glow:#2563eb18;
}

.kbf-page * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
.kbf-page .font-display { font-family: 'Syne', sans-serif; }

/* ── Breadcrumb ── */
.kbf-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    font-size: 13px;
    color: var(--ink-3);
}
.kbf-breadcrumb a { color: var(--ink-3); text-decoration: none; transition: color .15s; }
.kbf-breadcrumb a:hover { color: var(--accent); }
.kbf-breadcrumb span { color: var(--ink-2); font-weight: 500; }

/* ── Layout ── */
.kbf-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) { .kbf-layout { grid-template-columns: 1fr; } }

/* ── Card ── */
.kbf-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: var(--shadow);
    overflow: hidden;
}
.kbf-card-header {
    padding: 22px 24px 18px;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
    position: relative;
    overflow: hidden;
}
.kbf-card-header::after {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 130px; height: 130px;
    background: radial-gradient(circle, var(--accent-dim) 0%, transparent 70%);
    opacity: .6;
    pointer-events: none;
}
.kbf-card-header-icon {
    width: 38px; height: 38px;
    border-radius: 11px;
    background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px -2px rgba(37,99,235,.4);
}
.kbf-card-title {
    font-family: 'Syne', sans-serif;
    font-size: 17px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.3px;
}
.kbf-card-sub {
    font-size: 12.5px;
    color: var(--ink-3);
    margin-top: 3px;
}
.kbf-card-body { padding: 24px; }

/* ── Field group ── */
.kbf-field { margin-bottom: 22px; }
.kbf-field:last-child { margin-bottom: 0; }
.kbf-label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-family: 'Syne', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: var(--ink-2);
    letter-spacing: .5px;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.kbf-label-icon {
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
    background: var(--accent-dim);
    color: var(--accent);
    flex-shrink: 0;
}
.kbf-label-req {
    margin-left: auto;
    font-size: 10px;
    font-weight: 500;
    color: var(--danger);
    text-transform: none;
    letter-spacing: 0;
}

.kbf-input,
.kbf-textarea,
.kbf-select {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--ink);
    transition: border-color .15s, box-shadow .15s, background .15s;
    outline: none;
}
.kbf-input::placeholder,
.kbf-textarea::placeholder { color: var(--ink-3); }
.kbf-input:focus,
.kbf-textarea:focus,
.kbf-select:focus {
    border-color: var(--accent);
    background: var(--surface);
    box-shadow: 0 0 0 4px var(--accent-glow);
}
.kbf-textarea { resize: vertical; min-height: 160px; line-height: 1.6; }
.kbf-select { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 20 20' fill='%237a8499'%3E%3Cpath fill-rule='evenodd' d='M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z' clip-rule='evenodd'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }

/* Field hint */
.kbf-hint {
    margin-top: 6px;
    font-size: 12px;
    color: var(--ink-3);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ── Divider ── */
.kbf-divider {
    height: 1px;
    background: var(--border);
    margin: 24px 0;
}

/* ── Actions footer ── */
.kbf-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 24px;
    background: var(--surface-2);
    border-top: 1px solid var(--border);
}
.kbf-btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: var(--radius-sm);
    background: var(--accent);
    color: #fff;
    font-family: 'Syne', sans-serif;
    font-size: 13.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 14px -2px rgba(37,99,235,.4);
    transition: background .15s, transform .1s, box-shadow .15s;
    letter-spacing: .2px;
}
.kbf-btn-save:hover { background: #1d4ed8; box-shadow: 0 6px 20px -4px rgba(37,99,235,.5); transform: translateY(-1px); }
.kbf-btn-save:active { transform: scale(.97); }
.kbf-btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: var(--radius-sm);
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--ink-2);
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    transition: background .15s, color .15s, border-color .15s;
    cursor: pointer;
}
.kbf-btn-cancel:hover { background: var(--surface-3); color: var(--ink); border-color: var(--ink-3); }

/* ── Sidebar ── */
.kbf-sidebar { display: flex; flex-direction: column; gap: 16px; }

/* Tips card */
.kbf-tips {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.kbf-tips-header {
    padding: 14px 16px;
    background: linear-gradient(135deg, #ede9fe, #dbeafe);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
html.dark .kbf-tips-header { background: linear-gradient(135deg, #2e1f5e44, #1e3a5f44); }
.kbf-tips-title {
    font-family: 'Syne', sans-serif;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .3px;
}
.kbf-tips-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 12px; }
.kbf-tip {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.kbf-tip-num {
    width: 20px; height: 20px; flex-shrink: 0;
    border-radius: 50%;
    background: var(--accent);
    color: #fff;
    font-size: 10px;
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin-top: 1px;
}
.kbf-tip-text { font-size: 12.5px; color: var(--ink-2); line-height: 1.5; }
.kbf-tip-text strong { color: var(--ink); font-weight: 600; }

/* Meta card */
.kbf-meta {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.kbf-meta-header {
    padding: 12px 16px;
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
    font-family: 'Syne', sans-serif;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--ink-3);
}
.kbf-meta-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; }
.kbf-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.kbf-meta-key { font-size: 12px; color: var(--ink-3); }
.kbf-meta-val { font-size: 12.5px; font-weight: 600; color: var(--ink-2); font-family: 'Syne', sans-serif; }

/* Category quick-pick */
.kbf-cat-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.kbf-cat-pill {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    font-family: 'Syne', sans-serif;
    cursor: pointer;
    border: 1.5px solid var(--border);
    color: var(--ink-2);
    background: var(--surface-2);
    transition: all .15s;
    user-select: none;
}
.kbf-cat-pill:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }
.kbf-cat-pill.active { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }

/* Error states */
.kbf-error { font-size: 12px; color: var(--danger); margin-top: 5px; display: flex; align-items: center; gap: 4px; }
.kbf-input.has-error, .kbf-textarea.has-error, .kbf-select.has-error { border-color: var(--danger); }
</style>

<div class="kbf-page text-slate-900 dark:text-slate-100 font-sans">

    {{-- Breadcrumb --}}
    <nav class="kbf-breadcrumb">
        <a href="{{ route('support.ui.knowledge.index') }}">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="vertical-align:-2px">
                <path d="M9 4.804A7.968 7.968 0 0 0 5.8 3.25H4.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5H5.8A7.968 7.968 0 0 0 9 12.696V4.804ZM11 12.696a7.968 7.968 0 0 0 3.2 1.554H15.5a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5H14.2A7.968 7.968 0 0 0 11 4.804v7.892Z"/>
            </svg>
            Base de connaissance
        </a>
        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" style="color:var(--border)">
            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
        </svg>
        <span>{{ $item ? 'Modifier l\'article' : 'Nouvel article' }}</span>
    </nav>

    <form method="POST" action="{{ $item ? route('support.ui.knowledge.update', $item->id) : route('support.ui.knowledge.store') }}" id="kbf-form">
        @csrf
        @if($item) @method('PUT') @endif

        <div class="kbf-layout">

            {{-- Main form --}}
            <div>
                <div class="kbf-card">
                    <div class="kbf-card-header">
                        <div class="kbf-card-header-icon">
                            @if($item)
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/>
                                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z"/>
                                </svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                                </svg>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $item ? 'Modifier l\'article' : 'Créer un nouvel article' }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $item ? 'Mettez à jour les informations de cet article.' : 'Renseignez le problème, la solution et les métadonnées.' }}
                        </p>
                    </div>

                    <div class="kbf-card-body">

                        {{-- Keywords --}}
                        <div class="kbf-field">
                            <label for="problem_keywords" class="kbf-label">
                                <span class="kbf-label-icon">
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                Question / Mots-clés
                                <span class="kbf-label-req">Obligatoire</span>
                            </label>
                            <input type="text"
                                   id="problem_keywords"
                                   name="problem_keywords"
                                   value="{{ old('problem_keywords', $item->problem_keywords ?? '') }}"
                                   placeholder="ex: écran noir au démarrage, Windows ne répond plus…"
                                   class="kbf-input @error('problem_keywords') has-error @enderror">
                            @error('problem_keywords')
                                <p class="kbf-error">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="kbf-hint">
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/></svg>
                                Décrivez le problème ou saisissez des mots-clés de recherche
                            </p>
                        </div>

                        {{-- Solution --}}
                        <div class="kbf-field">
                            <label for="solution" class="kbf-label">
                                <span class="kbf-label-icon">
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                Solution
                                <span class="kbf-label-req">Obligatoire</span>
                            </label>
                            <textarea id="solution"
                                      name="solution"
                                      placeholder="Décrivez la solution étape par étape…&#10;&#10;1. Redémarrer le service&#10;2. Vérifier les logs&#10;3. ..."
                                      class="kbf-textarea @error('solution') has-error @enderror">{{ old('solution', $item->solution ?? '') }}</textarea>
                            @error('solution')
                                <p class="kbf-error">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="kbf-divider"></div>

                        {{-- Category --}}
                        <div class="kbf-field">
                            <label for="category" class="kbf-label">
                                <span class="kbf-label-icon">
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v.944a1 1 0 0 1-.445.832l-3.668 2.445a1 1 0 0 0 0 1.558l3.668 2.445A1 1 0 0 1 16 11.056V16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4.944a1 1 0 0 1 .445-.832l3.668-2.445a1 1 0 0 0 0-1.558L5.445 3.776A1 1 0 0 1 5 2.944V2Z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                Catégorie
                            </label>
                            <input type="text"
                                   id="category"
                                   name="category"
                                   value="{{ old('category', $item->category ?? '') }}"
                                   placeholder="ex: Réseau, Sécurité, Logiciel…"
                                   class="kbf-input @error('category') has-error @enderror"
                                   autocomplete="off">
                            {{-- Quick-pick pills --}}
                            <div class="kbf-cat-pills" id="cat-pills">
                                @foreach(['Réseau','Sécurité','Matériel','Logiciel','Email','Autre'] as $cat)
                                    <span class="kbf-cat-pill {{ old('category', $item->category ?? '') == $cat ? 'active' : '' }}"
                                          data-cat="{{ $cat }}">{{ $cat }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Author --}}
                        <div class="kbf-field">
                            <label for="author_id" class="kbf-label">
                                <span class="kbf-label-icon">
                                    <svg width="11" height="11" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/>
                                    </svg>
                                </span>
                                Auteur
                            </label>
                            <select id="author_id" name="author_id" class="kbf-select @error('author_id') has-error @enderror">
                                <option value="">— Sélectionner un auteur</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ (old('author_id', $item->author_id ?? '') == $u->id) ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Footer actions --}}
                    <div class="kbf-actions">
                        <button type="submit" class="kbf-btn-save">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                @if($item)
                                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/>
                                @else
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                @endif
                            </svg>
                            {{ $item ? 'Enregistrer les modifications' : 'Créer l\'article' }}
                        </button>
                        <a href="{{ route('support.ui.knowledge.index') }}" class="kbf-btn-cancel">
                            <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                            </svg>
                            Annuler
                        </a>

                        @if($item)
                            <form method="POST" action="{{ route('support.ui.knowledge.destroy', $item) }}"
                                  style="margin-left:auto"
                                  onsubmit="return confirm('Supprimer cet article définitivement ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="kbf-btn-cancel" style="color:var(--danger);border-color:var(--danger)20">
                                    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="kbf-sidebar">

                {{-- Tips --}}
                <div class="kbf-tips">
                    <div class="kbf-tips-header">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="color:#7c3aed">
                            <path d="M10 1a6 6 0 0 1 3.804 10.655C13.01 12.498 13 14.375 13 15a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1c0-.625-.01-2.502-.804-3.345A6 6 0 0 1 10 1ZM9 17h2a1 1 0 1 1-2 0Z"/>
                        </svg>
                        <span class="kbf-tips-title">Conseils de rédaction</span>
                    </div>
                    <div class="kbf-tips-body">
                        <div class="kbf-tip">
                            <div class="kbf-tip-num">1</div>
                            <p class="kbf-tip-text"><strong>Mots-clés précis</strong> — Utilisez les termes exacts que les techniciens saisiraient lors d'une recherche.</p>
                        </div>
                        <div class="kbf-tip">
                            <div class="kbf-tip-num">2</div>
                            <p class="kbf-tip-text"><strong>Solution étape par étape</strong> — Numérotez chaque action pour faciliter le suivi.</p>
                        </div>
                        <div class="kbf-tip">
                            <div class="kbf-tip-num">3</div>
                            <p class="kbf-tip-text"><strong>Catégorie cohérente</strong> — Utilisez les catégories existantes pour améliorer le filtrage.</p>
                        </div>
                    </div>
                </div>

                {{-- Meta info (edit mode only) --}}
                @if($item)
                <div class="kbf-meta">
                    <div class="kbf-meta-header">Informations</div>
                    <div class="kbf-meta-body">
                        <div class="kbf-meta-row">
                            <span class="kbf-meta-key">Créé le</span>
                            <span class="kbf-meta-val">{{ $item->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="kbf-meta-row">
                            <span class="kbf-meta-key">Modifié le</span>
                            <span class="kbf-meta-val">{{ $item->updated_at->format('d M Y') }}</span>
                        </div>
                        @if($item->author)
                        <div class="kbf-meta-row">
                            <span class="kbf-meta-key">Auteur</span>
                            <span class="kbf-meta-val">{{ $item->author->name }}</span>
                        </div>
                        @endif
                        @if($item->category)
                        <div class="kbf-meta-row">
                            <span class="kbf-meta-key">Catégorie</span>
                            <span class="kbf-meta-val">{{ $item->category }}</span>
                        </div>
                        @endif
                        <div class="kbf-meta-row">
                            <span class="kbf-meta-key">ID</span>
                            <span class="kbf-meta-val" style="font-family:monospace;font-size:11px">#{{ $item->id }}</span>
                        </div>
                    </div>
                </div>
                @endif

            </aside>

        </div>
    </form>

</div>

<script>
// Category pill quick-select
document.querySelectorAll('.kbf-cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        const input = document.getElementById('category');
        const val = pill.dataset.cat;
        input.value = val;
        document.querySelectorAll('.kbf-cat-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
    });
});

// Keep pills in sync when typing
document.getElementById('category')?.addEventListener('input', function() {
    document.querySelectorAll('.kbf-cat-pill').forEach(p => {
        p.classList.toggle('active', p.dataset.cat === this.value);
    });
});
</script>

@endsection
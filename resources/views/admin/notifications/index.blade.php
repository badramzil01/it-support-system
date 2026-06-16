@extends('admin.layouts.app')
@section('title','Notifications')
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold">Centre de notifications</h1>
            <p class="text-sm text-slate-500 mt-1">Toutes vos alertes et activités récentes</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="markAllReadBtn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-sm">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M2 10a8 8 0 1116 0A8 8 0 012 10zm8-4a1 1 0 00-1 1v3H6a1 1 0 100 2h6a1 1 0 001-1V7a1 1 0 00-1-1z"/></svg>
                Tout marquer lu
            </button>
            <button id="bulkDeleteBtn" class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-red-50 text-red-700 hover:bg-red-100 text-sm">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zM7 9a1 1 0 012 0v5a1 1 0 11-2 0V9zm4 0a1 1 0 112 0v5a1 1 0 11-2 0V9z"/></svg>
                Supprimer tout
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1">
                <input id="notifSearch" type="search" placeholder="Rechercher..." class="w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200" />
            </div>
            <div class="flex items-center gap-2">
                <button data-filter="all" class="filter-btn px-3 py-1 rounded bg-slate-100 text-sm">Tous</button>
                <button data-filter="pending" class="filter-btn px-3 py-1 rounded bg-amber-50 text-sm">En attente</button>
                <button data-filter="sent" class="filter-btn px-3 py-1 rounded bg-green-50 text-sm">Envoyés</button>
            </div>
        </div>

        <div id="notifList" class="divide-y divide-slate-100">
            @forelse($notifications as $n)
                @if(!empty($useLaravel) && $useLaravel)
                    @php
                        $title = $n->data['title'] ?? ($n->title ?? 'Notification');
                        $body = $n->data['body'] ?? '';
                        $read = !is_null($n->read_at);
                        $time = $n->created_at;
                    @endphp
                @else
                    @php
                        $title = $n->ticket->title ?? ('Notification #' . $n->id);
                        $body = 'Type: ' . ($n->type ?? '-') . ' • Statut: ' . ($n->status ?? '-');
                        $read = ($n->status ?? '') !== 'pending';
                        $time = $n->sent_at ?? $n->created_at;
                    @endphp
                @endif

                <div class="p-4 flex items-start gap-4 hover:bg-slate-50 transition" data-notif-id="{{ $n->id }}">
                    <div class="flex-shrink-0 mt-1">
                        <div class="h-9 w-9 rounded-full flex items-center justify-center bg-blue-100 text-blue-700">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3l-1 2h14l-1-2V8a6 6 0 00-6-6z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="text-sm font-medium truncate">{{ $title }}</div>
                                @if(! $read)
                                    <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded bg-amber-50 text-amber-700">Non lu</span>
                                @else
                                    <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded bg-slate-100 text-slate-600">Lu</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-400">{{ $time->diffForHumans() }}</div>
                        </div>
                        @if(!empty($body))
                            <div class="text-sm text-slate-600 mt-2 truncate">{{ \Illuminate\Support\Str::limit($body, 240) }}</div>
                        @endif
                    </div>
                    <div class="flex-shrink-0 flex flex-col items-end gap-2">
                        <div class="flex gap-2">
                            @if(! $read)
                                <button class="mark-read-btn text-xs px-3 py-1 rounded bg-amber-50 text-amber-700 hover:bg-amber-100" data-id="{{ $n->id }}">Marquer lu</button>
                            @endif
                            <form method="POST" action="{{ route('admin.notifications.destroy', $n->id) }}" onsubmit="return confirm('Supprimer cette notification ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500">Aucune notification pour le moment.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Mark single notification as read
        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = e.currentTarget.dataset.id;                const res = await fetch(`{{ url('equipeIT/ui/notifications') }}/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                if (res.ok) {
                    const node = document.querySelector(`div[data-notif-id='${id}']`);
                    node.querySelector('.mark-read-btn')?.remove();
                    node.querySelector('.inline-flex')?.classList.remove('bg-amber-50');
                    node.querySelector('.inline-flex')?.classList.add('bg-slate-100');
                    const badge = document.getElementById('notifBadge'); if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent||'0') - 1);
                }
            });
        });

        // Mark all read
        document.getElementById('markAllReadBtn')?.addEventListener('click', async () => {
            if (!confirm('Marquer toutes les notifications comme lues ?')) return;
            const res = await fetch(`{{ url('equipeIT/ui/notifications/read-all') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            if (res.ok) {
                document.querySelectorAll('.mark-read-btn').forEach(b => b.remove());
                document.querySelectorAll('#notifList .inline-flex').forEach(el => { el.classList.remove('bg-amber-50'); el.classList.add('bg-slate-100'); });
                const badge = document.getElementById('notifBadge'); if (badge) badge.textContent = '0';
            }
        });

        // Bulk delete
        document.getElementById('bulkDeleteBtn')?.addEventListener('click', async () => {
            if (!confirm('Supprimer toutes les notifications ?')) return;
            const res = await fetch(`{{ url('equipeIT/ui/notifications') }}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            if (res.ok) location.reload(); else alert('Erreur lors de la suppression.');
        });

        // Simple client-side filtering (non-destructive)
        document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', (e) => {
            const f = e.target.dataset.filter;
            document.querySelectorAll('#notifList > div[data-notif-id]').forEach(item => {
                if (f === 'all') { item.style.display = ''; return; }
                const isPending = !!item.querySelector('.mark-read-btn');
                if ((f === 'pending' && isPending) || (f === 'sent' && !isPending)) item.style.display = ''; else item.style.display = 'none';
            });
        }));

        // Search
        document.getElementById('notifSearch')?.addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('#notifList > div[data-notif-id]').forEach(item => {
                const txt = item.textContent.toLowerCase();
                item.style.display = txt.includes(q) ? '' : 'none';
            });
        });
    })();
</script>
@endpush

@endsection

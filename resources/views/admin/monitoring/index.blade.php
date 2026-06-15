@extends('admin.layouts.app')
@section('title', 'Monitoring système')
@section('content')

<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="brand-font text-xl font-bold text-slate-900 dark:text-white">État des services</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Surveillance en temps réel · {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.ui.monitoring.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.433a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd"/></svg>
            Rafraîchir
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($services as $s)
        @php
            $statusColor = match($s['status']) {
                'online' => 'bg-emerald-500',
                'degraded' => 'bg-amber-500',
                'offline' => 'bg-red-500',
                default => 'bg-slate-400',
            };
            $statusLabel = match($s['status']) {
                'online' => ['text' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'En ligne'],
                'degraded' => ['text' => 'text-amber-600 dark:text-amber-400', 'label' => 'Dégradé'],
                'offline' => ['text' => 'text-red-600 dark:text-red-400', 'label' => 'Hors ligne'],
                default => ['text' => 'text-slate-500 dark:text-slate-400', 'label' => 'Inconnu'],
            };
            $bgColor = match($s['status']) {
                'online' => 'bg-emerald-50 dark:bg-emerald-900/20',
                'degraded' => 'bg-amber-50 dark:bg-amber-900/20',
                'offline' => 'bg-red-50 dark:bg-red-900/20',
                default => 'bg-slate-50 dark:bg-slate-800',
            };
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 cursor-pointer hover:shadow-md transition card-service" data-service="{{ $s['id'] }}">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $bgColor }}">
                        @if($s['icon'] === 'server')
                        <svg class="h-5 w-5 {{ $statusLabel['text'] }}" viewBox="0 0 20 20" fill="currentColor"><path d="M4.464 3.162A2 2 0 0 1 6.279 2h7.442a2 2 0 0 1 1.815 1.162l1.484 3.183A2 2 0 0 1 17 7.755V15a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7.755a2 2 0 0 1 .02-1.41l1.444-3.183Z"/></svg>
                        @elseif($s['icon'] === 'database')
                        <svg class="h-5 w-5 {{ $statusLabel['text'] }}" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1c3.3 0 6 1 6 2.5V15c0 1.5-2.7 2.5-6 2.5s-6-1-6-2.5V3.5C4 2 6.7 1 10 1Z"/><path d="M4 7.5c0 1.5 2.7 2.5 6 2.5s6-1 6-2.5M4 12c0 1.5 2.7 2.5 6 2.5s6-1 6-2.5"/></svg>
                        @elseif($s['icon'] === 'flow')
                        <svg class="h-5 w-5 {{ $statusLabel['text'] }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.577 4.878a.75.75 0 0 1 .919-.53l4.78 1.281a.75.75 0 0 1 .531.919l-1.281 4.78a.75.75 0 0 1-1.449-.387l.81-3.022a19.407 19.407 0 0 0-5.594 5.203.75.75 0 0 1-1.139.093L7 10.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06l5.25-5.25a.75.75 0 0 1 1.06 0l3.074 3.073a20.923 20.923 0 0 1 5.545-4.931l-3.042.815a.75.75 0 0 1-.53-.919Z" clip-rule="evenodd"/></svg>
                        @elseif($s['icon'] === 'mail')
                        <svg class="h-5 w-5 {{ $statusLabel['text'] }}" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                        @else
                        <svg class="h-5 w-5 {{ $statusLabel['text'] }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.535 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.636-.544.298-1.584-.535-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $s['name'] }}</p>
                        <p class="text-[11px] {{ $statusLabel['text'] }} font-medium flex items-center gap-1.5" id="status-{{ $s['id'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $statusColor }} {{ $s['status']==='online' ? 'pulse' : '' }}"></span>
                            <span class="status-label">{{ $statusLabel['label'] }}</span>
                        </p>
                    </div>
                </div>
                <div>
                    <button class="test-btn p-2 text-xs text-slate-500 hover:text-amber-600 transition" data-service="{{ $s['id'] }}" title="Tester la connexion">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>
            </div>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Version</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['version'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Temps de réponse</dt><dd class="font-medium text-slate-700 dark:text-slate-200" id="time-{{ $s['id'] }}">{{ $s['response'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Uptime</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['uptime'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Dernière sync</dt><dd class="font-medium text-slate-700 dark:text-slate-200" id="sync-{{ $s['id'] }}">{{ $s['last_sync'] }}</dd></div>
            </dl>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Service Details -->
<div id="serviceModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">
        <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Configuration</h3>
              <div class="mt-4">
                
                <div id="modal-loader" class="flex justify-center py-6">
                    <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div id="modal-error-box" class="hidden mb-4 rounded-lg bg-red-50 p-4 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Erreur rencontrée (<span id="modal-error-time"></span>)</h3>
                    <div class="mt-2 text-xs text-red-700 dark:text-red-300 font-mono break-all" id="modal-error-text"></div>
                </div>

                <form id="service-config-form" class="hidden space-y-4">
                    @csrf
                    <input type="hidden" id="current-service-id">
                    <div id="dynamic-fields"></div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse border-t border-slate-200 dark:border-slate-800 pt-4">
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 sm:ml-3 sm:w-auto">Enregistrer</button>
                        <button type="button" class="close-modal mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">Annuler</button>
                    </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Handle manual test button
        document.querySelectorAll('.test-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent opening the modal
                let service = this.dataset.service;
                let icon = this.querySelector('svg');
                
                icon.classList.add('animate-spin');
                
                fetch('/admin/integrations/test/' + service, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(response => {
                    icon.classList.remove('animate-spin');
                    let statusEl = document.getElementById('status-' + service);
                    let timeEl = document.getElementById('time-' + service);
                    let syncEl = document.getElementById('sync-' + service);
                    
                    if(response.success) {
                        statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-emerald-500 pulse"></span> <span class="status-label">En ligne</span>';
                        statusEl.className = 'text-[11px] font-medium flex items-center gap-1.5 text-emerald-600';
                        timeEl.textContent = response.time + ' ms';
                        let now = new Date();
                        syncEl.textContent = ('0'+now.getDate()).slice(-2)+'/'+('0'+(now.getMonth()+1)).slice(-2)+'/'+now.getFullYear()+' '+('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+':'+('0'+now.getSeconds()).slice(-2);
                    } else {
                        statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> <span class="status-label">Hors ligne</span>';
                        statusEl.className = 'text-[11px] font-medium flex items-center gap-1.5 text-red-600';
                        timeEl.textContent = '—';
                    }
                })
                .catch(() => {
                    icon.classList.remove('animate-spin');
                    let statusEl = document.getElementById('status-' + service);
                    statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> <span class="status-label">Erreur</span>';
                    statusEl.className = 'text-[11px] font-medium flex items-center gap-1.5 text-red-600';
                });
            });
        });

        // Handle Card Click -> Open Modal
        const serviceModal = document.getElementById('serviceModal');
        const modalTitle = document.getElementById('modal-title');
        const currentServiceId = document.getElementById('current-service-id');
        const modalLoader = document.getElementById('modal-loader');
        const serviceConfigForm = document.getElementById('service-config-form');
        const modalErrorBox = document.getElementById('modal-error-box');
        const modalErrorText = document.getElementById('modal-error-text');
        const modalErrorTime = document.getElementById('modal-error-time');
        const dynamicFields = document.getElementById('dynamic-fields');

        document.querySelectorAll('.card-service').forEach(card => {
            card.addEventListener('click', function() {
                let serviceId = this.dataset.service;
                let serviceName = this.querySelector('p.font-semibold').textContent;
                
                modalTitle.textContent = 'Configuration : ' + serviceName;
                currentServiceId.value = serviceId;
                
                modalLoader.style.display = 'flex';
                serviceConfigForm.style.display = 'none';
                modalErrorBox.style.display = 'none';
                dynamicFields.innerHTML = '';
                
                serviceModal.classList.remove('hidden');

                fetch('/admin/monitoring/' + serviceId + '/details')
                .then(res => res.json())
                .then(response => {
                    modalLoader.style.display = 'none';
                    
                    if (response.last_error) {
                        modalErrorBox.style.display = 'block';
                        modalErrorText.textContent = response.last_error;
                        modalErrorTime.textContent = response.last_error_time;
                    }

                    if (response.fields && response.fields.length > 0) {
                        let html = '';
                        response.fields.forEach(function(field) {
                            html += '<div>';
                            html += '<label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">'+field.label+'</label>';
                            html += '<input type="'+field.type+'" name="'+field.key+'" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" value="'+(field.value || '')+'" />';
                            html += '</div>';
                        });
                        dynamicFields.innerHTML = html;
                        serviceConfigForm.style.display = 'block';
                    } else {
                        dynamicFields.innerHTML = '<p class="text-sm text-slate-500 dark:text-slate-400">Aucune configuration modifiable pour ce service.</p>';
                        serviceConfigForm.style.display = 'block';
                    }
                });
            });
        });

        // Close Modal
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                serviceModal.classList.add('hidden');
            });
        });

        // Submit Config Form
        serviceConfigForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let serviceId = currentServiceId.value;
            let submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> En cours...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            fetch('/admin/monitoring/' + serviceId + '/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(response => {
                serviceModal.classList.add('hidden');
                submitBtn.textContent = 'Enregistrer';
                submitBtn.disabled = false;
                
                let statusEl = document.getElementById('status-' + serviceId);
                let timeEl = document.getElementById('time-' + serviceId);
                let syncEl = document.getElementById('sync-' + serviceId);
                
                if(response.success) {
                    statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-emerald-500 pulse"></span> <span class="status-label">En ligne</span>';
                    statusEl.className = 'text-[11px] font-medium flex items-center gap-1.5 text-emerald-600';
                    timeEl.textContent = response.time + ' ms';
                    let now = new Date();
                    syncEl.textContent = ('0'+now.getDate()).slice(-2)+'/'+('0'+(now.getMonth()+1)).slice(-2)+'/'+now.getFullYear()+' '+('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+':'+('0'+now.getSeconds()).slice(-2);
                } else {
                    statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> <span class="status-label">Hors ligne</span>';
                    statusEl.className = 'text-[11px] font-medium flex items-center gap-1.5 text-red-600';
                    timeEl.textContent = '—';
                }
            })
            .catch(() => {
                submitBtn.textContent = 'Enregistrer';
                submitBtn.disabled = false;
                alert("Erreur lors de la sauvegarde.");
            });
        });
    });
</script>
@endpush
@endsection

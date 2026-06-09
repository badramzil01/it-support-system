<?php $__env->startSection('title', 'Communication Interne'); ?>
<?php $__env->startSection('content'); ?>
<?php
use Illuminate\Support\Str;
$typeStyles = [
    'message'      => ['bg' => 'bg-slate-105 dark:bg-slate-800 text-slate-700 dark:text-slate-350 border border-slate-200 dark:border-slate-750', 'label' => 'Message'],
    'request'      => ['bg' => 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900', 'label' => 'Demande'],
    'info_request' => ['bg' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900', 'label' => 'Demande d\'infos'],
    'follow_up'    => ['bg' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900', 'label' => 'Suivi'],
];
?>

<div class="space-y-5">
    
    <div>
        <h2 class="brand-font text-xl font-bold text-slate-900 dark:text-white">Communication Interne</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Échangez directement avec les Administrateurs et Managers de la plateforme.</p>
    </div>

    
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)_260px] h-[650px] divide-y lg:divide-y-0 lg:divide-x divide-slate-250 dark:divide-slate-800">
            
            
            <div class="flex flex-col overflow-hidden bg-slate-50/20 dark:bg-slate-900/10">
                <div class="px-4 py-3 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-550">Administration</h4>
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    <?php $__empty_1 = true; $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isActive = optional($selectedUser)->id === $member->id;
                            $unread = $member->unread_count ?? 0;
                        ?>
                        <a href="<?php echo e(route('support.ui.internal.index', ['with' => $member->id])); ?>"
                           class="flex items-center gap-3 p-3.5 transition hover:bg-slate-55 dark:hover:bg-slate-800/40 relative border-l-4 
                                  <?php echo e($isActive ? 'bg-blue-600/5 dark:bg-blue-600/10 border-blue-600' : 'border-transparent'); ?>">
                            <div class="relative shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-xs font-bold text-white dark:text-slate-950">
                                    <?php echo e(Str::upper(Str::substr($member->name, 0, 1))); ?>

                                </div>
                                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-900"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($member->name); ?></p>
                                    <?php if($unread > 0): ?>
                                        <span class="rounded-full bg-blue-600 text-[10px] font-bold text-white px-1.5 py-0.5"><?php echo e($unread); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="truncate text-[10px] text-slate-455 dark:text-slate-500 font-medium"><?php echo e($member->email); ?></p>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-5 text-xs text-slate-400 text-center font-medium">Aucun administrateur/manager trouvé.</div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="flex flex-col overflow-hidden bg-slate-50/30 dark:bg-slate-950/20">
                <?php if($selectedUser): ?>
                    
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-3 bg-white dark:bg-slate-900 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-xs font-bold text-white dark:text-slate-950">
                                <?php echo e(Str::upper(Str::substr($selectedUser->name, 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($selectedUser->name); ?></p>
                                <p class="text-[10px] text-slate-400 font-medium"><?php echo e($selectedUser->email); ?></p>
                            </div>
                        </div>
                    </div>

                    
                    <div id="internalChatBox" class="flex-1 overflow-y-auto p-5 space-y-4" data-last-message-id="0">
                        <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isMe = $msg->sender_id === $userId;
                                $typeConfig = $typeStyles[$msg->type] ?? $typeStyles['message'];
                            ?>
                            <div class="flex items-end gap-2.5 <?php echo e($isMe ? 'justify-end' : 'justify-start'); ?>" data-message-id="<?php echo e($msg->id); ?>">
                                <?php if(!$isMe): ?>
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-250 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-250">
                                        <?php echo e(Str::upper(Str::substr($selectedUser->name, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                                <div class="max-w-[75%] space-y-1">
                                    <div class="flex items-center gap-2 px-1 <?php echo e($isMe ? 'justify-end' : ''); ?>">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                            <?php echo e($isMe ? 'Vous (Support)' : $msg->sender->name); ?>

                                        </span>
                                        <span class="text-[9px] text-slate-450 font-semibold"><?php echo e($msg->created_at->format('d/m H:i')); ?></span>
                                    </div>
                                    <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm relative 
                                          <?php echo e($isMe ? 'bg-[#4F6EF7] text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-tl-none'); ?>">
                                        
                                        <?php if($msg->type !== 'message'): ?>
                                            <span class="inline-block text-[9px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full mb-1.5 <?php echo e($typeConfig['bg']); ?>">
                                                <?php echo e($typeConfig['label']); ?>

                                            </span>
                                        <?php endif; ?>

                                        <p class="whitespace-pre-line"><?php echo e($msg->content); ?></p>

                                        <?php if($msg->ticket): ?>
                                            <div class="mt-2.5 pt-2.5 border-t border-black/10 dark:border-white/10 flex flex-col gap-1.5">
                                                <div class="rounded-xl bg-slate-100/50 dark:bg-slate-900/50 p-2.5 border border-black/5 dark:border-white/5 text-xs text-slate-650 dark:text-slate-350">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-bold">Ticket #<?php echo e($msg->ticket->id); ?></span>
                                                        <span class="text-[9px] font-extrabold tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-450"><?php echo e($msg->ticket->status); ?></span>
                                                    </div>
                                                    <p class="mt-1 font-semibold truncate"><?php echo e($msg->ticket->title); ?></p>
                                                </div>
                                                <a href="<?php echo e(route('support.ui.tickets.show', $msg->ticket)); ?>" class="inline-flex items-center justify-center bg-slate-950 dark:bg-slate-150 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 text-[10px] font-bold py-1 px-3.5 rounded-lg shadow-sm">
                                                    Ouvrir le ticket
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($isMe): ?>
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#4F6EF7] text-[10px] font-bold text-white">SP</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex flex-col items-center justify-center py-16 text-center text-slate-400">
                                <svg class="h-10 w-10 text-slate-300 dark:text-slate-700 mb-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-xs font-bold">Aucun message échangé</p>
                                <p class="text-[11px] text-slate-500 font-medium">Démarrez la conversation ci-dessous.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shrink-0">
                        <form method="POST" action="<?php echo e(route('support.ui.internal.send')); ?>" id="internalSendForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="receiver_id" value="<?php echo e($selectedUser->id); ?>">
                            
                            <textarea name="content" id="internalContent" rows="2" required
                                      class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400 dark:text-slate-100 transition"
                                      placeholder="Écrire un message interne…"></textarea>

                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/60">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Type de message</label>
                                    <select name="type" class="w-full rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 py-1.5 px-3 text-xs outline-none focus:border-blue-500 dark:text-slate-350">
                                        <option value="message">Message simple</option>
                                        <option value="request">Demande</option>
                                        <option value="info_request">Demande d'information</option>
                                        <option value="follow_up">Suivi de dossier</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Associer un ticket (Optionnel)</label>
                                    <?php
                                        $availableTickets = \App\Models\Ticket::where('assigned_to', auth()->id())->orderBy('created_at', 'desc')->take(20)->get();
                                    ?>
                                    <select name="ticket_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 py-1.5 px-3 text-xs outline-none focus:border-blue-500 dark:text-slate-350">
                                        <option value="">Aucun ticket</option>
                                        <?php $__currentLoopData = $availableTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($t->id); ?>">#<?php echo e($t->id); ?> - <?php echo e(Str::limit($t->title, 40)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3.5 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 px-5 py-2 text-xs font-bold text-white transition shadow-sm shadow-blue-500/10">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3.105 2.289a.75.75 0 0 0-.826.95l1.414 4.949a.75.75 0 0 0 .702.544l6.364.243c.34.013.34.509 0 .522l-6.364.243a.75.75 0 0 0-.702.544l-1.414 4.95a.75.75 0 0 0 .826.949 43.789 43.789 0 0 0 14.822-6.607.75.75 0 0 0 0-1.18A43.789 43.789 0 0 0 3.105 2.289Z"/>
                                    </svg>
                                    Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="flex flex-1 flex-col items-center justify-center gap-2.5 text-center p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10 text-[#4F6EF7]">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-350">Sélectionnez un administrateur / manager</p>
                        <p class="text-xs text-slate-400 font-medium">Choisissez un membre de la direction dans la liste latérale gauche pour démarrer la messagerie interne.</p>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="flex flex-col overflow-y-auto bg-white dark:bg-slate-900">
                <?php if($selectedUser): ?>
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Profil Administrateur</h4>
                        <dl class="space-y-2.5 text-xs">
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Nom</dt>
                                <dd class="font-bold text-slate-800 dark:text-slate-200"><?php echo e($selectedUser->name); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Email</dt>
                                <dd class="font-bold text-blue-600 dark:text-blue-400 truncate"><?php echo e($selectedUser->email); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Rôles</dt>
                                <dd class="mt-1 flex flex-wrap gap-1">
                                    <?php $__currentLoopData = $selectedUser->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold bg-blue-500/10 text-[#4F6EF7] rounded-full border border-blue-500/20"><?php echo e($role->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="p-4">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Statistiques d'échange</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2 border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Envoyés</p>
                                <p class="text-base font-extrabold mt-0.5"><?php echo e($stats['sent']); ?></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2 border border-slate-100 dark:border-slate-800 text-center">
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Reçus</p>
                                <p class="text-base font-extrabold mt-0.5"><?php echo e($stats['received']); ?></p>
                            </div>
                            <div class="rounded-xl bg-blue-50 dark:bg-blue-950/25 p-2 border border-blue-100/60 dark:border-blue-900/40 text-center">
                                <p class="text-[8px] font-bold text-[#4F6EF7] uppercase tracking-wider">Non lus</p>
                                <p class="text-base font-extrabold mt-0.5"><?php echo e($stats['unread']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex flex-1 items-center justify-center p-6 text-xs text-slate-450 font-bold">Sélectionnez un profil</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<?php if($selectedUser): ?>
<script>
    let currentUserId = <?php echo e($selectedUser->id); ?>;
    let lastMessageId = 0;
    let pollInterval = null;

    function scrollChatToBottom() {
        const box = document.getElementById('internalChatBox');
        if (box) box.scrollTop = box.scrollHeight;
    }

    async function pollMessages() {
        const box = document.getElementById('internalChatBox');
        if (!box) return;

        try {
            const response = await fetch(`/equipeIT/ui/internal-communication/api/messages/${currentUserId}?last_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (document.querySelector(`[data-message-id="${msg.id}"]`)) return;

                    const isMe = msg.sender_id === <?php echo e($userId); ?>;
                    
                    const wrap = document.createElement('div');
                    wrap.className = `flex items-end gap-2.5 ${isMe ? 'justify-end' : 'justify-start'}`;
                    wrap.dataset.messageId = msg.id;

                    let badgeHtml = '';
                    if (msg.type !== 'message') {
                        let badgeBg = 'bg-slate-100 text-slate-700';
                        if (msg.type === 'request') badgeBg = 'bg-amber-100 text-amber-700';
                        if (msg.type === 'info_request') badgeBg = 'bg-blue-100 text-blue-700';
                        if (msg.type === 'follow_up') badgeBg = 'bg-emerald-100 text-emerald-700';

                        badgeHtml = `<span class="inline-block text-[9px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full mb-1.5 ${badgeBg}">${msg.type_label}</span>`;
                    }

                    let ticketHtml = '';
                    if (msg.ticket) {
                        ticketHtml = `
                            <div class="mt-2.5 pt-2.5 border-t border-black/10 dark:border-white/10 flex flex-col gap-1.5">
                                <div class="rounded-xl bg-slate-100/50 dark:bg-slate-900/50 p-2.5 border border-black/5 dark:border-white/5 text-xs text-slate-650 dark:text-slate-350">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold">Ticket #${msg.ticket.id}</span>
                                        <span class="text-[9px] font-extrabold tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-450">${msg.ticket.status}</span>
                                    </div>
                                    <p class="mt-1 font-semibold truncate">${msg.ticket.title}</p>
                                </div>
                                <a href="/equipeIT/ui/tickets/${msg.ticket.id}" class="inline-flex items-center justify-center bg-slate-950 dark:bg-slate-150 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 text-[10px] font-bold py-1 px-3.5 rounded-lg shadow-sm">
                                    Ouvrir le ticket
                                </a>
                            </div>
                        `;
                    }

                    wrap.innerHTML = `
                        ${!isMe ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-250 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-250">${escHtml(msg.sender.name.charAt(0).toUpperCase())}</div>` : ''}
                        <div class="max-w-[75%] space-y-1">
                            <div class="flex items-center gap-2 px-1 ${isMe ? 'justify-end' : ''}">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">${isMe ? 'Vous (Support)' : escHtml(msg.sender.name)}</span>
                                <span class="text-[9px] text-slate-455 font-semibold">${fmtDt(msg.created_at)}</span>
                            </div>
                            <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm relative ${isMe ? 'bg-[#4F6EF7] text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-tl-none'}">
                                ${badgeHtml}
                                <p class="whitespace-pre-line">${escHtml(msg.content)}</p>
                                ${ticketHtml}
                            </div>
                        </div>
                        ${isMe ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#4F6EF7] text-[10px] font-bold text-white font-semibold">SP</div>` : ''}
                    `;

                    box.appendChild(wrap);
                    lastMessageId = msg.id;
                });
                scrollChatToBottom();
            }
        } catch(e) {
            console.error('polling error', e);
        }
    }

    window.onload = function() {
        const box = document.getElementById('internalChatBox');
        if (box) {
            const items = box.querySelectorAll('[data-message-id]');
            if (items.length > 0) {
                lastMessageId = parseInt(items[items.length - 1].dataset.messageId);
            }
        }
        scrollChatToBottom();
        
        pollInterval = setInterval(pollMessages, 5000);
        
        fetch(`/equipeIT/ui/internal-communication/api/mark-read/${currentUserId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
    };
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/internal-communication/index.blade.php ENDPATH**/ ?>
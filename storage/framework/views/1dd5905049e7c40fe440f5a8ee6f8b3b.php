<?php $__env->startSection('title', 'Conversations'); ?>
<?php $__env->startSection('content'); ?>
<?php
use Illuminate\Support\Str;
$globalStats = [
    'users'       => $users->count(),
    'tickets'     => $users->sum('tickets_count'),
    'urgent'      => $users->sum('urgent_tickets_count'),
    'escalated'   => $users->sum('escalated_tickets_count'),
    'unread'      => $users->sum('unread_messages_count'),
    'convs'       => $users->sum('conversations_count'),
];
$senderStyles = [
    'user'    => 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100',
    'support' => 'bg-blue-600 text-white',
    'agent'   => 'bg-blue-600 text-white',
    'ai'      => 'bg-violet-600 text-white',
    'bot'     => 'bg-violet-600 text-white',
    'system'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
];
$ticketForActions = $tickets->first();
?>


<div class="mb-5">
    <div class="mb-3 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold">Centre de communication</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Vue globale · <?php echo e(now()->format('d/m/Y H:i')); ?></p>
        </div>
        <form method="GET" action="<?php echo e(route('support.ui.conversations.index')); ?>" class="relative">
            <svg class="pointer-events-none absolute left-2.5 top-2 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
            <input name="search" value="<?php echo e($search ?? ''); ?>" placeholder="Rechercher un utilisateur…"
                   class="w-56 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1.5 pl-8 pr-3 text-xs outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400">
        </form>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Utilisateurs</p>
            <p class="mt-1 text-2xl font-semibold"><?php echo e($globalStats['users']); ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Conversations</p>
            <p class="mt-1 text-2xl font-semibold"><?php echo e($globalStats['convs']); ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4">
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Tickets total</p>
            <p class="mt-1 text-2xl font-semibold"><?php echo e($globalStats['tickets']); ?></p>
        </div>
        <div class="rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900 p-4">
            <p class="text-[11px] text-red-600 dark:text-red-400">Urgents</p>
            <p class="mt-1 text-2xl font-semibold text-red-700 dark:text-red-300"><?php echo e($globalStats['urgent']); ?></p>
        </div>
        <div class="rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900 p-4">
            <p class="text-[11px] text-amber-700 dark:text-amber-400">Escaladés</p>
            <p class="mt-1 text-2xl font-semibold text-amber-800 dark:text-amber-300"><?php echo e($globalStats['escalated']); ?></p>
        </div>
        <div class="rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 p-4">
            <p class="text-[11px] text-blue-700 dark:text-blue-400">Non lus</p>
            <p class="mt-1 text-2xl font-semibold text-blue-800 dark:text-blue-300"><?php echo e($globalStats['unread']); ?></p>
        </div>
    </div>
</div>


<div class="mb-5">
    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
        Utilisateurs — <?php echo e($users->count()); ?> résultats
    </h3>

    <?php if($users->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 dark:border-slate-700 py-16 text-center">
            <svg class="mb-3 h-10 w-10 text-slate-300 dark:text-slate-700" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-6-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm-2 4a5 5 0 0 0-4.546 2.916A5.986 5.986 0 0 0 10 16a5.986 5.986 0 0 0 4.546-2.084A5 5 0 0 0 10 11Z" clip-rule="evenodd"/></svg>
            <p class="text-sm font-medium text-slate-500">Aucun utilisateur trouvé</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="userGrid">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $lastMsg = $user->latestMessage;
                    $isActive = optional($selectedUser)->id === $user->id;
                ?>
                <button type="button"
                        data-user-id="<?php echo e($user->id); ?>"
                        onclick="loadUserPanel(<?php echo e($user->id); ?>)"
                        class="user-card text-left rounded-xl border p-4 transition
                               <?php echo e($isActive
                                   ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/40 ring-2 ring-blue-500/20'
                                   : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50'); ?>">
                    <div class="flex items-start gap-3">
                        <div class="relative shrink-0">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-sm font-semibold text-white dark:text-slate-900">
                                <?php echo e(Str::upper(Str::substr($user->name, 0, 1))); ?>

                            </div>
                            <?php if(($user->unread_messages_count ?? 0) > 0): ?>
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-blue-600 text-[9px] font-bold text-white"><?php echo e(min($user->unread_messages_count, 9)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold"><?php echo e($user->name); ?></p>
                            <p class="truncate text-[11px] text-slate-500 dark:text-slate-400"><?php echo e($user->email); ?></p>
                            <?php if($lastMsg): ?>
                                <p class="mt-1 line-clamp-1 text-[11px] text-slate-600 dark:text-slate-300"><?php echo e($lastMsg->content); ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="shrink-0 text-[10px] text-slate-400"><?php echo e($lastMsg?->created_at?->diffForHumans(null, true)); ?></span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-300"><?php echo e($user->conversations_count ?? 0); ?> conv.</span>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-300"><?php echo e($user->tickets_count ?? 0); ?> tickets</span>
                        <?php if($user->urgent_tickets_count ?? 0): ?>
                            <span class="rounded-full bg-red-100 dark:bg-red-950 px-2 py-0.5 text-[10px] font-medium text-red-700 dark:text-red-300">Urgent</span>
                        <?php endif; ?>
                        <?php if($user->escalated_tickets_count ?? 0): ?>
                            <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2 py-0.5 text-[10px] font-medium text-amber-800 dark:text-amber-300">Escaladé</span>
                        <?php endif; ?>
                    </div>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>


<div id="chatPanel" class="<?php echo e($selectedUser ? '' : 'hidden'); ?> rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">

    
    <div id="panelHeader" class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center gap-3">
            <div id="panelAvatar" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-sm font-semibold text-white dark:text-slate-900">
                <?php echo e($selectedUser ? Str::upper(Str::substr($selectedUser->name, 0, 1)) : ''); ?>

            </div>
            <div>
                <p id="panelName" class="text-sm font-semibold"><?php echo e($selectedUser?->name); ?></p>
                <p id="panelEmail" class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($selectedUser?->email); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div id="panelBadges" class="flex flex-wrap gap-1.5">
                <?php if($selectedUser): ?>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2.5 py-1 text-[11px] font-medium text-slate-600 dark:text-slate-300"><?php echo e($tickets->count()); ?> tickets</span>
                    <?php if($tickets->where('is_urgent', 1)->count()): ?>
                        <span class="rounded-full bg-red-100 dark:bg-red-950 px-2.5 py-1 text-[11px] font-medium text-red-700 dark:text-red-300"><?php echo e($tickets->where('is_urgent',1)->count()); ?> urgents</span>
                    <?php endif; ?>
                    <?php if($tickets->where('is_escalated', 1)->count()): ?>
                        <span class="rounded-full bg-amber-100 dark:bg-amber-950 px-2.5 py-1 text-[11px] font-medium text-amber-800 dark:text-amber-300"><?php echo e($tickets->where('is_escalated',1)->count()); ?> escaladés</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <button onclick="closeChatPanel()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Fermer">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)_260px]" style="height: 600px;">

        
        <div class="flex flex-col border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                <h4 class="text-xs font-semibold text-slate-700 dark:text-slate-200">Conversations</h4>
                <?php if($selectedUser): ?>
                    <span class="text-[10px] text-slate-400"><?php echo e($conversationsList->count()); ?></span>
                <?php endif; ?>
            </div>
            <div id="convList" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                <?php if($selectedUser): ?>
                    <?php $__empty_1 = true; $__currentLoopData = $conversationsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $activeConv = optional($selectedConversation)->id === $conv->id; ?>
                        <button type="button"
                                onclick="loadConversation(<?php echo e($conv->id); ?>)"
                                class="conv-item w-full text-left block p-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/60
                                       <?php echo e($activeConv ? 'bg-slate-100 dark:bg-slate-800 border-l-2 border-blue-600' : ''); ?>"
                                data-conv-id="<?php echo e($conv->id); ?>">
                            <p class="truncate text-xs font-semibold"><?php echo e($conv->title ?: 'Conversation #'.$conv->id); ?></p>
                            <p class="mt-0.5 text-[10px] text-slate-400"><?php echo e($conv->latestMessage?->created_at?->format('d/m H:i') ?? $conv->updated_at?->format('d/m H:i')); ?></p>
                            <p class="mt-0.5 line-clamp-1 text-[10px] text-slate-500 dark:text-slate-400"><?php echo e($conv->latestMessage?->content); ?></p>
                            <div class="mt-1 flex gap-1">
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 text-[9px] text-slate-500 dark:text-slate-300"><?php echo e($conv->messages_count); ?> msg</span>
                                <?php if($conv->tickets->first()): ?>
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-950 px-1.5 py-0.5 text-[9px] text-emerald-700 dark:text-emerald-300">Ticket</span>
                                <?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-4 text-[11px] text-slate-400 text-center">Aucune conversation</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="p-4 text-[11px] text-slate-400 text-center">—</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="flex flex-col overflow-hidden">

            
            <div id="chatLoading" class="hidden flex-1 items-center justify-center">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Chargement…
                </div>
            </div>

            
            <div id="chatEmpty" class="<?php echo e($selectedConversation ? 'hidden' : 'flex'); ?> flex-1 flex-col items-center justify-center gap-2 text-center p-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                    <svg class="h-6 w-6 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5Zm3.293 1.293a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414L7.586 10 5.293 7.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Sélectionnez une conversation</p>
                <p class="text-xs text-slate-400">Les messages apparaîtront ici</p>
            </div>

            
            <div id="chatContent" class="<?php echo e($selectedConversation ? 'flex' : 'hidden'); ?> flex-col flex-1 overflow-hidden">

                
                <div id="chatHeader" class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4 py-2.5">
                    <p id="chatTitle" class="text-xs font-semibold truncate text-slate-700 dark:text-slate-200">
                        <?php echo e($selectedConversation?->title ?: ($selectedConversation ? 'Conversation #'.$selectedConversation->id : '')); ?>

                    </p>
                    <div class="flex items-center gap-2">
                        <?php if($selectedConversation && !$ticketForActions): ?>
                            <form method="POST" action="<?php echo e(route('support.ui.conversations.tickets.create')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="conversation_id" id="createTicketConvId" value="<?php echo e($selectedConversation?->id); ?>">
                                <button class="inline-flex items-center gap-1 rounded-lg bg-slate-900 dark:bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-white dark:text-slate-900 hover:bg-slate-700 dark:hover:bg-white transition">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                                    Créer ticket
                                </button>
                            </form>
                        <?php elseif($ticketForActions): ?>
                            <a href="<?php echo e(route('support.ui.tickets.show', $ticketForActions)); ?>" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1 text-[11px] font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Ticket #<?php echo e($ticketForActions->id); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($triggerTicket && $triggerMessage): ?>
                    <div class="border-b border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/40 px-4 py-2">
                        <div class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495ZM10 5.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm0 8.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-amber-900 dark:text-amber-100">Message déclencheur · <?php echo e($triggerTicket->priority); ?> · <?php echo e($triggerTicket->category); ?></p>
                                <p class="text-[11px] text-amber-800 dark:text-amber-200 italic truncate">"<?php echo e(Str::limit($triggerMessage->content, 100)); ?>"</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div id="chatBox" class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-950 p-4 space-y-3">
                    <?php if($selectedConversation): ?>
                        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sender  = $message->sender ?: 'user';
                                $isRight = in_array($sender, ['support','agent'], true);
                                $isAi    = in_array($sender, ['ai','bot'], true);
                                $bubble  = $senderStyles[$sender] ?? $senderStyles['system'];
                            ?>
                            <div class="flex items-end gap-2 <?php echo e($isRight ? 'justify-end' : 'justify-start'); ?>" data-message-id="<?php echo e($message->id); ?>">
                                <?php if(!$isRight): ?>
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-semibold text-slate-700 dark:text-slate-200">
                                        <?php echo e(Str::upper(Str::substr($selectedUser->name, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                                <div class="max-w-[78%]">
                                    <div class="mb-0.5 flex items-center gap-1.5 <?php echo e($isRight ? 'justify-end' : ''); ?>">
                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                            <?php echo e($sender === 'user' ? $selectedUser->name : ($isAi ? 'IA' : 'Support')); ?>

                                        </span>
                                        <span class="text-[10px] text-slate-400"><?php echo e($message->created_at->format('d/m H:i')); ?></span>
                                    </div>
                                    <div class="rounded-2xl px-3.5 py-2 text-sm leading-relaxed <?php echo e($bubble); ?> <?php echo e($isRight ? 'rounded-br-sm' : 'rounded-bl-sm'); ?>">
                                        <p class="whitespace-pre-line"><?php echo e($message->content); ?></p>
                                    </div>
                                </div>
                                <?php if($isRight): ?>
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950 text-[10px] font-semibold text-blue-700 dark:text-blue-300">SP</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                
                <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3">
                    <?php if($errors->any()): ?>
                        <div class="mb-2 rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950 px-3 py-1.5 text-xs text-red-700 dark:text-red-300"><?php echo e($errors->first()); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('support.ui.conversations.send')); ?>" id="replyForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="user_id" id="replyUserId" value="<?php echo e($selectedUser?->id); ?>">
                        <input type="hidden" name="conversation_id" id="replyConvId" value="<?php echo e($selectedConversation?->id); ?>">
                        <textarea name="content" id="replyContent" rows="2" required
                                  class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-950 placeholder:text-slate-400"
                                  placeholder="Écrire une réponse…"><?php echo e(old('content')); ?></textarea>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Réponse en tant que <strong class="text-slate-500">Support</strong></span>
                            <div class="flex items-center gap-2">
                                <button type="submit" name="_as" value="ai"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-[11px] font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <svg class="h-3 w-3 text-violet-500" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="8"/></svg>
                                    IA
                                </button>
                                <button type="submit" name="_as" value="support"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-blue-700 active:scale-95 transition">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M3.105 2.289a.75.75 0 0 0-.826.95l1.414 4.949a.75.75 0 0 0 .702.544l6.364.243c.34.013.34.509 0 .522l-6.364.243a.75.75 0 0 0-.702.544l-1.414 4.95a.75.75 0 0 0 .826.949 43.789 43.789 0 0 0 14.822-6.607.75.75 0 0 0 0-1.18A43.789 43.789 0 0 0 3.105 2.289Z"/></svg>
                                    Envoyer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
        <div id="sidebar" class="flex flex-col overflow-y-auto border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-slate-800">
            <?php if($selectedUser): ?>
                
                <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                    <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Statistiques client</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-2.5">
                            <p class="text-[10px] text-slate-500">Conversations</p>
                            <p class="text-xl font-semibold"><?php echo e($customerStats['conversations']); ?></p>
                        </div>
                        <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-2.5">
                            <p class="text-[10px] text-slate-500">Tickets</p>
                            <p class="text-xl font-semibold"><?php echo e($customerStats['tickets']); ?></p>
                        </div>
                        <div class="rounded-lg bg-red-50 dark:bg-red-950/40 p-2.5">
                            <p class="text-[10px] text-red-600 dark:text-red-400">Urgents</p>
                            <p class="text-xl font-semibold text-red-700 dark:text-red-300"><?php echo e($customerStats['urgent']); ?></p>
                        </div>
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/40 p-2.5">
                            <p class="text-[10px] text-amber-700 dark:text-amber-400">Escaladés</p>
                            <p class="text-xl font-semibold text-amber-800 dark:text-amber-300"><?php echo e($customerStats['escalated']); ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                    <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Informations</h4>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-500">Département</dt>
                            <dd class="font-medium truncate"><?php echo e($selectedUser->department ?? $selectedUser->role ?? '—'); ?></dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-500">Inscription</dt>
                            <dd class="font-medium"><?php echo e($selectedUser->created_at?->format('d/m/Y')); ?></dd>
                        </div>
                        <div class="flex justify-between gap-2">
                            <dt class="text-slate-500">Email</dt>
                            <dd class="font-medium truncate text-blue-600 dark:text-blue-400"><?php echo e($selectedUser->email); ?></dd>
                        </div>
                    </dl>
                </div>

                
                <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                    <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Dernier ticket</h4>
                    <?php if($lastTicket): ?>
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                            <p class="text-xs font-semibold">#<?php echo e($lastTicket->id); ?> · <?php echo e(Str::limit($lastTicket->title ?: 'Ticket support', 35)); ?></p>
                            <p class="mt-1 text-[10px] text-slate-500"><?php echo e($lastTicket->created_at?->format('d/m/Y H:i')); ?></p>
                            <div class="mt-2 flex flex-wrap gap-1">
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[9px] text-slate-600 dark:text-slate-300"><?php echo e(ucfirst($lastTicket->status)); ?></span>
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[9px] text-slate-600 dark:text-slate-300"><?php echo e(ucfirst($lastTicket->priority ?: 'medium')); ?></span>
                                <?php if($lastTicket->jira_ticket_id): ?>
                                    <span class="rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[9px] font-medium text-blue-700 dark:text-blue-300"><?php echo e($lastTicket->jira_ticket_id); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400">Aucun ticket créé.</p>
                    <?php endif; ?>
                </div>

                
                <div class="p-4">
                    <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Actions</h4>
                    <div class="space-y-2">
                        <?php if($selectedConversation): ?>
                            <form method="POST" action="<?php echo e(route('support.ui.conversations.tickets.create')); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="conversation_id" value="<?php echo e($selectedConversation->id); ?>">
                                <button class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                                    Créer ticket
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if($ticketForActions): ?>
                            <form method="POST" action="<?php echo e(route('support.ui.conversations.tickets.assign', $ticketForActions)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 8a7 7 0 1 1 14 0 .75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 17Z"/></svg>
                                    Assigner à moi
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('support.ui.conversations.tickets.escalate', $ticketForActions)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white hover:bg-amber-600 transition">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495ZM10 5.25a.75.75 0 0 1 .75.75v4a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm0 8.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                    Escalader
                                </button>
                            </form>
                            <a href="<?php echo e(route('support.ui.tickets.show', $ticketForActions)); ?>" class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Voir ticket associé
                            </a>
                            <?php if($ticketForActions->jira_ticket_id): ?>
                                <a href="<?php echo e(rtrim(config('services.jira.url','#'), '/').'/browse/'.$ticketForActions->jira_ticket_id); ?>" target="_blank" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 transition">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.061l1.224-1.225a4 4 0 0 0-5.656-5.656L8.929 5.414a.75.75 0 1 0 1.06 1.061l2.243-2.243Z"/><path d="M7.768 15.768a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.061l-1.224 1.225a4 4 0 1 0 5.656 5.656l2.243-2.242a.75.75 0 1 0-1.06-1.061l-2.243 2.243Z"/></svg>
                                    Ouvrir dans Jira
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-1 items-center justify-center p-6 text-xs text-slate-400">Sélectionnez un client</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const API_PREFIX = '<?php echo e(url('equipeIT')); ?>';

function escHtml(v) {
    return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtDt(v) {
    const d = v ? new Date(v) : new Date();
    return d.toLocaleString('fr-FR',{day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
}
function bubbleCls(sender) {
    if (sender==='support'||sender==='agent') return 'bg-blue-600 text-white rounded-2xl rounded-br-sm';
    if (sender==='ai'||sender==='bot')        return 'bg-violet-600 text-white rounded-2xl rounded-bl-sm';
    if (sender==='user')                      return 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl rounded-bl-sm';
    return 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl';
}

let currentUserId   = <?php echo e($selectedUser?->id ?? 'null'); ?>;
let currentConvId   = <?php echo e($selectedConversation?->id ?? 'null'); ?>;
let pollingInterval = null;

/* ── Ouvrir le panneau chat pour un user ── */
async function loadUserPanel(userId) {
    if (currentUserId === userId && !document.getElementById('chatPanel').classList.contains('hidden')) {
        document.getElementById('chatPanel').scrollIntoView({behavior:'smooth', block:'nearest'});
        return;
    }

    currentUserId = userId;
    currentConvId = null;

    document.querySelectorAll('.user-card').forEach(c => {
        const active = parseInt(c.dataset.userId) === userId;
        c.classList.toggle('border-blue-500', active);
        c.classList.toggle('bg-blue-50', active);
        c.classList.toggle('dark:bg-blue-950/40', active);
        c.classList.toggle('ring-2', active);
        c.classList.toggle('ring-blue-500/20', active);
        c.classList.toggle('border-slate-200', !active);
        c.classList.toggle('dark:border-slate-800', !active);
        c.classList.toggle('bg-white', !active);
        c.classList.toggle('dark:bg-slate-900', !active);
    });

    document.getElementById('chatPanel').classList.remove('hidden');
    document.getElementById('chatPanel').scrollIntoView({behavior:'smooth', block:'start'});

    showChatState('loading');

    try {
        const res = await fetch(`${API_PREFIX}/support/api/user-panel/${userId}`, {
            headers: {'Accept':'application/json','X-CSRF-TOKEN': CSRF}
        });
        const data = await res.json();

        document.getElementById('panelAvatar').textContent = data.user.name.charAt(0).toUpperCase();
        document.getElementById('panelName').textContent   = data.user.name;
        document.getElementById('panelEmail').textContent  = data.user.email;

        renderConvList(data.conversations, null);
        renderSidebar(data.user, data.stats, data.last_ticket);

        updateReplyInputs(userId, null);
        showChatState('empty');

        if (data.conversations.length > 0) {
            await loadConversation(data.conversations[0].id);
        }
    } catch(e) {
        console.error('loadUserPanel error', e);
        showChatState('empty');
        // Do not redirect on AJAX failure — keep the SPA-like panel open
        // The UI will remain usable; admins can retry the action.
    }
}

/* ── Charger une conversation ── */
async function loadConversation(convId) {
    currentConvId = convId;

    document.querySelectorAll('.conv-item').forEach(el => {
        const active = parseInt(el.dataset.convId) === convId;
        el.classList.toggle('bg-slate-100', active);
        el.classList.toggle('dark:bg-slate-800', active);
        el.classList.toggle('border-l-2', active);
        el.classList.toggle('border-blue-600', active);
    });

    showChatState('loading');

    try {
        const res = await fetch(`${API_PREFIX}/support/api/conversation/${convId}`, {
            headers: {'Accept':'application/json','X-CSRF-TOKEN': CSRF}
        });
        const data = await res.json();

        document.getElementById('chatTitle').textContent = data.conversation.title || `Conversation #${data.conversation.id}`;
        document.getElementById('createTicketConvId')?.setAttribute('value', convId);
        updateReplyInputs(currentUserId, convId);

        renderMessages(data.messages, data.selected_user?.name ?? '');
        showChatState('content');
        scrollChatToBottom();

        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => pollNewMessages(convId), 5000);
    } catch(e) {
        console.error('loadConversation error', e);
        showChatState('empty');
        // Do not redirect on AJAX failure — remain on the current page
        // This prevents a full page reload when loading conversations fails.
    }
}

/* ── Polling nouveaux messages ── */
async function pollNewMessages(convId) {
    if (convId !== currentConvId) return;
    const chatBox = document.getElementById('chatBox');
    if (!chatBox) return;

    const lastId = parseInt(chatBox.dataset.lastMessageId ?? 0);
    try {
        const res  = await fetch(`/api/messages/${convId}`, {headers:{'Accept':'application/json'}});
        if (!res.ok) return;
        const data = await res.json();
        const incoming = (data.messages ?? []).filter(m => parseInt(m.id) > lastId);
        incoming.forEach(m => appendMessage(m, chatBox));
    } catch(e) {}
}

/* ── Render helpers ── */
function renderMessages(messages, userName) {
    const box = document.getElementById('chatBox');
    if (!box) return;
    box.innerHTML = '';
    box.dataset.lastMessageId = messages.length ? messages[messages.length-1].id : 0;

    messages.forEach(m => appendMessage(m, box, userName));
}

function appendMessage(m, box, userName) {
    if (!box) return;
    if (box.querySelector(`[data-message-id="${m.id}"]`)) return;

    userName = userName || document.getElementById('panelName')?.textContent || 'Client';
    const sender  = m.sender || 'user';
    const isRight = sender === 'support' || sender === 'agent';
    const isAi    = sender === 'ai' || sender === 'bot';
    const label   = sender === 'user' ? userName : (isAi ? 'IA' : 'Support');

    const wrap = document.createElement('div');
    wrap.className = `flex items-end gap-2 ${isRight ? 'justify-end' : 'justify-start'}`;
    wrap.dataset.messageId = m.id;

    wrap.innerHTML = `
        ${!isRight ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-semibold text-slate-700 dark:text-slate-200">${escHtml(label.charAt(0).toUpperCase())}</div>` : ''}
        <div class="max-w-[78%]">
            <div class="mb-0.5 flex items-center gap-1.5 ${isRight ? 'justify-end' : ''}">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">${escHtml(label)}</span>
                <span class="text-[10px] text-slate-400">${fmtDt(m.created_at)}</span>
            </div>
            <div class="px-3.5 py-2 text-sm leading-relaxed ${bubbleCls(sender)}">
                <p class="whitespace-pre-line">${escHtml(m.content || m.response || '')}</p>
            </div>
        </div>
        ${isRight ? `<div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950 text-[10px] font-semibold text-blue-700 dark:text-blue-300">SP</div>` : ''}
    `;

    box.appendChild(wrap);
    box.dataset.lastMessageId = m.id;
    scrollChatToBottom();
}

function renderConvList(conversations, activeId) {
    const list = document.getElementById('convList');
    if (!list) return;

    if (!conversations.length) {
        list.innerHTML = '<div class="p-4 text-[11px] text-slate-400 text-center">Aucune conversation</div>';
        return;
    }

    list.innerHTML = conversations.map(conv => {
        const isActive = conv.id === activeId;
        return `<button type="button" onclick="loadConversation(${conv.id})"
            class="conv-item w-full text-left block p-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 ${isActive ? 'bg-slate-100 dark:bg-slate-800 border-l-2 border-blue-600' : ''}"
            data-conv-id="${conv.id}">
            <p class="truncate text-xs font-semibold">${escHtml(conv.title || `Conversation #${conv.id}`)}</p>
            <p class="mt-0.5 text-[10px] text-slate-400">${conv.updated_at ? fmtDt(conv.updated_at) : ''}</p>
            <p class="mt-0.5 text-[10px] text-slate-500 line-clamp-1">${escHtml(conv.latest_message?.content ?? '')}</p>
            <div class="mt-1 flex gap-1">
                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 text-[9px] text-slate-500 dark:text-slate-300">${conv.messages_count ?? 0} msg</span>
                ${conv.has_ticket ? '<span class="rounded-full bg-emerald-100 dark:bg-emerald-950 px-1.5 py-0.5 text-[9px] text-emerald-700 dark:text-emerald-300">Ticket</span>' : ''}
            </div>
        </button>`;
    }).join('');
}

function renderSidebar(user, stats, lastTicket) {
    const container = document.getElementById('sidebar');
    if (!container) return;

    container.innerHTML = `
        <div class="border-b border-slate-200 dark:border-slate-800 p-4">
            <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Statistiques client</h4>
            <div class="grid grid-cols-2 gap-2">
                <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-2.5">
                    <p class="text-[10px] text-slate-500">Conversations</p>
                    <p class="text-xl font-semibold">${escHtml(stats.conversations ?? 0)}</p>
                </div>
                <div class="rounded-lg bg-slate-50 dark:bg-slate-800 p-2.5">
                    <p class="text-[10px] text-slate-500">Tickets</p>
                    <p class="text-xl font-semibold">${escHtml(stats.tickets ?? 0)}</p>
                </div>
                <div class="rounded-lg bg-red-50 dark:bg-red-950/40 p-2.5">
                    <p class="text-[10px] text-red-600 dark:text-red-400">Urgents</p>
                    <p class="text-xl font-semibold text-red-700 dark:text-red-300">${escHtml(stats.urgent ?? 0)}</p>
                </div>
                <div class="rounded-lg bg-amber-50 dark:bg-amber-950/40 p-2.5">
                    <p class="text-[10px] text-amber-700 dark:text-amber-400">Escaladés</p>
                    <p class="text-xl font-semibold text-amber-800 dark:text-amber-300">${escHtml(stats.escalated ?? 0)}</p>
                </div>
            </div>
        </div>

        <div class="border-b border-slate-200 dark:border-slate-800 p-4">
            <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Informations</h4>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Nom</dt>
                    <dd class="font-medium truncate">${escHtml(user.name || '')}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-medium text-blue-600 dark:text-blue-400">${escHtml(user.email || '')}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Inscription</dt>
                    <dd class="font-medium">${lastTicket && lastTicket.created_at ? escHtml(new Date(lastTicket.created_at).toLocaleDateString('fr-FR')) : '—'}</dd>
                </div>
            </dl>
        </div>

        <div class="border-b border-slate-200 dark:border-slate-800 p-4">
            <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Dernier ticket</h4>
            ${ lastTicket ? (`
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                    <p class="text-xs font-semibold">#${escHtml(lastTicket.id)} · ${escHtml(String(lastTicket.title || '').slice(0,35))}</p>
                    <p class="mt-1 text-[10px] text-slate-500">${lastTicket.created_at ? escHtml(fmtDt(lastTicket.created_at)) : ''}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[9px] text-slate-600 dark:text-slate-300">${escHtml(lastTicket.status || '')}</span>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[9px] text-slate-600 dark:text-slate-300">${escHtml(lastTicket.priority || 'medium')}</span>
                        ${ lastTicket.jira_ticket_id ? `<span class="rounded-full bg-blue-100 dark:bg-blue-950 px-2 py-0.5 text-[9px] font-medium text-blue-700 dark:text-blue-300">${escHtml(lastTicket.jira_ticket_id)}</span>` : '' }
                    </div>
                </div>
            `) : ('<p class="text-xs text-slate-400">Aucun ticket créé.</p>') }
        </div>

        <div class="p-4">
            <h4 class="mb-3 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Actions</h4>
            <div class="space-y-2">
                <div class="text-xs text-slate-500">Sélectionnez une conversation pour voir les actions disponibles.</div>
            </div>
        </div>
    `;
}

function updateReplyInputs(userId, convId) {
    const ui = document.getElementById('replyUserId');
    const ci = document.getElementById('replyConvId');
    if (ui) ui.value = userId ?? '';
    if (ci) ci.value = convId ?? '';
}

function showChatState(state) {
    const loading = document.getElementById('chatLoading');
    const empty   = document.getElementById('chatEmpty');
    const content = document.getElementById('chatContent');

    loading?.classList.toggle('hidden', state !== 'loading');
    loading?.classList.toggle('flex', state === 'loading');
    empty?.classList.toggle('hidden', state !== 'empty');
    empty?.classList.toggle('flex', state === 'empty');
    content?.classList.toggle('hidden', state !== 'content');
    content?.classList.toggle('flex', state === 'content');
}

function scrollChatToBottom() {
    const box = document.getElementById('chatBox');
    if (box) box.scrollTop = box.scrollHeight;
}

function closeChatPanel() {
    document.getElementById('chatPanel').classList.add('hidden');
    if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
    document.querySelectorAll('.user-card').forEach(c => {
        c.classList.remove('border-blue-500','bg-blue-50','dark:bg-blue-950/40','ring-2','ring-blue-500/20');
        c.classList.add('border-slate-200','dark:border-slate-800','bg-white','dark:bg-slate-900');
    });
    currentUserId = null;
    currentConvId = null;
}

/* Auto-scroll on load if conversation already selected */
document.addEventListener('DOMContentLoaded', () => {
    const box = document.getElementById('chatBox');
    if (box) box.scrollTop = box.scrollHeight;

    <?php if($selectedUser): ?>
        document.getElementById('chatPanel')?.scrollIntoView({behavior:'smooth', block:'start'});
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/conversations/index.blade.php ENDPATH**/ ?>
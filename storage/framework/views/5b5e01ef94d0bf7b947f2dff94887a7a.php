<?php $__env->startSection('title', 'Conversations Clients'); ?>
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
    'support' => 'bg-amber-600 text-white',
    'agent'   => 'bg-amber-600 text-white',
    'ai'      => 'bg-violet-600 text-white',
    'bot'     => 'bg-violet-600 text-white',
    'system'  => 'bg-slate-100 dark:bg-slate-750 text-slate-600 dark:text-slate-300',
];
$ticketForActions = $tickets->first();
?>

<div class="space-y-5">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="brand-font text-xl font-bold text-slate-900 dark:text-white">Conversations Clients</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Gérez les échanges entre les utilisateurs clients et l'équipe de support.</p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.ui.conversations.index')); ?>" class="relative w-full sm:w-64">
            <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.131 3.131a.75.75 0 1 0 1.061-1.06l-3.131-3.132A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/>
            </svg>
            <input name="search" value="<?php echo e($search ?? ''); ?>" placeholder="Rechercher un client…"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-2 pl-9 pr-4 text-xs outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 placeholder:text-slate-400 dark:text-slate-200 transition">
        </form>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Clients</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white"><?php echo e($globalStats['users']); ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Conversations</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white"><?php echo e($globalStats['convs']); ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tickets associés</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white"><?php echo e($globalStats['tickets']); ?></p>
        </div>
        <div class="rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/40 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-red-650 dark:text-red-400 uppercase tracking-wider">Tickets Urgents</p>
            <p class="mt-1 text-2xl font-bold text-red-700 dark:text-red-350"><?php echo e($globalStats['urgent']); ?></p>
        </div>
        <div class="rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Tickets Escaladés</p>
            <p class="mt-1 text-2xl font-bold text-amber-800 dark:text-amber-300"><?php echo e($globalStats['escalated']); ?></p>
        </div>
        <div class="rounded-2xl bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 p-4 shadow-sm">
            <p class="text-[10px] font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Non lus</p>
            <p class="mt-1 text-2xl font-bold text-blue-800 dark:text-blue-300"><?php echo e($globalStats['unread']); ?></p>
        </div>
    </div>

    
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Clients Actifs
        </h3>

        <?php if($users->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-350 dark:border-slate-800 py-16 text-center bg-white dark:bg-slate-900">
                <svg class="mb-3 h-10 w-10 text-slate-300 dark:text-slate-700" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-6-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm-2 4a5 5 0 0 0-4.546 2.916A5.986 5.986 0 0 0 10 16a5.986 5.986 0 0 0 4.546-2.084A5 5 0 0 0 10 11Z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-slate-550 dark:text-slate-400">Aucun client trouvé</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="userGrid">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $lastMsg = $user->latestMessage;
                        $isActive = optional($selectedUser)->id === $user->id;
                    ?>
                    <button type="button"
                            data-user-id="<?php echo e($user->id); ?>"
                            onclick="loadUserPanel(<?php echo e($user->id); ?>)"
                            class="user-card text-left rounded-2xl border p-4 transition duration-200 group shadow-sm
                                   <?php echo e($isActive
                                       ? 'border-amber-500 bg-amber-500/5 dark:bg-amber-500/10 ring-2 ring-amber-500/20'
                                       : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/40'); ?>">
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-900 dark:bg-slate-100 text-sm font-bold text-white dark:text-slate-950 transition duration-200 group-hover:scale-105">
                                    <?php echo e(Str::upper(Str::substr($user->name, 0, 1))); ?>

                                </div>
                                <?php if(($user->unread_messages_count ?? 0) > 0): ?>
                                    <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950 ring-2 ring-white dark:ring-slate-900"><?php echo e(min($user->unread_messages_count, 99)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition"><?php echo e($user->name); ?></p>
                                <p class="truncate text-[11px] text-slate-450 dark:text-slate-500 font-medium"><?php echo e($user->email); ?></p>
                                <?php if($lastMsg): ?>
                                    <p class="mt-1.5 line-clamp-1 text-xs text-slate-600 dark:text-slate-300 font-medium"><?php echo e($lastMsg->content); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="shrink-0 text-[10px] text-slate-400 dark:text-slate-500 font-semibold"><?php echo e($lastMsg?->created_at?->diffForHumans(null, true)); ?></span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                            <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400"><?php echo e($user->conversations_count ?? 0); ?> conv.</span>
                            <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400"><?php echo e($user->tickets_count ?? 0); ?> tickets</span>
                            <?php if($user->urgent_tickets_count ?? 0): ?>
                                <span class="rounded-full bg-red-100 dark:bg-red-950/80 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:text-red-300">Urgent</span>
                            <?php endif; ?>
                            <?php if($user->escalated_tickets_count ?? 0): ?>
                                <span class="rounded-full bg-orange-100 dark:bg-orange-950/80 px-2 py-0.5 text-[10px] font-bold text-orange-700 dark:text-orange-350">Escaladé</span>
                            <?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div id="chatPanel" class="<?php echo e($selectedUser ? '' : 'hidden'); ?> rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-md">

        
        <div id="panelHeader" class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-4 bg-slate-50 dark:bg-slate-900/50">
            <div class="flex items-center gap-3">
                <div id="panelAvatar" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 dark:bg-slate-100 text-sm font-bold text-white dark:text-slate-950">
                    <?php echo e($selectedUser ? Str::upper(Str::substr($selectedUser->name, 0, 1)) : ''); ?>

                </div>
                <div>
                    <p id="panelName" class="text-sm font-bold text-slate-900 dark:text-white"><?php echo e($selectedUser?->name); ?></p>
                    <p id="panelEmail" class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?php echo e($selectedUser?->email); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div id="panelBadges" class="flex flex-wrap gap-1.5">
                    <?php if($selectedUser): ?>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:text-slate-350"><?php echo e($tickets->count()); ?> tickets</span>
                        <?php if($tickets->where('is_urgent', 1)->count()): ?>
                            <span class="rounded-full bg-red-100 dark:bg-red-950 px-2.5 py-0.5 text-xs font-bold text-red-700 dark:text-red-300"><?php echo e($tickets->where('is_urgent',1)->count()); ?> urgents</span>
                        <?php endif; ?>
                        <?php if($tickets->where('is_escalated', 1)->count()): ?>
                            <span class="rounded-full bg-orange-100 dark:bg-orange-950 px-2.5 py-0.5 text-xs font-bold text-orange-700 dark:text-orange-350"><?php echo e($tickets->where('is_escalated',1)->count()); ?> escaladés</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <button onclick="closeChatPanel()" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-150 dark:hover:bg-slate-800 transition" title="Fermer">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                    </svg>
                </button>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-[240px_minmax(0,1fr)_260px] h-[650px] divide-y lg:divide-y-0 lg:divide-x divide-slate-250 dark:divide-slate-800">

            
            <div class="flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-550">Discussions</h4>
                    <?php if($selectedUser): ?>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-400"><?php echo e($conversationsList->count()); ?></span>
                    <?php endif; ?>
                </div>
                <div id="convList" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    <?php if($selectedUser): ?>
                        <?php $__empty_1 = true; $__currentLoopData = $conversationsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $activeConv = optional($selectedConversation)->id === $conv->id; ?>
                            <button type="button"
                                    onclick="loadConversation(<?php echo e($conv->id); ?>)"
                                    class="conv-item w-full text-left block p-3.5 transition hover:bg-slate-50 dark:hover:bg-slate-800/40 relative
                                           <?php echo e($activeConv ? 'bg-amber-500/5 dark:bg-amber-500/10 border-l-4 border-amber-500' : 'border-l-4 border-transparent'); ?>"
                                    data-conv-id="<?php echo e($conv->id); ?>">
                                <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($conv->title ?: 'Conversation #'.$conv->id); ?></p>
                                <p class="mt-1 text-[10px] text-slate-400 font-semibold"><?php echo e($conv->latestMessage?->created_at?->format('d/m H:i') ?? $conv->updated_at?->format('d/m H:i')); ?></p>
                                <p class="mt-1 line-clamp-1 text-[11px] text-slate-500 dark:text-slate-400 font-medium"><?php echo e($conv->latestMessage?->content); ?></p>
                                <div class="mt-2 flex gap-1 items-center">
                                    <span class="rounded-full bg-slate-100 dark:bg-slate-850 px-1.5 py-0.5 text-[9px] font-semibold text-slate-550 dark:text-slate-450"><?php echo e($conv->messages_count); ?> msg</span>
                                    <?php if($conv->tickets->first()): ?>
                                        <span class="rounded-full bg-emerald-100 dark:bg-emerald-950 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-355">Ticket</span>
                                    <?php endif; ?>
                                </div>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-5 text-xs text-slate-400 font-medium text-center">Aucune conversation</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-5 text-xs text-slate-450 font-medium text-center">—</div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="flex flex-col overflow-hidden bg-slate-50/60 dark:bg-slate-950/40">

                
                <div id="chatLoading" class="hidden flex-1 items-center justify-center">
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <svg class="h-5 w-5 animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Chargement des messages…
                    </div>
                </div>

                
                <div id="chatEmpty" class="<?php echo e($selectedConversation ? 'hidden' : 'flex'); ?> flex-1 flex-col items-center justify-center gap-2.5 text-center p-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Sélectionnez une discussion</p>
                    <p class="text-xs text-slate-400 max-w-xs font-medium">Choisissez une conversation dans la liste de gauche pour afficher l'historique complet.</p>
                </div>

                
                <div id="chatContent" class="<?php echo e($selectedConversation ? 'flex' : 'hidden'); ?> flex-col flex-1 overflow-hidden">

                    
                    <div id="chatHeader" class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-3 bg-white dark:bg-slate-900">
                        <p id="chatTitle" class="text-xs font-bold truncate text-slate-800 dark:text-slate-200">
                            <?php echo e($selectedConversation?->title ?: ($selectedConversation ? 'Conversation #'.$selectedConversation->id : '')); ?>

                        </p>
                        <div class="flex items-center gap-2">
                            <?php if($selectedConversation && !$ticketForActions): ?>
                                <form method="POST" action="<?php echo e(route('admin.ui.conversations.tickets.create')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="conversation_id" id="createTicketConvId" value="<?php echo e($selectedConversation?->id); ?>">
                                    <button class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 dark:bg-slate-100 hover:bg-slate-850 dark:hover:bg-white px-3 py-1.5 text-xs font-bold text-white dark:text-slate-950 active:scale-95 transition shadow-sm">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                                        </svg>
                                        Créer ticket
                                    </button>
                                </form>
                            <?php elseif($ticketForActions): ?>
                                <a href="<?php echo e(route('admin.ui.tickets.show', $ticketForActions)); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-55 dark:hover:bg-slate-800 transition">
                                    Ticket #<?php echo e($ticketForActions->id); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div id="chatBox" class="flex-1 overflow-y-auto bg-slate-50/50 dark:bg-slate-950 p-5 space-y-4">
                        <?php if($selectedConversation): ?>
                            <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $sender  = $message->sender ?: 'user';
                                    $isRight = in_array($sender, ['support','agent'], true);
                                    $isAi    = in_array($sender, ['ai','bot'], true);
                                    $bubble  = $senderStyles[$sender] ?? $senderStyles['system'];

                                    // Check trigger indicators for badges
                                    $msgTicket = $tickets->first(fn ($t) => $t->trigger_message_id === $message->id || $t->message_id === $message->id);
                                ?>
                                <div class="flex items-end gap-2.5 <?php echo e($isRight ? 'justify-end' : 'justify-start'); ?>" data-message-id="<?php echo e($message->id); ?>">
                                    <?php if(!$isRight): ?>
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-350">
                                            <?php echo e(Str::upper(Str::substr($selectedUser->name, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div class="max-w-[75%] space-y-1">
                                        <div class="flex items-center gap-2 px-1 <?php echo e($isRight ? 'justify-end' : ''); ?>">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                <?php echo e($sender === 'user' ? $selectedUser->name : ($isAi ? 'IA Assistant' : 'Support')); ?>

                                            </span>
                                            <span class="text-[9px] font-semibold text-slate-400"><?php echo e($message->created_at->format('d/m H:i')); ?></span>
                                        </div>
                                        <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm relative <?php echo e($bubble); ?> <?php echo e($isRight ? 'rounded-tr-none' : 'rounded-tl-none'); ?>">
                                            <p class="whitespace-pre-line"><?php echo e($message->content); ?></p>

                                            
                                            <?php if($message->image_path): ?>
                                                <div class="mt-2.5 pt-2 border-t border-black/10 dark:border-white/10">
                                                    <a href="<?php echo e(asset($message->image_path)); ?>" target="_blank" class="block rounded-lg overflow-hidden border border-black/5 dark:border-white/5 bg-slate-100 dark:bg-slate-900 group">
                                                        <img src="<?php echo e(asset($message->image_path)); ?>" alt="Pièce jointe" class="max-h-48 w-full object-cover group-hover:opacity-90 transition">
                                                        <span class="block px-2.5 py-1.5 text-[10px] font-semibold text-slate-500 dark:text-slate-450 bg-slate-50 dark:bg-slate-800">Afficher la pièce jointe</span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        
                                        <?php if($msgTicket): ?>
                                            <div class="flex flex-wrap gap-1 px-1 mt-1 <?php echo e($isRight ? 'justify-end' : ''); ?>">
                                                <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">
                                                    Ticket créé #<?php echo e($msgTicket->id); ?>

                                                </span>
                                                <?php if($msgTicket->is_urgent): ?>
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900">
                                                        Urgent
                                                    </span>
                                                <?php endif; ?>
                                                <?php if($msgTicket->is_escalated): ?>
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-900">
                                                        Escaladé
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($isRight): ?>
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950">SP</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                        <?php if($errors->any()): ?>
                            <div class="mb-3 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950 px-4 py-2 text-xs text-red-700 dark:text-red-400 font-medium"><?php echo e($errors->first()); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('admin.ui.conversations.send')); ?>" id="replyForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="user_id" id="replyUserId" value="<?php echo e($selectedUser?->id); ?>">
                            <input type="hidden" name="conversation_id" id="replyConvId" value="<?php echo e($selectedConversation?->id); ?>">
                            <textarea name="content" id="replyContent" rows="2" required
                                      class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-3.5 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 placeholder:text-slate-400 dark:text-slate-100 transition"
                                      placeholder="Écrire un message d'assistance…"><?php echo e(old('content')); ?></textarea>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400 font-medium">Répondre en tant que <strong class="text-slate-500 font-bold">Admin</strong></span>
                                <div class="flex items-center gap-2">
                                    <button type="submit" name="_as" value="ai"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-55 dark:hover:bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                                        <svg class="h-3.5 w-3.5 text-violet-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>
                                        Réponse IA
                                    </button>
                                    <button type="submit" name="_as" value="support"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 px-4 py-2 text-xs font-bold text-slate-950 transition shadow-sm shadow-amber-500/10">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3.105 2.289a.75.75 0 0 0-.826.95l1.414 4.949a.75.75 0 0 0 .702.544l6.364.243c.34.013.34.509 0 .522l-6.364.243a.75.75 0 0 0-.702.544l-1.414 4.95a.75.75 0 0 0 .826.949 43.789 43.789 0 0 0 14.822-6.607.75.75 0 0 0 0-1.18A43.789 43.789 0 0 0 3.105 2.289Z"/>
                                        </svg>
                                        Envoyer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <div id="sidebar" class="flex flex-col overflow-y-auto bg-white dark:bg-slate-900">
                <?php if($selectedUser): ?>
                    
                    <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Informations client</h4>
                        <dl class="space-y-2.5 text-xs">
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Nom</dt>
                                <dd class="font-bold text-slate-800 dark:text-slate-250 truncate"><?php echo e($selectedUser->name); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Email</dt>
                                <dd class="font-bold text-amber-600 dark:text-amber-400 truncate"><?php echo e($selectedUser->email); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Département / Rôle</dt>
                                <dd class="font-bold text-slate-800 dark:text-slate-250 truncate"><?php echo e($selectedUser->department ?? $selectedUser->role ?? 'Client'); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-450 font-semibold mb-0.5">Date d'inscription</dt>
                                <dd class="font-bold text-slate-800 dark:text-slate-250"><?php echo e($selectedUser->created_at?->format('d/m/Y')); ?></dd>
                            </div>
                        </dl>
                    </div>

                    
                    <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Statistiques</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2.5 border border-slate-100 dark:border-slate-800">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Discussions</p>
                                <p class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mt-0.5"><?php echo e($customerStats['conversations']); ?></p>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2.5 border border-slate-100 dark:border-slate-800">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tickets</p>
                                <p class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mt-0.5"><?php echo e($customerStats['tickets']); ?></p>
                            </div>
                            <div class="rounded-xl bg-red-50 dark:bg-red-950/20 p-2.5 border border-red-100/60 dark:border-red-900/40">
                                <p class="text-[9px] font-bold text-red-500 uppercase tracking-wider">Urgents</p>
                                <p class="text-lg font-extrabold text-red-700 dark:text-red-400 mt-0.5"><?php echo e($customerStats['urgent']); ?></p>
                            </div>
                            <div class="rounded-xl bg-amber-50 dark:bg-amber-950/20 p-2.5 border border-amber-100/60 dark:border-amber-900/40">
                                <p class="text-[9px] font-bold text-amber-700 uppercase tracking-wider">Escalades</p>
                                <p class="text-lg font-extrabold text-amber-800 dark:text-amber-400 mt-0.5"><?php echo e($customerStats['escalated']); ?></p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex-1 overflow-y-auto">
                        <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Tickets du client</h4>
                        <div class="space-y-2.5 max-h-60 overflow-y-auto">
                            <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850/60 p-3 shadow-sm relative group/item">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">#<?php echo e($t->id); ?></p>
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-extrabold tracking-wide uppercase 
                                              <?php echo e($t->is_urgent ? 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-450'); ?>">
                                            <?php echo e($t->priority); ?>

                                        </span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 mt-1 line-clamp-2"><?php echo e($t->title); ?></p>
                                    
                                    <div class="mt-2 flex items-center justify-between text-[10px] text-slate-500">
                                        <span>Statut: <strong class="text-slate-700 dark:text-slate-350"><?php echo e($t->status); ?></strong></span>
                                        <span><?php echo e($t->created_at->format('d/m/Y')); ?></span>
                                    </div>
                                    
                                    
                                    <div class="mt-2.5 pt-2 border-t border-slate-200 dark:border-slate-700 flex gap-2">
                                        <a href="<?php echo e(route('admin.ui.tickets.show', $t)); ?>" class="flex-1 text-center bg-slate-950 dark:bg-slate-100 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 font-bold py-1 rounded-lg text-[10px] shadow-sm">
                                            Ouvrir le ticket
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-xs text-slate-400 font-medium">Aucun ticket associé.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex flex-1 items-center justify-center p-6 text-xs text-slate-450 font-bold">Sélectionnez un client</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const API_PREFIX = '<?php echo e(url('admin')); ?>';

function escHtml(v) {
    return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtDt(v) {
    const d = v ? new Date(v) : new Date();
    return d.toLocaleString('fr-FR',{day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
}
function bubbleCls(sender) {
    if (sender==='support'||sender==='agent') return 'bg-amber-600 text-white rounded-2xl rounded-tr-none';
    if (sender==='ai'||sender==='bot')        return 'bg-violet-600 text-white rounded-2xl rounded-tl-none';
    if (sender==='user')                      return 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl rounded-tl-none';
    return 'bg-slate-100 dark:bg-slate-750 text-slate-600 dark:text-slate-350 rounded-2xl';
}

let currentUserId   = <?php echo e($selectedUser?->id ?? 'null'); ?>;
let currentConvId   = <?php echo e($selectedConversation?->id ?? 'null'); ?>;
let pollingInterval = null;

async function loadUserPanel(userId) {
    if (currentUserId === userId && !document.getElementById('chatPanel').classList.contains('hidden')) {
        document.getElementById('chatPanel').scrollIntoView({behavior:'smooth', block:'nearest'});
        return;
    }

    currentUserId = userId;
    currentConvId = null;

    document.querySelectorAll('.user-card').forEach(c => {
        const active = parseInt(c.dataset.userId) === userId;
        c.classList.toggle('border-amber-500', active);
        c.classList.toggle('bg-amber-500/5', active);
        c.classList.toggle('dark:bg-amber-500/10', active);
        c.classList.toggle('ring-2', active);
        c.classList.toggle('ring-amber-500/20', active);
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
    }
}

async function loadConversation(convId) {
    currentConvId = convId;

    document.querySelectorAll('.conv-item').forEach(el => {
        const active = parseInt(el.dataset.convId) === convId;
        el.classList.toggle('bg-amber-500/5', active);
        el.classList.toggle('dark:bg-amber-500/10', active);
        el.classList.toggle('border-amber-500', active);
        el.classList.toggle('border-transparent', !active);
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
    }
}

async function pollNewMessages(convId) {
    if (convId !== currentConvId) return;
    const chatBox = document.getElementById('chatBox');
    if (!chatBox) return;

    const lastId = parseInt(chatBox.dataset.lastMessageId ?? 0);
    try {
        const res  = await fetch(`${API_PREFIX}/support/api/conversation/${convId}`, {headers:{'Accept':'application/json'}});
        if (!res.ok) return;
        const data = await res.json();
        const incoming = (data.messages ?? []).filter(m => parseInt(m.id) > lastId);
        incoming.forEach(m => appendMessage(m, chatBox));
    } catch(e) {}
}

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
    const label   = sender === 'user' ? userName : (isAi ? 'IA Assistant' : 'Support');

    const wrap = document.createElement('div');
    wrap.className = `flex items-end gap-2.5 ${isRight ? 'justify-end' : 'justify-start'}`;
    wrap.dataset.messageId = m.id;

    let attachmentHtml = '';
    if (m.image_path) {
        attachmentHtml = `
            <div class="mt-2 pt-2 border-t border-black/10 dark:border-white/10">
                <a href="${m.image_path}" target="_blank" class="block rounded-lg overflow-hidden border border-black/5 dark:border-white/5 bg-slate-100 dark:bg-slate-900 group">
                    <img src="${m.image_path}" alt="Pièce jointe" class="max-h-48 w-full object-cover group-hover:opacity-90 transition">
                    <span class="block px-2.5 py-1.5 text-[10px] font-semibold text-slate-500 dark:text-slate-450 bg-slate-50 dark:bg-slate-800">Afficher la pièce jointe</span>
                </a>
            </div>
        `;
    }

    wrap.innerHTML = `
        ${!isRight ? `<div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-350">${escHtml(label.charAt(0).toUpperCase())}</div>` : ''}
        <div class="max-w-[75%] space-y-1">
            <div class="flex items-center gap-2 px-1 ${isRight ? 'justify-end' : ''}">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">${escHtml(label)}</span>
                <span class="text-[9px] font-semibold text-slate-400">${fmtDt(m.created_at)}</span>
            </div>
            <div class="px-4 py-3 text-sm leading-relaxed shadow-sm ${bubbleCls(sender)} relative">
                <p class="whitespace-pre-line">${escHtml(m.content || m.response || '')}</p>
                ${attachmentHtml}
            </div>
        </div>
        ${isRight ? `<div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-slate-950">SP</div>` : ''}
    `;

    box.appendChild(wrap);
    box.dataset.lastMessageId = m.id;
    scrollChatToBottom();
}

function renderConvList(conversations, activeId) {
    const list = document.getElementById('convList');
    if (!list) return;

    if (!conversations.length) {
        list.innerHTML = '<div class="p-5 text-xs font-semibold text-slate-450 text-center">Aucune conversation</div>';
        return;
    }

    list.innerHTML = conversations.map(conv => {
        const isActive = conv.id === activeId;
        return `<button type="button" onclick="loadConversation(${conv.id})"
            class="conv-item w-full text-left block p-3.5 transition hover:bg-slate-50 dark:hover:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800/60 border-l-4 ${isActive ? 'bg-amber-500/5 dark:bg-amber-500/10 border-amber-500' : 'border-transparent'}"
            data-conv-id="${conv.id}">
            <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">${escHtml(conv.title || `Conversation #${conv.id}`)}</p>
            <p class="mt-1 text-[10px] text-slate-450 font-semibold">${conv.updated_at ? fmtDt(conv.updated_at) : ''}</p>
            <p class="mt-1 text-xs text-slate-500 line-clamp-1 font-medium">${escHtml(conv.latest_message ?? '')}</p>
            <div class="mt-2 flex gap-1 items-center">
                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[9px] font-semibold text-slate-550">${conv.messages_count ?? 0} msg</span>
                ${conv.has_ticket ? '<span class="rounded-full bg-emerald-100 dark:bg-emerald-950 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-300">Ticket</span>' : ''}
            </div>
        </button>`;
    }).join('');
}

function renderSidebar(user, stats, lastTicket) {
    const container = document.getElementById('sidebar');
    if (!container) return;

    // We will render user tickets dynamically
    fetchTicketsForSidebar(user.id);
}

async function fetchTicketsForSidebar(userId) {
    try {
        const response = await fetch(`${API_PREFIX}/support/api/user-panel/${userId}`, {
            headers: {'Accept':'application/json','X-CSRF-TOKEN': CSRF}
        });
        const data = await response.json();
        
        const container = document.getElementById('sidebar');
        if (!container) return;

        let ticketsHtml = '';
        if (data.stats.tickets > 0) {
            // Retrieve actual tickets list from conversation panel if possible, otherwise render a generic list
            ticketsHtml = `
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex-1 overflow-y-auto">
                    <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Tickets du client</h4>
                    <div class="space-y-2.5 max-h-60 overflow-y-auto">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850/60 p-3 shadow-sm">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-250">Total tickets: ${data.stats.tickets}</p>
                            <p class="text-[11px] font-medium text-slate-550 dark:text-slate-450 mt-1">Gérer les détails et états depuis l'onglet Tickets.</p>
                            <a href="/admin/tickets" class="block w-full text-center bg-slate-950 dark:bg-slate-100 hover:bg-slate-850 dark:hover:bg-white text-white dark:text-slate-950 font-bold py-1 rounded-lg text-[10px] shadow-sm mt-2">
                                Gérer les tickets
                            </a>
                        </div>
                    </div>
                </div>
            `;
        } else {
            ticketsHtml = `
                <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                    <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Tickets du client</h4>
                    <p class="text-xs text-slate-400 font-medium">Aucun ticket associé.</p>
                </div>
            `;
        }

        container.innerHTML = `
            <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Informations client</h4>
                <dl class="space-y-2.5 text-xs">
                    <div>
                        <dt class="text-slate-450 font-semibold mb-0.5">Nom</dt>
                        <dd class="font-bold text-slate-800 dark:text-slate-250 truncate">${escHtml(data.user.name)}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-450 font-semibold mb-0.5">Email</dt>
                        <dd class="font-bold text-amber-600 dark:text-amber-400 truncate">${escHtml(data.user.email)}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-450 font-semibold mb-0.5">Département / Rôle</dt>
                        <dd class="font-bold text-slate-800 dark:text-slate-250 truncate">Client</dd>
                    </div>
                </dl>
            </div>

            <div class="border-b border-slate-200 dark:border-slate-800 p-4">
                <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-450 dark:text-slate-500">Statistiques</h4>
                <div class="grid grid-cols-2 gap-2">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2.5 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Discussions</p>
                        <p class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">${data.stats.conversations}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-850 p-2.5 border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tickets</p>
                        <p class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">${data.stats.tickets}</p>
                    </div>
                    <div class="rounded-xl bg-red-50 dark:bg-red-950/20 p-2.5 border border-red-100/60 dark:border-red-900/40">
                        <p class="text-[9px] font-bold text-red-500 uppercase tracking-wider">Urgents</p>
                        <p class="text-lg font-extrabold text-red-700 dark:text-red-400 mt-0.5">${data.stats.urgent}</p>
                    </div>
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-950/20 p-2.5 border border-amber-100/60 dark:border-amber-900/40">
                        <p class="text-[9px] font-bold text-amber-700 uppercase tracking-wider">Escalades</p>
                        <p class="text-lg font-extrabold text-amber-850 dark:text-amber-400 mt-0.5">${data.stats.escalated}</p>
                    </div>
                </div>
            </div>

            ${ticketsHtml}
        `;
    } catch (e) {
        console.error('fetchTicketsForSidebar error', e);
    }
}

function showChatState(state) {
    const loading = document.getElementById('chatLoading');
    const empty   = document.getElementById('chatEmpty');
    const content = document.getElementById('chatContent');

    if (!loading || !empty || !content) return;

    loading.classList.toggle('hidden', state !== 'loading');
    empty.classList.toggle('hidden', state !== 'empty');
    content.classList.toggle('hidden', state !== 'content');
}

function closeChatPanel() {
    document.getElementById('chatPanel').classList.add('hidden');
    if (pollingInterval) clearInterval(pollingInterval);
    currentConvId = null;
    currentUserId = null;
}

function updateReplyInputs(userId, convId) {
    const replyUserId = document.getElementById('replyUserId');
    const replyConvId = document.getElementById('replyConvId');
    if (replyUserId) replyUserId.value = userId ?? '';
    if (replyConvId) replyConvId.value = convId ?? '';
}

function scrollChatToBottom() {
    const box = document.getElementById('chatBox');
    if (box) box.scrollTop = box.scrollHeight;
}

window.onload = function() {
    if (currentUserId) {
        // user index has selectedUser by default
        loadUserPanel(currentUserId);
    }
};
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/admin/conversations/index.blade.php ENDPATH**/ ?>
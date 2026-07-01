<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConversationController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ConversationController as AdminConversationController;
use App\Http\Controllers\Admin\KnowledgeBaseController as AdminKnowledgeController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\InternalCommunicationController as AdminInternalCommunicationController;

use App\Http\Controllers\ItSupportEquipe\DashboardController as SupportITDashboardController;
use App\Http\Controllers\ItSupportEquipe\SupportTicketController;

use App\Http\Controllers\SupportUI\DashboardController as SupportUIDashboardController;
use App\Http\Controllers\SupportUI\TicketController as SupportUITicketController;
use App\Http\Controllers\SupportUI\ConversationController as SupportUIConversationController;
use App\Http\Controllers\SupportUI\KnowledgeBaseController as SupportUIKnowledgeController;
use App\Http\Controllers\SupportUI\NotificationController as SupportUINotificationController;
use App\Http\Controllers\SupportUI\SettingsController as SupportUISettingsController;
use App\Http\Controllers\SupportUI\InternalCommunicationController as SupportUIInternalCommunicationController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// =====================================================
// HOME
// =====================================================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('client.dashboard');
    }
    return redirect('/login');
});

// =====================================================
// CHAT
// =====================================================
Route::get('/chat', function () {
    return view('chat');
})->middleware('auth')->name('chat.index');

// =====================================================
// CHATBOT WEBHOOK
// =====================================================
Route::post('/webhook/support', [SupportController::class, 'handle'])
    ->middleware('auth');

// =====================================================
// DEFAULT DASHBOARD - Redirige vers le nouvel espace client
// =====================================================
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Admin → Admin Dashboard
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    // Support → Support Dashboard
    if ($user->isSupport()) {
        return redirect()->route('support.dashboard');
    }

    // Client → Nouvel espace client
    return redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// =====================================================
// PROFILE
// =====================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================================================
// ADMIN
// =====================================================
Route::middleware(['auth', 'role:admin', 'redirect.client'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread-count', [AdminNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.readAll');
        Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [AdminNotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

        // Monitoring
        Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('ui.monitoring.index');
        Route::get('/monitoring/{service}/details', [AdminMonitoringController::class, 'getServiceDetails'])->name('ui.monitoring.details');
        Route::post('/monitoring/{service}/settings', [AdminMonitoringController::class, 'updateServiceSettings'])->name('ui.monitoring.settings');
        Route::post('/integrations/test/{service}', [AdminMonitoringController::class, 'test'])->name('ui.integrations.test');

        // Tickets
        Route::get('/tickets', [AdminTicketController::class, 'index'])->name('ui.tickets.index');
        Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('ui.tickets.show');
        Route::post('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('ui.tickets.updateStatus');
        Route::post('/tickets/{ticket}/escalate', [AdminTicketController::class, 'escalate'])->name('ui.tickets.escalate');
        Route::post('/tickets/{ticket}/reassign', [AdminTicketController::class, 'reassign'])->name('ui.tickets.reassign');
        Route::get('/tickets/{ticket}/history', [AdminTicketController::class, 'history'])->name('ui.tickets.history');
        Route::get('/tickets/export/csv', [AdminTicketController::class, 'exportCsv'])->name('ui.tickets.export.csv');
        Route::get('/tickets/export/excel', [AdminTicketController::class, 'exportExcel'])->name('ui.tickets.export.excel');

        // Escalations
        Route::get('/escalations', [\App\Http\Controllers\Admin\EscalatedTicketsController::class, 'index'])->name('escalations.index');

        // Conversations
        Route::get('/conversations', [AdminConversationController::class, 'index'])->name('ui.conversations.index');
        Route::post('/conversations/send', [AdminConversationController::class, 'send'])->name('ui.conversations.send');
        Route::post('/conversations/tickets', [AdminConversationController::class, 'createTicket'])->name('ui.conversations.tickets.create');
        Route::post('/conversations/tickets/{ticket}/escalate', [AdminConversationController::class, 'escalate'])->name('ui.conversations.tickets.escalate');
        Route::get('/support/api/user-panel/{userId}', [\App\Http\Controllers\SupportController::class, 'userPanel'])->name('ui.api.userPanel');
        Route::get('/support/api/conversation/{convId}', [\App\Http\Controllers\SupportController::class, 'conversationPanel'])->name('ui.api.conversationPanel');

        // Knowledge base
        Route::get('/knowledge', [AdminKnowledgeController::class, 'index'])->name('ui.knowledge.index');
        Route::post('/knowledge', [AdminKnowledgeController::class, 'store'])->name('ui.knowledge.store');
        Route::get('/knowledge/{item}', [AdminKnowledgeController::class, 'show'])->name('ui.knowledge.show');
        Route::put('/knowledge/{item}', [AdminKnowledgeController::class, 'update'])->name('ui.knowledge.update');
        Route::delete('/knowledge/{item}', [AdminKnowledgeController::class, 'destroy'])->name('ui.knowledge.destroy');

        // Knowledge base : page dédiée auteur
        Route::get('/knowledge-base/authors/{user}', [AdminKnowledgeController::class, 'showAuthor'])->name('ui.knowledge.author');

        // Users
        Route::get('/users', [AdminDashboardController::class, 'ListeUsers'])->name('ui.users.index');
        Route::post('/users/store', [AdminDashboardController::class, 'EnregistrerUser'])->name('ui.users.store');
        Route::put('/users/modifier/{id}', [AdminDashboardController::class, 'ModifierUser'])->name('ui.users.update');
        Route::post('/tickets/{ticket}/assign-to-me', [AdminTicketController::class, 'assignToMe'])->name('ui.tickets.assignToMe');
        Route::get('/users/{user}/permissions', [AdminDashboardController::class, 'getUserPermissions'])->name('ui.users.getPermissions');
        Route::put('/users/{user}/permissions', [AdminDashboardController::class, 'updatePermissions'])->name('ui.users.updatePermissions');
        Route::delete('/users/{user}/delete', [AdminDashboardController::class, 'SupprimerUser'])->name('ui.users.delete');

        // Roles & Permissions
        Route::get('/roles&permissions', [AdminDashboardController::class, 'listeRole'])->name('ui.roles');
        Route::post('/permissions', [AdminDashboardController::class, 'EnregistrerPermission'])->name('ui.permissions.store');
        Route::put('/permissions/{permission}', [AdminDashboardController::class, 'ModifierPermission'])->name('ui.permissions.update');
        Route::delete('/permissions/{permission}', [AdminDashboardController::class, 'SupprimerPermission'])->name('ui.permission.destroy');
        Route::post('/roles', [AdminDashboardController::class, 'EnregistrerRole'])->name('ui.roles.store');
        Route::put('/roles/{role}', [AdminDashboardController::class, 'ModifierRole'])->name('ui.roles.update');
        Route::delete('/roles/{role}', [AdminDashboardController::class, 'SupprimerRole'])->name('ui.roles.destroy');

        // Settings
        Route::prefix('settings')->name('ui.settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::put('/profile', [AdminSettingsController::class, 'updateProfile'])->name('profile.update');
            Route::put('/password', [AdminSettingsController::class, 'updatePassword'])->name('password.update');
            Route::put('/appearance', [AdminSettingsController::class, 'updateAppearance'])->name('appearance.update');
            Route::put('/integrations', [AdminSettingsController::class, 'updateIntegrations'])->name('integrations.update');
            Route::get('/logs/download', [AdminSettingsController::class, 'download_log_file'])->name('logs.download');
        });

        // AI Responses
        Route::get('/ai_responses', [AdminDashboardController::class, 'ListeRespAi'])->name('respAi.index');
        Route::post('/ai_responses/store', [AdminDashboardController::class, 'EnregistrerRespAi'])->name('respAi.store');
        Route::put('/ai_responses/modifier/{id}', [AdminDashboardController::class, 'ModifierRAI'])->name('respAi.update');
        Route::delete('/ai_responses/{id}/delete', [AdminDashboardController::class, 'SupprimerRai'])->name('respAi.delete');

        // Logs
        Route::get('/logs', [LogController::class, 'index'])->name('logs');
        Route::get('/logs/download', [LogController::class, 'download'])->name('logs.download');

        // Internal Communication
        Route::get('/internal-communication', [AdminInternalCommunicationController::class, 'index'])->name('ui.internal.index');
        Route::post('/internal-communication/send', [AdminInternalCommunicationController::class, 'send'])->name('ui.internal.send');
        Route::get('/internal-communication/api/messages/{userId}', [AdminInternalCommunicationController::class, 'apiMessages'])->name('ui.internal.api.messages');
        Route::get('/internal-communication/api/team', [AdminInternalCommunicationController::class, 'apiTeam'])->name('ui.internal.api.team');
        Route::post('/internal-communication/api/mark-read/{userId}', [AdminInternalCommunicationController::class, 'apiMarkRead'])->name('ui.internal.api.markRead');
        Route::get('/internal-communication/api/unread-count', [AdminInternalCommunicationController::class, 'apiUnreadCount'])->name('ui.internal.api.unreadCount');

        // Support Teams
        Route::prefix('support-teams')->name('support-teams.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SupportTeamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\SupportTeamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SupportTeamController::class, 'store'])->name('store');
            Route::get('/{supportTeam}/edit', [\App\Http\Controllers\Admin\SupportTeamController::class, 'edit'])->name('edit');
            Route::put('/{supportTeam}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'update'])->name('update');
            Route::delete('/{supportTeam}', [\App\Http\Controllers\Admin\SupportTeamController::class, 'destroy'])->name('destroy');
            Route::get('/reporting', [\App\Http\Controllers\Admin\SupportTeamController::class, 'reporting'])->name('reporting');
        });
});

// =====================================================
// SUPPORT TEAM
// =====================================================
Route::middleware(['auth', 'role:support|support_n1|support_n2|support_n3', 'redirect.client'])
    ->prefix('equipeIT')
    ->name('support.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [SupportITDashboardController::class, 'index'])->name('dashboard');

        // Tickets
        Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
        Route::put('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.status');

        // Conversations
        Route::get('/conversations', [SupportITDashboardController::class, 'listeConversations'])->name('discussions.index');

        // Notifications
        Route::get('/notifications', [SupportUINotificationController::class, 'index'])->name('notifications');


        // New Support UI pages
        Route::prefix('ui')->group(function () {
            Route::get('/dashboard', [SupportUIDashboardController::class, 'index'])->name('ui.dashboard');
            Route::get('/tickets', [SupportUITicketController::class, 'index'])->name('ui.tickets.index');
            Route::get('/tickets/{ticket}', [SupportUITicketController::class, 'show'])->name('ui.tickets.show');
            Route::post('/tickets/{ticket}/status', [SupportUITicketController::class, 'updateStatus'])->name('ui.tickets.updateStatus');
            Route::post('/tickets/{ticket}/assign-to-me', [SupportUITicketController::class, 'assignToMe'])->name('ui.tickets.assignToMe');
            Route::post('/tickets/{ticket}/escalate', [SupportUITicketController::class, 'escalate'])->name('ui.tickets.escalate');
            Route::post('/tickets/{ticket}/reassign', [SupportUITicketController::class, 'reassign'])->name('ui.tickets.reassign');
            Route::get('/conversations', [SupportUIConversationController::class, 'index'])->name('ui.conversations.index');
            Route::get('/conversations/{conversation}', [SupportUIConversationController::class, 'show'])->name('ui.conversations.show');
            Route::post('/conversations/send', [SupportUIConversationController::class, 'store'])->name('ui.conversations.send');
            Route::post('/conversations/tickets', [SupportUIConversationController::class, 'createTicket'])->name('ui.conversations.tickets.create');
            Route::post('/conversations/tickets/{ticket}/assign', [SupportUIConversationController::class, 'assignTicket'])->name('ui.conversations.tickets.assign');
            Route::post('/conversations/tickets/{ticket}/escalate', [SupportUIConversationController::class, 'escalateTicket'])->name('ui.conversations.tickets.escalate');
            Route::get('/knowledge', [SupportUIKnowledgeController::class, 'index'])->name('ui.knowledge.index');
            Route::get('/knowledge/create', [SupportUIKnowledgeController::class, 'create'])->name('ui.knowledge.create');
            Route::post('/knowledge', [SupportUIKnowledgeController::class, 'store'])->name('ui.knowledge.store');
            Route::get('/knowledge/{item}', [SupportUIKnowledgeController::class, 'show'])->name('ui.knowledge.show');
            Route::get('/knowledge/{item}/edit', [SupportUIKnowledgeController::class, 'edit'])->name('ui.knowledge.edit');
            Route::put('/knowledge/{item}', [SupportUIKnowledgeController::class, 'update'])->name('ui.knowledge.update');
            Route::delete('/knowledge/{item}', [SupportUIKnowledgeController::class, 'destroy'])->name('ui.knowledge.destroy');
            Route::get('/knowledge-base/authors/{user}', [SupportUIKnowledgeController::class, 'showAuthor'])->name('ui.knowledge.author');
            // DISABLED: Escalated Tickets page merged into single Tickets page
            // Route::get('/escalations', [SupportUITicketController::class, 'escalatedIndex'])->name('ui.escalations.index');
            Route::get('/notifications', [SupportUINotificationController::class, 'index'])->name('ui.notifications.index');
            Route::get('/notifications/unread-count', [SupportUINotificationController::class, 'unreadCount'])->name('ui.notifications.unreadCount');
            Route::post('/notifications/{id}/read', [SupportUINotificationController::class, 'markRead'])->name('ui.notifications.read');
            Route::post('/notifications/read-all', [SupportUINotificationController::class, 'markAllRead'])->name('ui.notifications.readAll');
            Route::delete('/notifications/{id}', [SupportUINotificationController::class, 'destroy'])->name('ui.notifications.destroy');
            Route::delete('/notifications', [SupportUINotificationController::class, 'destroyAll'])->name('ui.notifications.destroyAll');
            Route::get('/settings', [SupportUISettingsController::class, 'index'])->name('ui.settings.index');
            Route::put('/settings/profile', [SupportUISettingsController::class, 'updateProfile'])->name('ui.settings.profile.update');
            Route::put('/settings/password', [SupportUISettingsController::class, 'updatePassword'])->name('ui.settings.password.update');

            // Internal Communication (Support → Admin)
            Route::get('/internal-communication', [SupportUIInternalCommunicationController::class, 'index'])->name('ui.internal.index');
            Route::post('/internal-communication/send', [SupportUIInternalCommunicationController::class, 'send'])->name('ui.internal.send');
            Route::get('/internal-communication/api/messages/{userId}', [SupportUIInternalCommunicationController::class, 'apiMessages'])->name('ui.internal.api.messages');
            Route::get('/internal-communication/api/team', [SupportUIInternalCommunicationController::class, 'apiTeam'])->name('ui.internal.api.team');
            Route::post('/internal-communication/api/mark-read/{userId}', [SupportUIInternalCommunicationController::class, 'apiMarkRead'])->name('ui.internal.api.markRead');
            Route::get('/internal-communication/api/unread-count', [SupportUIInternalCommunicationController::class, 'apiUnreadCount'])->name('ui.internal.api.unreadCount');
        });

        // Lightweight AJAX endpoints used by the support UI JavaScript
        Route::get('/support/api/user-panel/{userId}', [\App\Http\Controllers\SupportController::class, 'userPanel'])->name('ui.api.userPanel');
        Route::get('/support/api/conversation/{convId}', [\App\Http\Controllers\SupportController::class, 'conversationPanel'])->name('ui.api.conversationPanel');
});

// =====================================================
// CONVERSATION & MESSAGE MANAGEMENT (AJAX)
// =====================================================
Route::middleware(['auth'])->group(function () {
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
    Route::delete('/messages/{message}', [ConversationController::class, 'deleteMessage'])->name('messages.destroy');
    Route::put('/messages/{message}', [ConversationController::class, 'updateMessage'])->name('messages.update');
});

// =====================================================
// SUPPORT N1 - GESTION DES TICKETS NIVEAU 1
// =====================================================
Route::middleware(['auth', 'role:support_n1|support_n2|admin'])
    ->prefix('support-n1')
    ->name('support-n1.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SupportN1Controller::class, 'dashboard'])->name('dashboard');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\SupportN1Controller::class, 'show'])->name('show');
        Route::post('/tickets/{ticket}/status', [\App\Http\Controllers\SupportN1Controller::class, 'updateStatus'])->name('update-status');
        Route::post('/tickets/{ticket}/escalate', [\App\Http\Controllers\SupportN1Controller::class, 'escalate'])->name('escalate');
    });

// =====================================================
// SUPPORT N2 - GESTION DES TICKETS NIVEAU 2
// =====================================================
Route::middleware(['auth', 'role:support_n2|admin'])
    ->prefix('support-n2')
    ->name('support-n2.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SupportN2Controller::class, 'dashboard'])->name('dashboard');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\SupportN2Controller::class, 'show'])->name('show');
        Route::post('/tickets/{ticket}/status', [\App\Http\Controllers\SupportN2Controller::class, 'updateStatus'])->name('update-status');
        Route::post('/tickets/{ticket}/return-to-n1', [\App\Http\Controllers\SupportN2Controller::class, 'returnToN1'])->name('return-to-n1');
    });

// =====================================================
// CLIENT ESPACE (accessible par tous les utilisateurs authentifiés)
// =====================================================
Route::middleware(['auth'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');

        // (Chat uses the existing /chat route - route('chat.index'))

        // Profile
        Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
        Route::patch('/profile', [ClientController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ClientController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/photo', [ClientController::class, 'updatePhoto'])->name('profile.photo');

        // Dark mode toggle
        Route::post('/toggle-dark', [ClientController::class, 'toggleDarkMode'])->name('toggle-dark');
    });

// =====================================================
// AUTH
// =====================================================
require __DIR__.'/auth.php';

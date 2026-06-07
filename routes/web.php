<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController;

use App\Http\Controllers\ItSupportEquipe\DashboardController as SupportITDashboardController;
use App\Http\Controllers\ItSupportEquipe\SupportTicketController;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportUI\DashboardController as SupportUIDashboardController;
use App\Http\Controllers\SupportUI\TicketController as SupportUITicketController;
use App\Http\Controllers\SupportUI\ConversationController as SupportUIConversationController;
use App\Http\Controllers\SupportUI\KnowledgeBaseController as SupportUIKnowledgeController;
use App\Http\Controllers\SupportUI\NotificationController as SupportUINotificationController;
use App\Http\Controllers\SupportUI\SettingsController as SupportUISettingsController;

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
        return redirect('/chat');
    }

    return redirect('/login');

});

// =====================================================
// CHAT
// =====================================================
Route::get('/chat', function () {

    return view('chat');

})->middleware('auth');

// =====================================================
// CHATBOT WEBHOOK
// =====================================================
Route::post('/webhook/support', [SupportController::class, 'handle'])
    ->middleware('auth');

// =====================================================
// DEFAULT DASHBOARD
// =====================================================
Route::get('/dashboard', function () {

    return view('dashboard');

})->middleware(['auth', 'verified'])
  ->name('dashboard');

// =====================================================
// PROFILE
// =====================================================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

// =====================================================
// ADMIN
// =====================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications');

        Route::get('/roles&permissions', [AdminDashboardController::class, 'listeRole'])
            ->name('role_permissions');

        // ==========================
        // USERS
        // ==========================

        Route::get('/users', [AdminDashboardController::class, 'ListeUsers'])
            ->name('users.index');

        Route::post('/users/store', [AdminDashboardController::class, 'EnregistrerUser'])
            ->name('users.store');

        Route::put('/users/modifier/{id}', [AdminDashboardController::class, 'ModifierUser'])
            ->name('users.update');

        Route::delete('/users/{user}/delete', [AdminDashboardController::class, 'SupprimerUser'])
            ->name('users.delete');

        // ==========================
        // KNOWLEDGE BASE
        // ==========================

        Route::get('/knowledge_base', [AdminDashboardController::class, 'ListeBase'])
            ->name('base.index');

        Route::post('/knowledge_base/store', [AdminDashboardController::class, 'EnregistrerBase'])
            ->name('base.store');

        Route::put('/knowledge_base/modifier/{id}', [AdminDashboardController::class, 'ModifierBase'])
            ->name('base.update');

        Route::delete('/knowledge_base/{id}/delete', [AdminDashboardController::class, 'SupprimerBase'])
            ->name('base.delete');

        // ==========================
        // AI RESPONSES
        // ==========================

        Route::get('/ai_responses', [AdminDashboardController::class, 'ListeBase'])
            ->name('respAi.index');

        Route::post('/ai_responses/store', [AdminDashboardController::class, 'EnregistrerBase'])
            ->name('respAi.store');

        Route::put('/ai_responses/modifier/{id}', [AdminDashboardController::class, 'ModifierBase'])
            ->name('respAi.update');

        Route::delete('/ai_responses/{id}/delete', [AdminDashboardController::class, 'SupprimerBase'])
            ->name('respAi.delete');

        // ==========================
        // LOGS
        // ==========================

        Route::get('/logs', [LogController::class, 'index'])
            ->name('logs');

        Route::get('/logs/download', [LogController::class, 'download'])
            ->name('logs.download');

        // ==========================
        // SETTINGS
        // ==========================

        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {

                Route::get('/', [SettingsController::class, 'index'])
                    ->name('index');

                Route::put('/general', [SettingsController::class, 'updateGeneral'])
                    ->name('general.update');

                Route::put('/integration', [SettingsController::class, 'updateIntegration'])
                    ->name('integration.update');
            });

});

// =====================================================
// SUPPORT TEAM
// =====================================================
Route::middleware(['auth', 'role:support'])
    ->prefix('equipeIT')
    ->name('support.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SupportITDashboardController::class, 'index'])
            ->name('dashboard');

        // Tickets
        Route::get('/tickets', [SupportTicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])
            ->name('tickets.show');

        Route::put('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])
            ->name('tickets.status');

        // Conversations
        Route::get('/conversations', [SupportITDashboardController::class, 'listeConversations'])
            ->name('discussions.index');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications');

        // Settings
        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {

                Route::get('/', [SettingsController::class, 'index'])
                    ->name('index');

                Route::put('/general', [SettingsController::class, 'updateGeneral'])
                    ->name('general.update');

                Route::put('/integration', [SettingsController::class, 'updateIntegration'])
                    ->name('integration.update');
                
            });

        // New Support UI pages
        Route::prefix('ui')->group(function () {
            Route::get('/dashboard', [SupportUIDashboardController::class, 'index'])->name('ui.dashboard');
            Route::get('/tickets', [SupportUITicketController::class, 'index'])->name('ui.tickets.index');
            Route::get('/tickets/{ticket}', [SupportUITicketController::class, 'show'])->name('ui.tickets.show');
            Route::post('/tickets/{ticket}/status', [SupportUITicketController::class, 'updateStatus'])->name('ui.tickets.updateStatus');
            Route::post('/tickets/{ticket}/assign-to-me', [SupportUITicketController::class, 'assignToMe'])->name('ui.tickets.assignToMe');
            Route::get('/conversations', [SupportUIConversationController::class, 'index'])->name('ui.conversations.index');
            Route::get('/conversations/{conversation}', [SupportUIConversationController::class, 'show'])->name('ui.conversations.show');
            Route::post('/conversations/send', [SupportUIConversationController::class, 'store'])->name('ui.conversations.send');
            Route::post('/conversations/tickets', [SupportUIConversationController::class, 'createTicket'])->name('ui.conversations.tickets.create');
            Route::post('/conversations/tickets/{ticket}/assign', [SupportUIConversationController::class, 'assignTicket'])->name('ui.conversations.tickets.assign');
            Route::post('/conversations/tickets/{ticket}/escalate', [SupportUIConversationController::class, 'escalateTicket'])->name('ui.conversations.tickets.escalate');
            Route::get('/knowledge', [SupportUIKnowledgeController::class, 'index'])->name('ui.knowledge.index');
            Route::get('/knowledge/create', [SupportUIKnowledgeController::class, 'create'])->name('ui.knowledge.create');
            Route::post('/knowledge', [SupportUIKnowledgeController::class, 'store'])->name('ui.knowledge.store');
            Route::get('/knowledge/{item}/edit', [SupportUIKnowledgeController::class, 'edit'])->name('ui.knowledge.edit');
            Route::put('/knowledge/{item}', [SupportUIKnowledgeController::class, 'update'])->name('ui.knowledge.update');
            Route::delete('/knowledge/{item}', [SupportUIKnowledgeController::class, 'destroy'])->name('ui.knowledge.destroy');
            Route::get('/notifications', [SupportUINotificationController::class, 'index'])->name('ui.notifications.index');
            Route::get('/notifications/unread-count', [SupportUINotificationController::class, 'unreadCount'])->name('ui.notifications.unreadCount');
            Route::get('/notifications/unread-count', [SupportUINotificationController::class, 'unreadCount'])->name('ui.notifications.unreadCount');
            Route::post('/notifications/{id}/read', [SupportUINotificationController::class, 'markRead'])->name('ui.notifications.read');
            Route::post('/notifications/read-all', [SupportUINotificationController::class, 'markAllRead'])->name('ui.notifications.readAll');
            Route::delete('/notifications/{id}', [SupportUINotificationController::class, 'destroy'])->name('ui.notifications.destroy');
            Route::delete('/notifications', [SupportUINotificationController::class, 'destroyAll'])->name('ui.notifications.destroyAll');
            Route::get('/settings', [SupportUISettingsController::class, 'index'])->name('ui.settings.index');
            Route::put('/settings/profile', [SupportUISettingsController::class, 'updateProfile'])->name('ui.settings.profile.update');
            Route::put('/settings/password', [SupportUISettingsController::class, 'updatePassword'])->name('ui.settings.password.update');
        });

        // Lightweight AJAX endpoints used by the support UI JavaScript
        Route::get('/support/api/user-panel/{userId}', [\App\Http\Controllers\SupportController::class, 'userPanel'])->name('ui.api.userPanel');
        Route::get('/support/api/conversation/{convId}', [\App\Http\Controllers\SupportController::class, 'conversationPanel'])->name('ui.api.conversationPanel');
});

// =====================================================
// AUTH
// =====================================================
require __DIR__.'/auth.php';

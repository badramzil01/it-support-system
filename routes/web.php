<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ItSupportEquipe\DashboardController as SupportITDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\LogController;


/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// ===============================
// HOME
// ===============================
Route::get('/', function () {

    // ✅ si connecté → chat
    if (auth()->check()) {
        return redirect('/chat');
    }

    // ✅ sinon login
    return redirect('/login');

});

// ===============================
// CHAT PAGE
// ===============================
Route::get('/chat', function () {

    return view('chat');

})->middleware('auth');

// ===============================
// CHATBOT API
// ===============================
Route::post('/webhook/support', [SupportController::class, 'handle'])
    ->middleware('auth');

// ===============================
// DASHBOARD
// ===============================
Route::get('/dashboard', function () {

    return view('dashboard');

})->middleware(['auth', 'verified'])
  ->name('dashboard');

// ===============================
// PROFILE
// ===============================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

// ===============================
// Admin
// ===============================
Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::get('/roles&permissions', [AdminDashboardController::class, 'listeRole'])->name('role_permissions');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    
        // Utilisateurs
    Route::get('/users', [AdminDashboardController::class, 'ListeUsers'])->name('users.index');
    Route::post('/users/store', [AdminDashboardController::class, 'EnregistrerUser'])->name('users.store');
    Route::put('/users/modifier/{id}', [AdminDashboardController::class, 'ModifierUser'])->name('users.update');
    Route::delete('/users/{user}/delete', [AdminDashboardController::class, 'SupprimerUser'])->name('users.delete');
    // Knwoledge base
    Route::get('/knowledge_base', [AdminDashboardController::class, 'ListeBase'])->name('base.index');
    Route::post('/knowledge_base/store', [AdminDashboardController::class, 'EnregistrerBase'])->name('base.store');
    Route::put('/knowledge_base/modifier/{id}', [AdminDashboardController::class, 'ModifierBase'])->name('base.update');
    Route::delete('/knowledge_base/{id}/delete', [AdminDashboardController::class, 'SupprimerBase'])->name('base.delete');
    // responses AI
    Route::get('/ai_responses', [AdminDashboardController::class, 'ListeBase'])->name('respAi.index');
    Route::post('/ai_responses/store', [AdminDashboardController::class, 'EnregistrerBase'])->name('respAi.store');
    Route::put('/ai_responses/modifier/{id}', [AdminDashboardController::class, 'ModifierBase'])->name('respAi.update');
    Route::delete('/ai_responses/{id}/delete', [AdminDashboardController::class, 'SupprimerBase'])->name('respAi.delete');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/logs/download', [LogController::class, 'download'])->name('logs.download');

    Route::prefix('settings')->name('settings.')
    ->group(function () {

        Route::get('/', [SettingsController::class,'index'])->name('index');
        Route::put('/general', [SettingsController::class,'updateGeneral'])->name('general.update');
        Route::put('/integration', [SettingsController::class,'updateIntegration'])->name('integration.update');
    });
});

// ===============================
// Admin
// ===============================
Route::middleware(['auth','role:support'])
    ->prefix('equipeIT')
    ->name('support.')
    ->group(function () {

        Route::get('/dashboard', [SupportITDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tickets', [SupportITDashboardController::class, 'listeTickets'])->name('tickets.index');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::prefix('settings')->name('settings.')
        ->group(function () {

        Route::get('/', [SettingsController::class,'index'])->name('index');
        Route::put('/general', [SettingsController::class,'updateGeneral'])->name('general.update');
        Route::put('/integration', [SettingsController::class,'updateIntegration'])->name('integration.update');
        });
        // Conversations
        Route::get('/conversations', [SupportITDashboardController::class, 'listeConversations'])->name('discussions.index');
   
});


// ===============================
// AUTH ROUTES
// ===============================
require __DIR__.'/auth.php';
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;

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
// AUTH ROUTES
// ===============================
require __DIR__.'/auth.php';
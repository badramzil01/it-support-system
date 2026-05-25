<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupportController;
use App\Http\Controllers\AIResponseController;
use App\Http\Controllers\Api\TicketController;

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/


// =====================================================
// ✅ TEST API
// =====================================================
Route::get('/test', function () {

    return response()->json([

        'success' => true,

        'message' => 'API WORKING 🚀'

    ]);

});


// =====================================================
// ✅ SUPPORT CHATBOT WEBHOOK
// =====================================================
Route::post(

    '/webhook/support',

    [SupportController::class, 'handle']

);


// =====================================================
// ✅ USER FEEDBACK
// =====================================================
Route::post(

    '/ticket/feedback',

    [SupportController::class, 'feedback']

);


// =====================================================
// ✅ SAVE AI RESPONSE
// =====================================================
Route::post(

    '/ai-response',

    [AIResponseController::class, 'store']

);


// =====================================================
// ✅ CHECK IF TICKET EXISTS
// =====================================================
Route::post(

    '/check-ticket',

    [TicketController::class, 'checkTicket']

);


// =====================================================
// ✅ CREATE TICKET
// =====================================================
Route::post(

    '/create-ticket',

    [TicketController::class, 'createTicket']

);


// =====================================================
// ✅ UPDATE TICKET STATUS
// =====================================================
Route::post(

    '/update-ticket-status',

    [TicketController::class, 'updateStatus']

);


// =====================================================
// ✅ GET USER CONVERSATIONS
// =====================================================
Route::get(

    '/conversations/{user_id}',

    [SupportController::class, 'getConversations']

);


// =====================================================
// ✅ GET CHAT MESSAGES
// =====================================================
Route::get(

    '/messages/{conversation_id}',

    [SupportController::class, 'getMessages']

);


// =====================================================
// ✅ CORS PREFLIGHT
// =====================================================
Route::options('{any}', function () {

    return response()->json([], 200);

})->where('any', '.*');

// =====================================================
// ✅ save ticket to db
// =====================================================

Route::post('/tickets', [TicketController::class, 'store']);

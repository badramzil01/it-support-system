<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SupportController;
use App\Http\Controllers\AIResponseController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\RuntimeSettingsController;
use App\Http\Controllers\Api\JiraController;
use App\Http\Controllers\Admin\MonitoringController;

use App\Models\IntegrationConfig;

/*
|--------------------------------------------------------------------------
| TEST API
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API WORKING 🚀',
    ]);
});

/*
|--------------------------------------------------------------------------
| RUNTIME CONFIG (n8n)
|--------------------------------------------------------------------------
*/

Route::get(
    '/integrations/runtime-config',
    [MonitoringController::class, 'runtimeConfig']
);

Route::get(
    '/settings/runtime',
    [RuntimeSettingsController::class, 'index']
);

/*
|--------------------------------------------------------------------------
| AI CONFIG FOR N8N
|--------------------------------------------------------------------------
*/

Route::get('/ai/config', function () {

    return response()->json([

        'provider' => IntegrationConfig::where(
            'service',
            'ai'
        )->where(
            'config_key',
            'provider'
        )->value('config_value') ?? 'openrouter',

        'openrouter_api_key' => IntegrationConfig::where(
            'service',
            'openrouter'
        )->where(
            'config_key',
            'api_key'
        )->value('config_value'),

        'openrouter_model' => IntegrationConfig::where(
            'service',
            'openrouter'
        )->where(
            'config_key',
            'model'
        )->value('config_value') ?? 'google/gemma-3-27b-it',

        'gemini_api_key' => IntegrationConfig::where(
            'service',
            'gemini'
        )->where(
            'config_key',
            'api_key'
        )->value('config_value'),

        'laravel_api_url' => IntegrationConfig::where(
            'service',
            'laravel'
        )->where(
            'config_key',
            'base_url'
        )->value('config_value'),
    ]);
});

/*
|--------------------------------------------------------------------------
| SUPPORT CHATBOT
|--------------------------------------------------------------------------
*/

Route::post(
    '/webhook/support',
    [SupportController::class, 'handle']
);

Route::post(
    '/ticket/feedback',
    [SupportController::class, 'feedback']
);

Route::post(
    '/ai-response',
    [AIResponseController::class, 'store']
);

/*
|--------------------------------------------------------------------------
| TICKETS
|--------------------------------------------------------------------------
*/

Route::post(
    '/check-ticket',
    [TicketController::class, 'checkTicket']
);

Route::post(
    '/create-ticket',
    [TicketController::class, 'createTicket']
);

Route::post(
    '/update-ticket-status',
    [TicketController::class, 'updateStatus']
);

Route::post(
    '/tickets',
    [TicketController::class, 'store']
);

Route::post(
    '/tickets/{id}/jira-key',
    [SupportController::class, 'updateJiraKey']
);

/*
|--------------------------------------------------------------------------
| CONVERSATIONS
|--------------------------------------------------------------------------
*/

Route::get(
    '/conversations/{user_id}',
    [SupportController::class, 'getConversations']
);

Route::get(
    '/messages/{conversation_id}',
    [SupportController::class, 'getMessages']
);

/*
|--------------------------------------------------------------------------
| JIRA
|--------------------------------------------------------------------------
*/

Route::post(
    '/jira/create',
    [JiraController::class, 'create']
);

Route::get('/jira-test/{key}', function ($key) {

    return app(\App\Services\JiraService::class)
        ->getTransitions($key);

});

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
*/

Route::options('{any}', function () {

    return response()->json([], 200);

})->where('any', '.*');
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    public static function refreshConfig(): void
    {
        try {
            $url = \App\Support\SettingsManager::get('integration.refresh_config_webhook')
                ?? env('N8N_REFRESH_CONFIG_URL')
                ?? 'http://localhost:5678/webhook/refresh-config';

            Log::info('N8N Refresh Start', [
                'url' => $url,
            ]);

            $cfg = fn(string $svc, string $key, ?string $env = null) =>
                \App\Models\IntegrationConfig::getValue($svc, $key, $env);

            $laravelBaseUrl = \App\Models\IntegrationConfig::getValue('laravel', 'base_url') 
                ?? env('APP_URL') 
                ?? request()->getSchemeAndHttpHost();

            if (str_contains($laravelBaseUrl, 'localhost') || str_contains($laravelBaseUrl, '127.0.0.1')) {
                $laravelBaseUrl = request()->getSchemeAndHttpHost();
            }

            $payload = [
                'action' => 'refresh_config',
                'source' => 'laravel',
                'timestamp' => now()->toDateTimeString(),
                'config' => [
                    'laravel_api_url'     => rtrim($laravelBaseUrl, '/'),
                    'n8n_webhook_support' => \App\Models\IntegrationConfig::getValue('n8n', 'webhook_support') ?? env('N8N_WEBHOOK_URL'),
                    'n8n_webhook_ticket'  => \App\Models\IntegrationConfig::getValue('n8n', 'webhook_ticket') ?? env('N8N_WEBHOOK_TICKET'),
                    'gemini_api_key'      => $cfg('gemini', 'api_key',   'GEMINI_API_KEY'),
                    'gemini_model'        => $cfg('gemini', 'model',     'GEMINI_MODEL') ?? 'gemini-1.5-flash',
                    'openrouter_api_key'  => $cfg('openrouter', 'api_key', 'OPENROUTER_API_KEY'),
                    'openrouter_model'    => $cfg('openrouter', 'model',   'OPENROUTER_MODEL') ?? 'nvidia/nemotron-3-nano-30b-a3b:free',
                    'jira_url'            => $cfg('jira', 'url',         'JIRA_URL'),
                    'jira_token'          => $cfg('jira', 'token',       'JIRA_TOKEN'),
                    'jira_project_key'    => $cfg('jira', 'project_key', 'JIRA_PROJECT_KEY') ?? 'SUP',
                    'jira_email'          => $cfg('gmail', 'address',    'MAIL_FROM_ADDRESS'),
                    'support_email'       => $cfg('gmail', 'address',   'MAIL_FROM_ADDRESS'),
                ]
            ];

            $response = Http::timeout(5)
                ->post($url, $payload);

            Log::info('N8N Refresh Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

        } catch (\Throwable $e) {

            Log::error('N8N Refresh Error', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoie le payload de support à n8n.
     */
    public static function sendSupportWebhook(array $payload, int $timeoutSeconds = 30): array
    {
        $cfg = fn(string $svc, string $key, ?string $env = null) =>
            \App\Models\IntegrationConfig::getValue($svc, $key, $env);

        $url = $cfg('n8n', 'webhook_support', 'N8N_WEBHOOK_URL')
            ?? 'http://localhost:5678/webhook-test/support';

        $traceId = request()->header('X-Trace-Id') ?? (string) \Illuminate\Support\Str::uuid();

        // LAST-RESORT GUARD: ensure customer_email is always a non-empty string
        // in the payload we send to n8n. This protects against any caller that
        // forgot to set it.
        if (!isset($payload['customer_email']) || trim((string) $payload['customer_email']) === '') {
            $fallback = (string) (env('MAIL_FROM_ADDRESS') ?: 'client@example.com');
            try {
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $fallback = (string) \Illuminate\Support\Facades\Auth::user()->email ?: $fallback;
                }
            } catch (\Throwable $e) {
                // ignore
            }
            if ($fallback === '' || !filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
                $fallback = 'client@example.com';
            }
            $payload['customer_email'] = $fallback;
        }

        // Masquer les données base64 pour les logs
        $loggedPayload = $payload;
        if (isset($loggedPayload['image_base64']) && is_string($loggedPayload['image_base64'])) {
            $loggedPayload['image_base64'] = '[base64:' . strlen($loggedPayload['image_base64']) . ' bytes]';
        }

        Log::info('n8n.support_webhook.request', [
            'trace_id' => $traceId,
            'url'      => $url,
            'payload'  => $loggedPayload,
        ]);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout($timeoutSeconds)
                ->post($url, $payload);

            Log::info('n8n.support_webhook.response', [
                'trace_id'    => $traceId,
                'http_status' => $response->status(),
                'successful'  => $response->successful(),
                'body'        => \Illuminate\Support\Str::limit($response->body(), 8000),
            ]);

            if (!$response->successful()) {
                return [
                    'ok'      => false,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'message' => 'Le service de support IA n8n a retourné une erreur HTTP ' . $response->status(),
                ];
            }

            return [
                'ok'     => true,
                'status' => $response->status(),
                'body'   => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('n8n.support_webhook.error', [
                'trace_id' => $traceId,
                'message'  => $e->getMessage(),
            ]);

            return [
                'ok'      => false,
                'status'  => 0,
                'message' => 'Erreur de communication avec le service n8n: ' . $e->getMessage(),
            ];
        }
    }
}
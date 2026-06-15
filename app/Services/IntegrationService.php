<?php

namespace App\Services;

use App\Models\IntegrationConfig;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntegrationService
{
    // ─────────────────────────────────────────────
    // Helper: read from IntegrationConfig with env fallback
    // ─────────────────────────────────────────────
    private function cfg(string $service, string $key, ?string $envFallback = null): ?string
    {
        return IntegrationConfig::getValue($service, $key, $envFallback);
    }

    // ─────────────────────────────────────────────
    // Service Tests
    // ─────────────────────────────────────────────
    public function testOpenAI(): array
    {
        $start = microtime(true);
        try {
            $key = $this->cfg('openai', 'api_key', 'OPENAI_API_KEY');
            if (!$key) throw new \Exception('Missing API Key');
            $response = Http::withToken($key)->get('https://api.openai.com/v1/models');
            return ['success' => $response->successful(), 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'openai', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testOpenRouter(): array
    {
        $start = microtime(true);
        try {
            $key = $this->cfg('openrouter', 'api_key', 'OPENROUTER_API_KEY');
            if (!$key) throw new \Exception('Missing API Key');
            $response = Http::withToken($key)->get('https://openrouter.ai/api/v1/models');
            return ['success' => $response->successful(), 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'openrouter', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testGemini(): array
    {
        $start = microtime(true);
        try {
            $key = $this->cfg('gemini', 'api_key', 'GEMINI_API_KEY');
            if (!$key) throw new \Exception('Missing API Key');
            $response = Http::get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $key);
            return ['success' => $response->successful(), 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'gemini', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testJira(): array
    {
        $start = microtime(true);
        try {
            $url   = $this->cfg('jira', 'url', 'JIRA_URL');
            $email = $this->cfg('gmail', 'address', 'MAIL_FROM_ADDRESS');
            $token = $this->cfg('jira', 'token', 'JIRA_TOKEN');
            if (!$url || !$email || !$token) throw new \Exception('Missing Jira Config');

            $response = Http::withBasicAuth($email, $token)->get($url . '/rest/api/2/myself');
            return ['success' => $response->successful(), 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'jira', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testN8N(): array
    {
        $start = microtime(true);
        try {
            $url = $this->cfg('n8n', 'webhook_support', 'N8N_WEBHOOK_URL');
            if (!$url) throw new \Exception('Missing n8n Webhook URL');
            $response = Http::timeout(5)->get($url);
            return [
                'success' => $response->status() >= 200 && $response->status() < 500,
                'time'    => round((microtime(true) - $start) * 1000)
            ];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'n8n', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testGmail(): array
    {
        $start = microtime(true);
        try {
            $address = $this->cfg('gmail', 'address', 'MAIL_FROM_ADDRESS');
            if (!$address) throw new \Exception('Missing Gmail Address');
            return ['success' => true, 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'gmail', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    public function testLaravel(): array
    {
        return ['success' => true, 'time' => 0];
    }

    public function testMysql(): array
    {
        $start = microtime(true);
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            return ['success' => true, 'time' => round((microtime(true) - $start) * 1000)];
        } catch (\Exception $e) {
            SystemLog::create(['service' => 'mysql', 'level' => 'error', 'message' => $e->getMessage()]);
            return ['success' => false, 'time' => 0];
        }
    }

    // ─────────────────────────────────────────────
    // Synchronisation : Laravel → n8n
    // Envoie toutes les configs dynamiques vers n8n
    // via un webhook de synchronisation dédié.
    // ─────────────────────────────────────────────
    public function syncConfigToN8n(): array
    {
        try {
            $syncUrl = $this->cfg('n8n', 'webhook_config_sync', 'N8N_WEBHOOK_CONFIG_SYNC');
            if (!$syncUrl) {
                Log::warning('[Sync] n8n webhook_config_sync not configured.');
                return ['synced' => false, 'reason' => 'webhook_config_sync not set'];
            }

            $payload = [
                // Laravel
                'laravel_base_url'    => $this->cfg('laravel', 'base_url', 'APP_URL'),

                // n8n Webhooks
                'n8n_webhook_support' => $this->cfg('n8n', 'webhook_support', 'N8N_WEBHOOK_URL'),
                'n8n_webhook_ticket'  => $this->cfg('n8n', 'webhook_ticket',  'N8N_WEBHOOK_TICKET'),

                // Jira
                'jira_url'            => $this->cfg('jira', 'url',         'JIRA_URL'),
                'jira_token'          => $this->cfg('jira', 'token',       'JIRA_TOKEN'),
                'jira_project_key'    => $this->cfg('jira', 'project_key', 'JIRA_PROJECT_KEY'),
                'jira_email'          => $this->cfg('gmail', 'address',    'MAIL_FROM_ADDRESS'),

                // OpenRouter
                'openrouter_api_key'  => $this->cfg('openrouter', 'api_key', 'OPENROUTER_API_KEY'),
                'openrouter_model'    => $this->cfg('openrouter', 'model',   'OPENROUTER_MODEL'),

                // Gemini
                'gemini_api_key'      => $this->cfg('gemini', 'api_key', 'GEMINI_API_KEY'),
                'gemini_model'        => $this->cfg('gemini', 'model',   'GEMINI_MODEL'),

                // Gmail
                'support_email'       => $this->cfg('gmail', 'address',  'MAIL_FROM_ADDRESS'),
            ];

            $response = Http::timeout(10)->post($syncUrl, $payload);

            if (!$response->successful()) {
                $msg = 'n8n config-sync failed: HTTP ' . $response->status();
                SystemLog::create(['service' => 'n8n', 'level' => 'warning', 'message' => $msg]);
                return ['synced' => false, 'reason' => $msg];
            }

            Log::info('[Sync] Laravel → n8n config synced successfully.');
            return ['synced' => true];

        } catch (\Exception $e) {
            SystemLog::create(['service' => 'n8n', 'level' => 'error', 'message' => '[Sync] ' . $e->getMessage()]);
            return ['synced' => false, 'reason' => $e->getMessage()];
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    protected \App\Services\ConfigSyncService $configSyncService;

    public function __construct(\App\Services\ConfigSyncService $configSyncService)
    {
        $this->configSyncService = $configSyncService;
    }
    /**
     * GET /api/integrations/runtime-config
     * Called by n8n "Load Platform Configuration" node.
     * Returns all dynamic integration configs so n8n has zero hardcoded values.
     */
    public function runtimeConfig(Request $request): \Illuminate\Http\JsonResponse
    {
        $cfg = fn(string $svc, string $key, ?string $env = null) =>
            \App\Models\IntegrationConfig::getValue($svc, $key, $env);

        $laravelBaseUrl = \App\Models\IntegrationConfig::getValue('laravel', 'base_url') 
            ?? env('APP_URL') 
            ?? $request->getSchemeAndHttpHost();

        if (str_contains($laravelBaseUrl, 'localhost') || str_contains($laravelBaseUrl, '127.0.0.1')) {
            $laravelBaseUrl = $request->getSchemeAndHttpHost();
        }

        return response()->json([
            // Laravel
            'laravel_api_url'    => rtrim($laravelBaseUrl, '/'),

            // n8n Webhooks
            'n8n_webhook_support' => $cfg('n8n', 'webhook_support', 'http://localhost:5678/webhook/support'),
            'n8n_webhook_ticket'  => $cfg('n8n', 'webhook_ticket',  'N8N_WEBHOOK_TICKET'),

            // Gemini
            'gemini_api_key'     => $cfg('gemini', 'api_key',   'GEMINI_API_KEY'),
            'gemini_model'       => $cfg('gemini', 'model',     'GEMINI_MODEL') ?? 'gemini-1.5-flash',

            // OpenRouter
            'openrouter_api_key' => $cfg('openrouter', 'api_key', 'OPENROUTER_API_KEY'),
            'openrouter_model'   => $cfg('openrouter', 'model',   'OPENROUTER_MODEL') ?? 'nvidia/nemotron-3-nano-30b-a3b:free',

            // Jira
            'jira_url'           => $cfg('jira', 'url',         'JIRA_URL'),
            'jira_token'         => $cfg('jira', 'token',       'JIRA_TOKEN'),
            'jira_project'       => $cfg('jira', 'project_key', 'JIRA_PROJECT_KEY') ?? 'SUP',
            'jira_email'         => $cfg('gmail', 'address',    'MAIL_FROM_ADDRESS'),

            // Gmail
            'gmail_from'         => $cfg('gmail', 'address',   'MAIL_FROM_ADDRESS'),
            'gmail_to'           => $cfg('gmail', 'to_address', 'MAIL_TO_ADDRESS') ?? $cfg('gmail', 'address', 'MAIL_FROM_ADDRESS'),
            'support_email'      => $cfg('gmail', 'address',   'MAIL_FROM_ADDRESS'),
        ]);
    }
    public function index()
    {
        $metrics = \App\Models\SystemMetric::all()->keyBy('service');
        
        $serviceList = [
            'laravel' => ['name' => 'Laravel', 'icon' => 'server', 'uptime' => '99.99%', 'version' => app()->version()],
            'mysql' => ['name' => 'MySQL', 'icon' => 'database', 'uptime' => '99.95%', 'version' => '—'],
            'n8n' => ['name' => 'n8n', 'icon' => 'flow', 'uptime' => '—', 'version' => '—'],
            'jira' => ['name' => 'Jira', 'icon' => 'ticket', 'uptime' => '—', 'version' => '—'],
            'gmail' => ['name' => 'Gmail', 'icon' => 'mail', 'uptime' => '—', 'version' => '—'],
            'openrouter' => ['name' => 'OpenRouter', 'icon' => 'sparkles', 'uptime' => '—', 'version' => '—'],
            'gemini' => ['name' => 'Gemini', 'icon' => 'sparkles', 'uptime' => '—', 'version' => '—'],
        ];

        $services = [];
        foreach ($serviceList as $key => $info) {
            $metric = $metrics->get($key);
            $services[] = [
                'id' => $key,
                'name' => $info['name'],
                'icon' => $info['icon'],
                'status' => $metric ? $metric->status : 'unknown',
                'response' => $metric ? $metric->response_time . 'ms' : '—',
                'last_sync' => $metric && $metric->last_sync ? $metric->last_sync->format('d/m/Y H:i:s') : '—',
                'uptime' => $info['uptime'],
                'version' => $info['version'],
                'last_error' => null,
            ];
        }

        return view('admin.monitoring.index', compact('services'));
    }

    public function test(Request $request, $service, \App\Services\IntegrationService $integrationService)
    {
        $method = 'test' . ucfirst($service);
        if (method_exists($integrationService, $method)) {
            $result = $integrationService->$method();
            
            // Update metric
            \App\Models\SystemMetric::updateOrCreate(
                ['service' => $service],
                [
                    'status' => $result['success'] ? 'online' : 'offline',
                    'response_time' => $result['time'],
                    'last_sync' => now()
                ]
            );

            return response()->json($result);
        }
        
        // Handle laravel / mysql if they click test (though job handles it, we can return dummy or implement later)
        return response()->json(['success' => false, 'error' => 'Service introuvable']);
    }
    public function getServiceDetails($service)
    {
        // Map: service_id => list of configurable fields
        // Format: [config_key => [label, type, service, key]]
        $serviceFields = [
            'laravel' => [
                ['service' => 'laravel', 'key' => 'base_url',           'label' => 'Base URL Laravel',       'type' => 'url'],
            ],
            'n8n' => [
                ['service' => 'n8n', 'key' => 'webhook_support',         'label' => 'Webhook Support',        'type' => 'url'],
                ['service' => 'n8n', 'key' => 'webhook_ticket',          'label' => 'Webhook Ticket',         'type' => 'url'],
                ['service' => 'n8n', 'key' => 'webhook_config_sync',     'label' => 'Webhook Config Sync',   'type' => 'url'],
            ],
            'jira' => [
                ['service' => 'jira', 'key' => 'url',                    'label' => 'URL Jira',               'type' => 'url'],
                ['service' => 'jira', 'key' => 'token',                  'label' => 'Token Jira',             'type' => 'password'],
                ['service' => 'jira', 'key' => 'project_key',            'label' => 'Clé Projet Jira',        'type' => 'text'],
            ],
            'openrouter' => [
                ['service' => 'openrouter', 'key' => 'api_key',          'label' => 'Clé API OpenRouter',     'type' => 'password'],
                ['service' => 'openrouter', 'key' => 'model',            'label' => 'Modèle',                 'type' => 'text'],
            ],
            'gemini' => [
                ['service' => 'gemini', 'key' => 'api_key',              'label' => 'Clé API Gemini',         'type' => 'password'],
                ['service' => 'gemini', 'key' => 'model',                'label' => 'Modèle',                 'type' => 'text'],
            ],
            'gmail' => [
                ['service' => 'gmail', 'key' => 'address',    'label' => 'Adresse From (expéditeur)', 'type' => 'email'],
                ['service' => 'gmail', 'key' => 'to_address', 'label' => 'Adresse To (destinataire)', 'type' => 'email'],
            ],
            'mysql'   => [],
        ];

        $fieldDefs = $serviceFields[$service] ?? [];
        $data = [];
        foreach ($fieldDefs as $def) {
            $data[] = [
                'key'   => $def['service'] . '__' . $def['key'],  // Used as input name
                'label' => $def['label'],
                'type'  => $def['type'],
                'value' => \App\Models\IntegrationConfig::getValue($def['service'], $def['key']),
            ];
        }

        $lastError = \App\Models\SystemLog::where('service', $service)
                        ->where('level', 'error')
                        ->latest()
                        ->first();

        return response()->json([
            'fields'          => $data,
            'last_error'      => $lastError ? $lastError->message : null,
            'last_error_time' => $lastError ? $lastError->created_at->format('d/m/Y H:i:s') : null,
        ]);
    }

    public function updateServiceSettings(Request $request, $service, \App\Services\IntegrationService $integrationService)
    {
        // Input names are in format: "service__key" (double underscore)
        $inputs = $request->except('_token');
        foreach ($inputs as $inputName => $value) {
            // Parse "service__key" => [$svc, $key]
            if (!str_contains($inputName, '__')) continue;
            [$svc, $cfgKey] = explode('__', $inputName, 2);

            if ($value === null || $value === '') {
                $this->configSyncService->delete($svc, $cfgKey, false);
            } else {
                $this->configSyncService->save($svc, $cfgKey, $value, false);
            }
        }

        // Trigger sync to n8n once at the end
        $this->configSyncService->syncAll();

        // Test the service with new config
        return $this->test($request, $service, $integrationService);
    }
}

<?php

namespace App\Services;

use App\Models\IntegrationConfig;
use App\Models\Setting;
use App\Services\IntegrationService;
use App\Services\N8nService;
use Illuminate\Support\Facades\Log;

class ConfigSyncService
{
    protected IntegrationService $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    private const KEY_MAP = [
        'jira.url'                => 'integration.jira_url',
        'jira.token'              => 'integration.jira_token',
        'gmail.address'           => 'integration.gmail_address',
        'n8n.webhook_support'     => 'integration.n8n_webhook',
        'openrouter.api_key'      => 'integration.openrouter_api_key',
        'gemini.api_key'          => 'integration.gemini_api_key',
        'laravel.base_url'        => 'integration.laravel_api_url',
        'n8n.webhook_config_sync' => 'integration.refresh_config_webhook',
    ];

    /**
     * Sauvegarder une configuration dans IntegrationConfig et Setting.
     */
    public function save(string $service, string $key, ?string $value, bool $triggerSync = true): array
    {
        Log::info("[CONFIG SAVE] service={$service}, key={$key}, has_value=" . (!empty($value) ? 'yes' : 'no'));

        try {
            $cacheKey = "iconfig_{$service}_{$key}";

            // Clear cache before saving
            cache()->forget($cacheKey);
            Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");

            // 1. Enregistrer dans IntegrationConfig
            IntegrationConfig::setValue($service, $key, $value);

            // Clear cache after saving
            cache()->forget($cacheKey);
            Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");

            // 2. Synchroniser dans Setting si mappé
            $settingsKey = self::KEY_MAP["{$service}.{$key}"] ?? null;
            if ($settingsKey) {
                Setting::setValue($settingsKey, $value);
                Log::info("[CONFIG SAVE] Setting table updated for key={$settingsKey}");
            }

            // 3. Déclencher la synchronisation n8n et refresh si demandé
            if ($triggerSync) {
                return $this->syncAll();
            }

            return [
                'success' => true,
                'message' => 'Configuration sauvegardée localement.',
            ];

        } catch (\Throwable $e) {
            Log::error("[CONFIG SAVE FAILED] service={$service}, key={$key}, error=" . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Supprimer une configuration de IntegrationConfig et Setting.
     */
    public function delete(string $service, string $key, bool $triggerSync = true): array
    {
        Log::info("[CONFIG DELETE] service={$service}, key={$key}");

        try {
            $cacheKey = "iconfig_{$service}_{$key}";

            // Clear cache before delete
            cache()->forget($cacheKey);
            Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");

            // 1. Supprimer de IntegrationConfig
            IntegrationConfig::where('service', $service)->where('config_key', $key)->delete();

            // Clear cache after delete
            cache()->forget($cacheKey);
            Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");

            // 2. Supprimer de Setting si mappé
            $settingsKey = self::KEY_MAP["{$service}.{$key}"] ?? null;
            if ($settingsKey) {
                Setting::where('key', $settingsKey)->delete();
                cache()->forget("setting_{$settingsKey}");
                Log::info("[CONFIG DELETE] Setting table updated (deleted) for key={$settingsKey}");
            }

            // 3. Déclencher la synchronisation n8n et refresh si demandé
            if ($triggerSync) {
                return $this->syncAll();
            }

            return [
                'success' => true,
                'message' => 'Configuration supprimée localement.',
            ];

        } catch (\Throwable $e) {
            Log::error("[CONFIG DELETE FAILED] service={$service}, key={$key}, error=" . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Synchroniser toutes les configurations et envoyer le webhook n8n refresh-config.
     */
    public function syncAll(): array
    {
        Log::info("[N8N SYNC START]");

        try {
            // 1. Synchroniser Laravel -> n8n (IntegrationService)
            $syncRes = $this->integrationService->syncConfigToN8n();
            
            if (isset($syncRes['synced']) && !$syncRes['synced']) {
                Log::warning("[N8N SYNC FAILED] Reason: " . ($syncRes['reason'] ?? 'Unknown'));
            } else {
                Log::info("[N8N SYNC SUCCESS]");
            }

            // 2. Envoyer le webhook refresh-config (N8nService)
            N8nService::refreshConfig();
            Log::info("[N8N REFRESH WEBHOOK SENT]");

            return [
                'success' => true,
                'message' => 'Configuration sauvegardée et synchronisée avec succès.',
            ];

        } catch (\Throwable $e) {
            Log::error("[N8N REFRESH FAILED] Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Sauvegardé mais la synchronisation n8n a échoué : ' . $e->getMessage(),
            ];
        }
    }
}

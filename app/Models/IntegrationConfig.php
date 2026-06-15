<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationConfig extends Model
{
    protected $fillable = ['service', 'config_key', 'config_value'];

    /**
     * Get a config value, with optional .env fallback.
     */
    public static function getValue(string $service, string $key, ?string $envFallback = null): ?string
    {
        $cacheKey = "iconfig_{$service}_{$key}";

        return cache()->remember($cacheKey, now()->addHour(), function () use ($service, $key, $envFallback) {
            $record = static::where('service', $service)->where('config_key', $key)->first();
            if ($record && $record->config_value !== null) {
                return $record->config_value;
            }
            return $envFallback ? env($envFallback) : null;
        });
    }

    /**
     * Set a config value and clear its cache.
     */
    public static function setValue(string $service, string $key, ?string $value): void
    {
        $cacheKey = "iconfig_{$service}_{$key}";
        
        cache()->forget($cacheKey);
        \Illuminate\Support\Facades\Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");

        static::updateOrCreate(
            ['service' => $service, 'config_key' => $key],
            ['config_value' => $value]
        );

        cache()->forget($cacheKey);
        \Illuminate\Support\Facades\Log::info("[CONFIG CACHE CLEARED] service={$service}, key={$key}");
    }

    /**
     * Return all configs for a given service as a flat key => value array.
     */
    public static function getServiceConfig(string $service): array
    {
        return static::where('service', $service)
            ->pluck('config_value', 'config_key')
            ->toArray();
    }
}

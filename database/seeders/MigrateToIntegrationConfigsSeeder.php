<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IntegrationConfig;
use App\Models\Setting;

class MigrateToIntegrationConfigsSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            ['jira',        'url',             Setting::where('key','integration.jira_url')->value('value')             ?: env('JIRA_URL')],
            ['jira',        'token',           Setting::where('key','integration.jira_token')->value('value')           ?: env('JIRA_TOKEN')],
            ['gmail',       'address',         Setting::where('key','integration.gmail_address')->value('value')        ?: env('MAIL_FROM_ADDRESS')],
            ['n8n',         'webhook_support', Setting::where('key','integration.n8n_webhook')->value('value')          ?: env('N8N_WEBHOOK_URL')],
            ['openrouter',  'api_key',         Setting::where('key','integration.openrouter_api_key')->value('value')   ?: env('OPENROUTER_API_KEY')],
            ['gemini',      'api_key',         Setting::where('key','integration.gemini_api_key')->value('value')       ?: env('GEMINI_API_KEY')],
            ['laravel',     'base_url',        env('APP_URL', 'http://localhost:8000')],
        ];

        foreach ($map as [$service, $key, $value]) {
            if ($value) {
                IntegrationConfig::setValue($service, $key, $value);
                $this->command->info("Migrated: {$service}.{$key}");
            }
        }
    }
}

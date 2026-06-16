<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ConfigSyncService;
use App\Services\N8nService;
use App\Support\SettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    protected ConfigSyncService $configSyncService;

    public function __construct(ConfigSyncService $configSyncService)
    {
        $this->configSyncService = $configSyncService;
    }

    public function index()
    {
        $integrations = $this->getIntegrations();
        return view('admin.settings.index', compact('integrations'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatars', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profil mis à jour');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->with('error', 'Mot de passe actuel incorrect');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return back()->with('success', 'Mot de passe mis à jour');
    }

    public function updateAppearance(Request $request)
    {
        $theme = $request->input('theme', 'light');
        Setting::setValue('ui.theme', $theme);
        return back()->with('success', 'Thème mis à jour');
    }

    public function updateIntegrations(Request $request)
    {
        Log::info('==============================');
        Log::info('UPDATE INTEGRATIONS START');
        Log::info('==============================');
        Log::info('Request Data', $request->all());

        $fields = [
            'integration.openai_api_key',
            'integration.openrouter_api_key',
            'integration.gemini_api_key',
            'integration.jira_url',
            'integration.jira_token',
            'integration.gmail_address',
            'integration.n8n_webhook',
            'integration.laravel_api_url',
            'integration.refresh_config_webhook',
        ];

        $configMap = [
            'integration.jira_url'               => ['jira', 'url'],
            'integration.jira_token'             => ['jira', 'token'],
            'integration.gmail_address'          => ['gmail', 'address'],
            'integration.n8n_webhook'            => ['n8n', 'webhook_support'],
            'integration.openrouter_api_key'     => ['openrouter', 'api_key'],
            'integration.gemini_api_key'         => ['gemini', 'api_key'],
            'integration.laravel_api_url'        => ['laravel', 'base_url'],
            'integration.refresh_config_webhook' => ['n8n', 'webhook_config_sync'],
        ];

        foreach ($fields as $field) {
            Log::info("Checking field: {$field}");

            if (!$request->has($field)) {
                Log::warning("Field not found: {$field}");
                continue;
            }

            $value = $request->input($field);

            Log::info('Field received', [
                'field' => $field,
                'has_value' => !empty($value),
            ]);

            if (isset($configMap[$field])) {
                [$service, $key] = $configMap[$field];
                if (empty($value)) {
                    $this->configSyncService->delete($service, $key, false);
                } else {
                    $this->configSyncService->save($service, $key, $value, false);
                }
            } else {
                if (empty($value)) {
                    Setting::where('key', $field)->delete();
                    cache()->forget("setting_{$field}");
                    Log::info("Deleted setting: {$field}");
                } else {
                    Setting::setValue($field, $value);
                    Log::info("Saved setting: {$field}");
                }
            }
        }

        $syncResult = $this->configSyncService->syncAll();

        Log::info('==============================');
        Log::info('UPDATE INTEGRATIONS END');
        Log::info('==============================');

        if (!$syncResult['success']) {
            return back()->with('error', $syncResult['message']);
        }

        return back()->with('success', 'Intégrations mises à jour et configuration n8n synchronisée');
    }

    private function getIntegrations(): array
    {
        return [
            'openai_api_key' => SettingsManager::get('integration.openai_api_key', 'OPENAI_API_KEY'),
            'openrouter_api_key' => SettingsManager::get('integration.openrouter_api_key', 'OPENROUTER_API_KEY'),
            'gemini_api_key' => SettingsManager::get('integration.gemini_api_key', 'GEMINI_API_KEY'),
            'jira_url' => SettingsManager::get('integration.jira_url', 'JIRA_URL'),
            'jira_token' => SettingsManager::get('integration.jira_token', 'JIRA_TOKEN'),
            'gmail_address' => SettingsManager::get('integration.gmail_address', 'MAIL_FROM_ADDRESS'),
            'n8n_webhook' => SettingsManager::get('integration.n8n_webhook', 'N8N_WEBHOOK_URL'),
            'laravel_api_url' => SettingsManager::get('integration.laravel_api_url', 'APP_URL'),
            'refresh_config_webhook' => SettingsManager::get('integration.refresh_config_webhook', null),
        ];
    }

    public function download_log_file()
    {
        $logFile = storage_path('logs/audit.log');

        if (!File::exists($logFile)) {
            return back()->with('error', 'Aucun fichier de log trouvé.');
        }

        $content = File::get($logFile);
        $fileName = 'Journal_' . now()->format('Y_m_d_His') . '.txt';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=$fileName");
    }
}
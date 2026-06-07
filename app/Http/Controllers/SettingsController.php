<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => config('app.name'),
            'admin_email' => env('ADMIN_EMAIL'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];

        $integrations = [
            [
                'name' => 'OpenAI',
                'status' => !empty(env('OPENAI_API_KEY')),
                'key' => env('OPENAI_API_KEY'),
            ],
            [
                'name' => 'Jira',
                'status' => !empty(env('JIRA_URL')),
                'key' => env('JIRA_URL'),
            ],
            [
                'name' => 'SMTP',
                'status' => !empty(env('MAIL_HOST')),
                'key' => env('MAIL_HOST'),
            ],
            [
                'name' => 'n8n',
                'status' => !empty(env('N8N_WEBHOOK_URL')),
                'key' => env('N8N_WEBHOOK_URL'),
            ],
        ];

        $logs = $this->getRecentLogs();

        return view('settings', compact(
            'settings',
            'integrations',
            'logs'
        ));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email'],
            'timezone' => ['required'],
            'locale' => ['required'],
        ]);

        return back()->with(
            'success',
            'Informations générales mises à jour avec succès.'
        );
    }

    public function updateIntegration(Request $request)
    {
        $request->validate([
            'service' => ['required'],
            'api_key' => ['nullable', 'string'],
        ]);

        return back()->with(
            'success',
            'Intégration mise à jour avec succès.'
        );
    }

    /**
     * Lire les derniers logs Laravel
     */
    private function getRecentLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return [];
        }

        $content = File::get($logFile);

        $lines = explode("\n", $content);

        $lines = array_filter($lines);

        return array_slice(
            array_reverse($lines),
            0,
            20
        );
    }
}
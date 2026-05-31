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
                'name' => 'Stripe',
                'status' => !empty(env('STRIPE_SECRET')),
                'key' => env('STRIPE_SECRET'),
            ],
            [
                'name' => 'OpenAI',
                'status' => !empty(env('OPENAI_API_KEY')),
                'key' => env('OPENAI_API_KEY'),
            ],
            [
                'name' => 'SMTP',
                'status' => !empty(env('MAIL_HOST')),
                'key' => env('MAIL_HOST'),
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
        $data = $request->validate([
        'app_name' => ['required', 'string', 'max:255'],
        'admin_email' => ['required', 'email'],
        'timezone' => ['required'],
        'locale' => ['required'],
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with(
            'success',
            'Informations générales mises à jour.'
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
            'Intégration mise à jour.'
        );
    }

 
}
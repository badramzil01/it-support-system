<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
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
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
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
        $this->setSetting('ui.theme', $theme);
        return back()->with('success', 'Thème mis à jour');
    }

    public function updateIntegrations(Request $request)
    {
        $fields = [
            'integration.openai_api_key',
            'integration.openrouter_api_key',
            'integration.gemini_api_key',
            'integration.jira_url',
            'integration.jira_token',
            'integration.gmail_address',
            'integration.n8n_webhook',
        ];
        foreach ($fields as $f) {
            if ($request->has($f)) {
                $value = $request->input($f);
                if ($value === null || $value === '') {
                    Setting::where('key', $f)->delete();
                } else {
                    $this->setSetting($f, $value);
                }
            }
        }
        return back()->with('success', 'Intégrations mises à jour');
    }

    private function setSetting(string $key, $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    private function getIntegrations(): array
    {
        return [
            'openai_api_key'    => Setting::where('key','integration.openai_api_key')->value('value') ?: env('OPENAI_API_KEY',''),
            'openrouter_api_key'=> Setting::where('key','integration.openrouter_api_key')->value('value') ?: env('OPENROUTER_API_KEY',''),
            'gemini_api_key'    => Setting::where('key','integration.gemini_api_key')->value('value') ?: env('GEMINI_API_KEY',''),
            'jira_url'          => Setting::where('key','integration.jira_url')->value('value') ?: env('JIRA_URL',''),
            'jira_token'        => Setting::where('key','integration.jira_token')->value('value') ?: env('JIRA_TOKEN',''),
            'gmail_address'     => Setting::where('key','integration.gmail_address')->value('value') ?: env('MAIL_FROM_ADDRESS',''),
            'n8n_webhook'       => Setting::where('key','integration.n8n_webhook')->value('value') ?: env('N8N_WEBHOOK_URL',''),
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

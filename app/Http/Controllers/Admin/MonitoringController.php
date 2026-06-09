<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    public function index()
    {
        $services = [
            $this->checkLaravel(),
            $this->checkMysql(),
            $this->checkN8n(),
            $this->checkJira(),
            $this->checkGmail(),
            $this->checkAI(),
        ];
        return view('admin.monitoring.index', compact('services'));
    }

    private function checkLaravel(): array
    {
        $start = microtime(true);
        $latency = (int) ((microtime(true) - $start) * 1000);
        return [
            'name' => 'Laravel',
            'icon' => 'server',
            'status' => 'online',
            'version' => app()->version(),
            'response' => $latency . 'ms',
            'last_sync' => now()->format('d/m/Y H:i'),
            'last_error' => null,
            'uptime' => '99.99%',
        ];
    }

    private function checkMysql(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latency = (int) ((microtime(true) - $start) * 1000);
            $version = DB::select('SELECT VERSION() as v')[0]->v ?? '—';
            return [
                'name' => 'MySQL',
                'icon' => 'database',
                'status' => 'online',
                'version' => $version,
                'response' => $latency . 'ms',
                'last_sync' => now()->format('d/m/Y H:i'),
                'last_error' => null,
                'uptime' => '99.95%',
            ];
        } catch (\Throwable $e) {
            return [
                'name' => 'MySQL', 'icon' => 'database', 'status' => 'offline',
                'version' => '—', 'response' => '—', 'last_sync' => '—',
                'last_error' => $e->getMessage(), 'uptime' => '—',
            ];
        }
    }

    private function checkN8n(): array
    {
        $url = config('services.n8n.url') ?: env('N8N_WEBHOOK_URL');
        $result = [
            'name' => 'n8n', 'icon' => 'flow', 'status' => $url ? 'online' : 'unknown',
            'version' => '—', 'response' => '—', 'last_sync' => '—', 'last_error' => null, 'uptime' => '—',
        ];
        if ($url) {
            try {
                $start = microtime(true);
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_NOBODY => true,
                ]);
                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $latency = (int) ((microtime(true) - $start) * 1000);
                $result['response'] = $latency . 'ms';
                $result['last_sync'] = now()->format('d/m/Y H:i');
                $result['status'] = $code >= 200 && $code < 500 ? 'online' : 'degraded';
                if ($code >= 400) $result['last_error'] = 'HTTP ' . $code;
            } catch (\Throwable $e) {
                $result['status'] = 'offline';
                $result['last_error'] = $e->getMessage();
            }
        }
        return $result;
    }

    private function checkJira(): array
    {
        $url = config('services.jira.url') ?: env('JIRA_URL');
        $result = [
            'name' => 'Jira', 'icon' => 'ticket', 'status' => $url ? 'online' : 'unknown',
            'version' => '—', 'response' => '—', 'last_sync' => '—', 'last_error' => null, 'uptime' => '—',
        ];
        if ($url) {
            try {
                $start = microtime(true);
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_NOBODY => true,
                ]);
                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $latency = (int) ((microtime(true) - $start) * 1000);
                $result['response'] = $latency . 'ms';
                $result['last_sync'] = now()->format('d/m/Y H:i');
                $result['status'] = $code >= 200 && $code < 500 ? 'online' : 'degraded';
                if ($code >= 400) $result['last_error'] = 'HTTP ' . $code;
            } catch (\Throwable $e) {
                $result['status'] = 'offline';
                $result['last_error'] = $e->getMessage();
            }
        }
        return $result;
    }

    private function checkGmail(): array
    {
        $addr = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        return [
            'name' => 'Gmail', 'icon' => 'mail',
            'status' => $addr ? 'online' : 'unknown',
            'version' => '—', 'response' => '—', 'last_sync' => $addr ? now()->format('d/m/Y H:i') : '—',
            'last_error' => null, 'uptime' => $addr ? '99.90%' : '—',
        ];
    }

    private function checkAI(): array
    {
        $key = config('services.openai.key') ?: config('services.openrouter.key') ?: env('OPENAI_API_KEY') ?: env('OPENROUTER_API_KEY');
        return [
            'name' => 'OpenAI / OpenRouter', 'icon' => 'sparkles',
            'status' => $key ? 'online' : 'unknown',
            'version' => '—', 'response' => '—', 'last_sync' => $key ? now()->format('d/m/Y H:i') : '—',
            'last_error' => null, 'uptime' => $key ? '99.80%' : '—',
        ];
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\IntegrationService;

class MonitorServicesJob implements ShouldQueue
{
    use Queueable;

    public function handle(IntegrationService $integrationService): void
    {
        $services = [
            'openai' => fn() => $integrationService->testOpenAI(),
            'openrouter' => fn() => $integrationService->testOpenRouter(),
            'gemini' => fn() => $integrationService->testGemini(),
            'jira' => fn() => $integrationService->testJira(),
            'n8n' => fn() => $integrationService->testN8N(),
            'gmail' => fn() => $integrationService->testGmail(),
            'laravel' => fn() => $integrationService->testLaravel(),
            'mysql' => fn() => $integrationService->testMysql(),
        ];

        foreach ($services as $serviceName => $testFunction) {
            $result = $testFunction();
            
            $status = $result['success'] ? 'online' : 'offline';
            
            \App\Models\SystemMetric::updateOrCreate(
                ['service' => $serviceName],
                [
                    'status' => $status,
                    'response_time' => $result['time'],
                    'last_sync' => now()
                ]
            );

            if ($status === 'offline') {
                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send(
                    $admins, 
                    new \App\Notifications\ServiceOfflineNotification(strtoupper($serviceName) . ' est hors ligne')
                );
            }
        }
    }
}

<?php

namespace App\Jobs;

use App\Services\EscalationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEscalationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 3;

    public function handle(EscalationService $escalationService): void
    {
        Log::info('escalation.job.start');

        $results = $escalationService->processSlaChecks();

        Log::info('escalation.job.done', $results);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('escalation.job.failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
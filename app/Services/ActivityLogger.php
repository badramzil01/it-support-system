<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{

    public function log(
        string $action,
        ?string $model = null,
        ?int $modelId = null,
        ?string $description = null,
        array $context = [],
        ?array $old = null,
        ?array $new = null
    ): void {
        
        $userId = auth()->id();

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()?->ip(),
        ]);

        Log::channel('audit')->info('audit', [
            'action' => $action,
            'user_id' => $userId,
            'model' => $model,
            'model_id' => $modelId,
            'context' => $context,
        ]);

    }
}

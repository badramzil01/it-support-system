<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public static function log(string $action,?string $model = null,?int $modelId = null,?string $description = null, array $context = []) 
    {
        $userId = auth()->id();

        Log::info($action, array_merge([
            'user_id' => $userId,
            'model' => $model,
            'model_id' => $modelId,
        ], $context));

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}

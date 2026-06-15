<?php

namespace App\Observers;

use App\Services\ActivityLogger;

class GlobalObserver
{
    public function created($model)
    {
        
        if ($model instanceof \App\Models\AuditLog) {
            return;
        }
        app(ActivityLogger::class)->log(
            'created',
            get_class($model),
            $model->id,
            'test description',
            [],
            null,
            $model->toArray()
        );
    }

    public function updated($model)
    {
        if ($model instanceof \App\Models\AuditLog) {
            return;
        }
        app(ActivityLogger::class)->log(
            'updated',
            get_class($model),
            $model->id,
            $model->getOriginal(),
            $model->getChanges()
        );
    }

    public function deleted($model)
    {
        if ($model instanceof \App\Models\AuditLog) {
            return;
        }
        app(ActivityLogger::class)->log(
            'deleted',
            get_class($model),
            $model->id
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    protected $fillable = [
        'service',
        'status',
        'response_time',
        'last_sync'
    ];

    protected $casts = [
        'last_sync' => 'datetime',
    ];
}

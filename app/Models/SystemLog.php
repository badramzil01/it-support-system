<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'service',
        'level',
        'message',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];
}

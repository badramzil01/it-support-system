<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIResponse extends Model
{
    protected $table = 'ai_responses';

    protected $fillable = [

        'conversation_id',

        'user_id',

        'message',

        'user_message',

        'ai_response',

        'source',

        'priority',

        'category',

        'is_urgent',

        'is_escalated',

        'create_ticket',

        'has_image',

        'image_url',

        'reason',

    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    protected $fillable = [

        'conversation_id',

        'user_id',

        'message_id',

        'title',

        'description',

        'jira_ticket_id',

        'solution',

        'source',

        'status',

        'priority',

        'category',

        'confidence',

        'feedback',

        'is_escalated'

    ];


    // =====================================================
    // ✅ RELATION MESSAGE
    // =====================================================
    public function message()
    {

        return $this->belongsTo(Message::class);

    }


    // =====================================================
    // ✅ RELATION NOTIFICATIONS
    // =====================================================
    public function notifications()
    {

        return $this->hasMany(Notification::class);

    }

}
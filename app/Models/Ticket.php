<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'message_id',
        'jira_ticket_id',
        'solution',
        'source',
        'status'
    ];

    // 🔗 relation avec Message
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    // 🔗 relation avec Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
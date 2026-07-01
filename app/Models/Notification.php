<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'type',
        'status',
        'sent_at',
        'data',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'data' => 'array',
    ];

    // 🔗 relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 relation avec Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

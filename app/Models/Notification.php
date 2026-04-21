<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'ticket_id',
        'type',
        'status',
        'sent_at'
    ];

    // 🔗 relation avec Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
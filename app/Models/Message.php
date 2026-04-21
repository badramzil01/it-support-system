<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'channel'
    ];

    // 🔗 relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 relation avec Ticket
    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
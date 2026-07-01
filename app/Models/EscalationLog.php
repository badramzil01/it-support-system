<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscalationLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'from_level',
        'to_level',
        'from_user_id',
        'to_user_id',
        'reason',
        'sla_breached',
    ];

    protected $casts = [
        'from_level' => 'integer',
        'to_level' => 'integer',
        'sla_breached' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getLevelNameAttribute(): string
    {
        return match ($this->to_level) {
            0       => 'Initial',
            1       => 'N1',
            2       => 'N2',
            3       => 'N3',
            4       => 'Support Manager',
            default => 'Unknown',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'ticket_id',
        'content',
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // =========================================
    // RELATIONS
    // =========================================

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeBetween($query, int $userId1, int $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where(fn ($q2) => $q2->where('sender_id', $userId1)->where('receiver_id', $userId2))
              ->orWhere(fn ($q2) => $q2->where('sender_id', $userId2)->where('receiver_id', $userId1));
        });
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'request'      => 'Demande',
            'info_request' => 'Info request',
            'follow_up'    => 'Suivi',
            default        => 'Message',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'request'      => 'warning',
            'info_request' => 'info',
            'follow_up'    => 'success',
            default        => 'primary',
        };
    }
}
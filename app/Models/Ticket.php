<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * Champs autorisés
     */
    protected $fillable = [
    'conversation_id',
    'user_id',
    'assigned_to',
    'message_id',
    'trigger_message_id',
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
    'is_urgent',
    'is_escalated',
    'has_image',
    'resolved_at',
    'closed_at',
	];
    /**
     * Casts
     */
    protected $casts = [
        'is_urgent'    => 'boolean',
        'is_escalated' => 'boolean',
        'has_image'    => 'boolean',
        'confidence'   => 'float',
        'resolved_at'  => 'datetime',
        'closed_at'    => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Employé ayant créé le ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Agent support assigné
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Conversation associée
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Message source
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function triggerMessage()
    {
        return $this->belongsTo(Message::class, 'trigger_message_id');
    }

    /**
     * Notifications liées au ticket
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            default    => 'success',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'closed'      => 'success',
            'resolved'    => 'success',
            'in_progress' => 'warning',
            'open'        => 'primary',
            default       => 'secondary',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }
}

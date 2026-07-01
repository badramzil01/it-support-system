<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'ticket_status',
        'priority',
        'category',
        'confidence',
        'feedback',
        'is_urgent',
        'is_escalated',
        'has_image',
        'image_url',
        'mime_type',
        'resolved_at',
        'closed_at',
        'support_level',
        'assigned_team',
        'escalation_level',
        'sla_deadline',
        'escalated_at',
        'last_response_at',
        'sla_breached',
        'previous_owner',
        'escalation_reason',
        // Escalade N1/N2
        'escalated',
        'escalated_by',
        'resolved_by',
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
        'sla_deadline' => 'datetime',
        'escalated_at' => 'datetime',
        'last_response_at' => 'datetime',
        'sla_breached' => 'boolean',
        'escalation_level' => 'integer',
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

    /**
     * Agent N1/N2 qui a escaladé le ticket.
     */
    public function escalatedByUser()
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    /**
     * Agent N1/N2 qui a résolu le ticket.
     */
    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeNotResolved($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeN1($query)
    {
        return $query->where('support_level', 'N1');
    }

    public function scopeN2($query)
    {
        return $query->where('support_level', 'N2');
    }

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

    /**
     * Manual escalation history.
     */
    public function escalationHistory()
    {
        return $this->hasMany(TicketEscalation::class)->latest();
    }

    public function supportTeam()
    {
        return $this->belongsTo(SupportTeam::class, 'assigned_team', 'slug');
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

    /**
     * Escalade le ticket de N1 vers N2.
     */
    public function escalateToN2(int $escalatedByUserId, string $reason): bool
    {
        if ($this->support_level === 'N2') {
            return false;
        }

        $currentEscalationLevel = $this->escalation_level ?? 0;

        // Créer l'historique d'escalade
        \App\Models\TicketEscalation::create([
            'ticket_id' => $this->id,
            'from_level' => $this->support_level ?? 'N1',
            'to_level' => 'N2',
            'escalated_by' => $escalatedByUserId,
            'reason' => $reason,
        ]);

        $updated = $this->update([
            'support_level'     => 'N2',
            'assigned_team'     => 'support_n2',
            'escalation_level'  => $currentEscalationLevel + 1,
            'is_escalated'      => true,
            'escalated'         => true,
            'escalated_by'      => $escalatedByUserId,
            'escalated_at'      => now(),
            'escalation_reason' => $reason,
            'status'            => 'open',
        ]);

        if ($updated) {
            Log::info('ticket.escalated_to_n2', [
                'ticket_id'      => $this->id,
                'escalated_by'   => $escalatedByUserId,
                'reason'         => $reason,
                'new_level'      => 'N2',
                'escalation_lvl' => $currentEscalationLevel + 1,
            ]);
        }

        return $updated;
    }
}

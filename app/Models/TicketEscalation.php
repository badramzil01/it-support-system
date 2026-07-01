<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEscalation extends Model
{
    protected $fillable = [
        'ticket_id',
        'jira_key',
        'from_level',
        'to_level',
        'previous_team',
        'current_team',
        'escalated_by',
        'assigned_to',
        'reason',
        'escalation_level',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function escalatedBy()
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
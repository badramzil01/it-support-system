<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\HasMany;

class SupportTeam extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'support_level',
        'is_active',
        'sla_hours_low',
        'sla_hours_medium',
        'sla_hours_high',
        'sla_minutes_critical',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours_low' => 'integer',
        'sla_hours_medium' => 'integer',
        'sla_hours_high' => 'integer',
        'sla_minutes_critical' => 'integer',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'support_team_members')
            ->withPivot('is_leader')
            ->withTimestamps();
    }

    public function leaders(): BelongsToMany
    {
        return $this->members()->wherePivot('is_leader', true);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_team', 'slug');
    }

    public function escalationLogs()
    {
        return $this->hasMany(EscalationLog::class);
    }

    /**
     * Get SLA duration in hours for a given priority.
     */
    public function getSlaHours(string $priority): float
    {
        return match ($priority) {
            'low'      => $this->sla_hours_low,
            'medium'   => $this->sla_hours_medium,
            'high'     => $this->sla_hours_high,
            'critical' => $this->sla_minutes_critical / 60,
            default    => $this->sla_hours_medium,
        };
    }

    /**
     * Get the next escalation level team.
     */
    public function getNextLevelTeam(): ?self
    {
        $nextLevel = match ($this->support_level) {
            'n1'      => 'n2',
            'n2'      => 'n3',
            'n3'      => 'manager',
            default   => null,
        };

        if (!$nextLevel) return null;

        return self::where('support_level', $nextLevel)
            ->where('is_active', true)
            ->first();
    }
}
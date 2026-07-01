<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\SupportTeam;
use App\Models\EscalationLog;
use App\Models\User;
use App\Models\Notification as AppNotification;
use App\Notifications\TicketEscalationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EscalationService
{
    /**
     * Priority to initial level mapping.
     * Low/Medium → N1, High → N2, Critical → N3
     */
    private const PRIORITY_TO_TEAM = [
        'low'      => 'n1',
        'medium'   => 'n1',
        'high'     => 'n2',
        'critical' => 'n3',
    ];

    /**
     * Priority to SLA hours mapping (fallback if team SLA not set).
     */
    private const PRIORITY_SLA_HOURS = [
        'low'      => 24,
        'medium'   => 8,
        'high'     => 2,
        'critical' => 0.5, // 30 minutes
    ];

    /**
     * Auto-assign a ticket based on priority.
     */
    public function autoAssign(Ticket $ticket): void
    {
        $priority = $ticket->priority ?? 'medium';
        $targetLevel = self::PRIORITY_TO_TEAM[$priority] ?? 'n1';

        $team = SupportTeam::where('support_level', $targetLevel)
            ->where('is_active', true)
            ->first();

        if (!$team) {
            // Fallback: try N1
            $team = SupportTeam::where('support_level', 'n1')
                ->where('is_active', true)
                ->first();
        }

        if (!$team) {
            Log::warning('escalation.auto_assign.no_team', [
                'ticket_id' => $ticket->id,
                'priority' => $priority,
            ]);
            return;
        }

        // Find an available agent in the team
        $agent = $this->findAvailableAgent($team);

        // Calculate SLA deadline
        $slaHours = $team->getSlaHours($priority);
        $slaDeadline = Carbon::now()->addHours($slaHours);

        $ticket->update([
            'support_level'   => $team->support_level,
            'assigned_team'   => $team->slug,
            'assigned_to'     => $agent?->id,
            'escalation_level' => $this->getLevelNumber($team->support_level),
            'sla_deadline'    => $slaDeadline,
            'last_response_at' => now(),
        ]);

        Log::info('ticket.auto_assigned', [
            'ticket_id'    => $ticket->id,
            'team'         => $team->name,
            'team_level'   => $team->support_level,
            'assigned_to'  => $agent?->id,
            'sla_deadline' => $slaDeadline,
        ]);

        // Notify assigned agent
        if ($agent) {
            $this->notifyAssigned($ticket, $agent);
        }
    }

    /**
     * Process SLA checks for all open tickets.
     */
    public function processSlaChecks(): array
    {
        $results = ['checked' => 0, 'escalated' => 0];

        $tickets = Ticket::whereNotIn('status', ['closed', 'resolved'])
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<=', now())
            ->with(['user', 'assignedAgent'])
            ->get();

        foreach ($tickets as $ticket) {
            $results['checked']++;

            if ($this->shouldEscalate($ticket)) {
                $this->escalate($ticket);
                $results['escalated']++;
            }
        }

        Log::info('escalation.sla_check', $results);

        return $results;
    }

    /**
     * Check if a ticket should be escalated.
     */
    public function shouldEscalate(Ticket $ticket): bool
    {
        if (in_array($ticket->status, ['closed', 'resolved'])) {
            return false;
        }

        if (!$ticket->sla_deadline) {
            return false;
        }

        return $ticket->sla_deadline->isPast();
    }

    /**
     * Escalate a ticket to the next level.
     */
    public function escalate(Ticket $ticket): bool
    {
        $currentLevel = $ticket->escalation_level ?? 0;

        // Get the next team based on current level
        $nextTeam = $this->getNextTeam($currentLevel);

        if (!$nextTeam) {
            Log::warning('escalation.no_next_team', [
                'ticket_id'     => $ticket->id,
                'current_level' => $currentLevel,
            ]);

            // Mark as breached but no more escalation possible
            $ticket->update([
                'sla_breached' => true,
                'is_escalated' => true,
            ]);

            return false;
        }

        // Find an available agent in the next team
        $newAgent = $this->findAvailableAgent($nextTeam);

        // Store previous owner before updating
        $previousOwner = $ticket->assigned_to;

        // Calculate reason
        $reason = $this->getEscalationReason($ticket, $currentLevel, $this->getLevelNumber($nextTeam->support_level));

        // Log the escalation
        EscalationLog::create([
            'ticket_id'     => $ticket->id,
            'from_level'    => $currentLevel,
            'to_level'      => $this->getLevelNumber($nextTeam->support_level),
            'from_user_id'  => $previousOwner,
            'to_user_id'    => $newAgent?->id,
            'reason'        => $reason,
            'sla_breached'  => true,
        ]);

        // Calculate new SLA deadline
        $newSlaHours = $nextTeam->getSlaHours($ticket->priority ?? 'medium');

        // Update the ticket
        $ticket->update([
            'support_level'     => $nextTeam->support_level,
            'assigned_team'     => $nextTeam->slug,
            'assigned_to'       => $newAgent?->id,
            'escalation_level'  => $this->getLevelNumber($nextTeam->support_level),
            'sla_deadline'      => Carbon::now()->addHours($newSlaHours),
            'escalated_at'      => now(),
            'sla_breached'      => true,
            'is_escalated'      => true,
            'previous_owner'    => $previousOwner,
            'escalation_reason' => $reason,
        ]);

        Log::info('ticket.escalated', [
            'ticket_id'   => $ticket->id,
            'from_level'  => $currentLevel,
            'to_level'    => $this->getLevelNumber($nextTeam->support_level),
            'from_team'   => $ticket->getOriginal('assigned_team'),
            'to_team'     => $nextTeam->slug,
            'assigned_to' => $newAgent?->id,
        ]);

        // Send notifications
        $this->notifyEscalation($ticket, $nextTeam, $newAgent, $reason);

        // Sync to Jira if linked
        $this->syncToJira($ticket, "Ticket escalated from level $currentLevel to " . $nextTeam->support_level . ". Reason: $reason");

        return true;
    }

    /**
     * Get the level number for a support_level string.
     */
    private function getLevelNumber(?string $supportLevel): int
    {
        return match ($supportLevel) {
            'n1'      => 1,
            'n2'      => 2,
            'n3'      => 3,
            'manager' => 4,
            default   => 0,
        };
    }

    /**
     * Get the next escalation team based on current level.
     */
    private function getNextTeam(int $currentLevel): ?SupportTeam
    {
        $levelMap = [
            0 => 'n1',
            1 => 'n2',
            2 => 'n3',
            3 => 'manager',
        ];

        $nextLevelName = $levelMap[$currentLevel + 1] ?? null;
        if (!$nextLevelName) return null;

        return SupportTeam::where('support_level', $nextLevelName)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Find an available agent in a team (least tickets assigned).
     */
    private function findAvailableAgent(SupportTeam $team): ?User
    {
        $memberIds = $team->members()->pluck('users.id')->toArray();

        if (empty($memberIds)) return null;

        // Find the agent with the fewest open tickets
        return User::whereIn('id', $memberIds)
            ->withCount(['tickets as open_tickets_count' => function ($q) {
                $q->whereNotIn('status', ['closed', 'resolved']);
            }])
            ->orderBy('open_tickets_count')
            ->first();
    }

    /**
     * Generate escalation reason text.
     */
    private function getEscalationReason(Ticket $ticket, int $fromLevel, int $toLevel): string
    {
        $levelNames = [0 => 'Initial', 1 => 'N1', 2 => 'N2', 3 => 'N3', 4 => 'Support Manager'];

        $fromName = $levelNames[$fromLevel] ?? "Level $fromLevel";
        $toName = $levelNames[$toLevel] ?? "Level $toLevel";

        $elapsed = $ticket->created_at ? $ticket->created_at->diffForHumans() : 'unknown';

        return "SLA breached. Escalated from $fromName to $toName. Ticket created $elapsed.";
    }

    /**
     * Send notifications for escalation.
     */
    private function notifyEscalation(Ticket $ticket, SupportTeam $nextTeam, ?User $newAgent, string $reason): void
    {
        // Notify the new agent via Laravel notification system
        if ($newAgent) {
            $newAgent->notify(new TicketEscalationNotification(
                $ticket,
                $reason,
                $newAgent
            ));
        }

        // Notify team leaders
        $leaders = $nextTeam->leaders()->get();
        foreach ($leaders as $leader) {
            if ($leader->id !== ($newAgent?->id)) {
                $leader->notify(new TicketEscalationNotification(
                    $ticket,
                    "Escalated to your team ({$nextTeam->name}): {$reason}",
                    $newAgent
                ));
            }
        }

        // Also create in-app notifications for users without notifiable trait fallback
        if ($newAgent) {
            AppNotification::create([
                'user_id'   => $newAgent->id,
                'type'      => 'ticket_escalated',
                'ticket_id' => $ticket->id,
                'status'    => 'pending',
                'sent_at'   => now(),
                'data'      => [
                    'reason' => $reason,
                    'from_level' => $ticket->escalation_level,
                    'to_level' => $this->getLevelNumber($nextTeam->support_level),
                ],
            ]);
        }

        foreach ($leaders as $leader) {
            if ($leader->id !== ($newAgent?->id)) {
                AppNotification::create([
                    'user_id'   => $leader->id,
                    'type'      => 'ticket_escalated',
                    'ticket_id' => $ticket->id,
                    'status'    => 'pending',
                    'sent_at'   => now(),
                    'data'      => [
                        'reason' => $reason,
                        'team' => $nextTeam->name,
                    ],
                ]);
            }
        }
    }

    /**
     * Notify assigned agent of new ticket.
     */
    private function notifyAssigned(Ticket $ticket, User $agent): void
    {
        $agent->notify(new TicketEscalationNotification(
            $ticket,
            "New ticket assigned to you.",
            $agent
        ));

        AppNotification::create([
            'user_id'   => $agent->id,
            'type'      => 'ticket_assigned',
            'ticket_id' => $ticket->id,
            'status'    => 'pending',
            'sent_at'   => now(),
            'data'      => [
                'title' => $ticket->title,
                'priority' => $ticket->priority,
            ],
        ]);
    }

    /**
     * Sync escalation to Jira.
     */
    private function syncToJira(Ticket $ticket, string $comment): void
    {
        if (empty($ticket->jira_ticket_id)) return;

        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth(
                env('JIRA_EMAIL'),
                env('JIRA_API_TOKEN')
            )->post(
                env('JIRA_URL') . '/rest/api/3/issue/' . $ticket->jira_ticket_id . '/comment',
                [
                    'body' => [
                        'type' => 'doc',
                        'version' => 1,
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    ['type' => 'text', 'text' => $comment]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            // Also update assignee if we have a new owner
            if ($ticket->assigned_to) {
                $agent = User::find($ticket->assigned_to);
                if ($agent && $agent->email) {
                    \Illuminate\Support\Facades\Http::withBasicAuth(
                        env('JIRA_EMAIL'),
                        env('JIRA_API_TOKEN')
                    )->put(
                        env('JIRA_URL') . '/rest/api/3/issue/' . $ticket->jira_ticket_id . '/assignee',
                        ['accountId' => $agent->email] // Using email as fallback
                    );
                }
            }

            // Update Jira priority if escalated
            $priorityMap = [
                'n1' => 'Low',
                'n2' => 'Medium',
                'n3' => 'High',
                'manager' => 'Highest',
            ];

            if (isset($priorityMap[$ticket->support_level])) {
                \Illuminate\Support\Facades\Http::withBasicAuth(
                    env('JIRA_EMAIL'),
                    env('JIRA_API_TOKEN')
                )->put(
                    env('JIRA_URL') . '/rest/api/3/issue/' . $ticket->jira_ticket_id,
                    [
                        'fields' => [
                            'priority' => ['name' => $priorityMap[$ticket->support_level]]
                        ]
                    ]
                );
            }

            Log::info('escalation.jira.synced', [
                'ticket_id' => $ticket->id,
                'jira_key'  => $ticket->jira_ticket_id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('escalation.jira.failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get SLA compliance statistics.
     */
    public function getSlaStats(): array
    {
        $totalBreached = Ticket::where('sla_breached', true)->count();
        $totalResolved = Ticket::whereIn('status', ['resolved', 'closed'])->count();
        $totalTickets = Ticket::count();

        $complianceRate = $totalTickets > 0
            ? round((($totalTickets - $totalBreached) / $totalTickets) * 100, 1)
            : 100;

        // Escalation rate
        $escalatedCount = Ticket::where('is_escalated', true)->count();
        $escalationRate = $totalTickets > 0
            ? round(($escalatedCount / $totalTickets) * 100, 1)
            : 0;

        // Tickets by support level
        $byLevel = Ticket::selectRaw('support_level, count(*) as count')
            ->groupBy('support_level')
            ->pluck('count', 'support_level')
            ->toArray();

        // Average resolution time
        $avgResolution = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total_tickets' => $totalTickets,
            'total_breached' => $totalBreached,
            'total_resolved' => $totalResolved,
            'compliance_rate' => $complianceRate,
            'escalated_count' => $escalatedCount,
            'escalation_rate' => $escalationRate,
            'by_level' => $byLevel,
            'avg_resolution_hours' => round($avgResolution ?? 0, 1),
        ];
    }

    /**
     * Get team performance stats.
     */
    public function getTeamPerformance(): array
    {
        $teams = SupportTeam::withCount('members')->get();
        $performance = [];

        foreach ($teams as $team) {
            $tickets = Ticket::where('assigned_team', $team->slug);
            $total = (clone $tickets)->count();
            $resolved = (clone $tickets)->whereIn('status', ['resolved', 'closed'])->count();
            $breached = (clone $tickets)->where('sla_breached', true)->count();
            $escalated = (clone $tickets)->where('is_escalated', true)->count();

            $avgResolution = (clone $tickets)->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours');

            $performance[] = [
                'team' => $team->name,
                'level' => $team->support_level,
                'members_count' => $team->members_count,
                'total_tickets' => $total,
                'resolved' => $resolved,
                'breached' => $breached,
                'escalated' => $escalated,
                'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
                'sla_compliance' => $total > 0 ? round((($total - $breached) / $total) * 100, 1) : 100,
                'avg_resolution_hours' => round($avgResolution ?? 0, 1),
            ];
        }

        return $performance;
    }
}
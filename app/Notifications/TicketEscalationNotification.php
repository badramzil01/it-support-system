<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class TicketEscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Ticket $ticket;
    public string $escalationMessage;
    public ?User $newOwner;

    public function __construct(Ticket $ticket, string $escalationMessage, ?User $newOwner = null)
    {
        $this->ticket = $ticket;
        $this->escalationMessage = $escalationMessage;
        $this->newOwner = $newOwner;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.ui.tickets.show', $this->ticket->id);

        return (new MailMessage)
            ->subject("🚨 Ticket #{$this->ticket->id} Escalated - {$this->ticket->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A ticket has been escalated and assigned to you.")
            ->line("**Ticket:** #{$this->ticket->id} - {$this->ticket->title}")
            ->line("**Priority:** {$this->ticket->priority}")
            ->line("**Status:** {$this->ticket->status}")
            ->line("**Reason:** {$this->escalationMessage}")
            ->action('View Ticket', $url)
            ->line('Please review and take action as soon as possible.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'message' => $this->escalationMessage,
            'type' => 'ticket_escalated',
            'assigned_to' => $this->newOwner?->name ?? 'Unknown',
        ];
    }
}
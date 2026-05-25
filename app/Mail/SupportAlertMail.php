<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SupportAlertMail extends Mailable
{
    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this

            ->subject('🚨 Ticket Support Urgent')

            ->view('emails.support-alert');
    }
}
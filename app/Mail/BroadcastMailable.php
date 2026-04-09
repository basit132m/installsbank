<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastMailable extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $bodyContent;

    public function __construct(string $emailSubject, string $bodyContent)
    {
        $this->emailSubject = $emailSubject;
        $this->bodyContent  = $bodyContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast',
            text: 'emails.broadcast-text',
        );
    }
}

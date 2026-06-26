<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class BroadcastMailable extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $bodyContent;
    public string $fromEmail;

    public function __construct(string $emailSubject, string $bodyContent, string $fromEmail = 'contact@installsbank.com')
    {
        $this->emailSubject = $emailSubject;
        $this->bodyContent  = $bodyContent;
        $this->fromEmail    = $fromEmail;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->fromEmail, 'Installs Bank'),
            subject: $this->emailSubject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe'       => "<mailto:{$this->fromEmail}?subject=unsubscribe>",
                'List-Unsubscribe-Post'  => 'List-Unsubscribe=One-Click',
                'Precedence'             => 'bulk',
                'X-Mailer'               => 'Installs Bank Mailer',
            ]
        );
    }

    public function content(): Content
    {
        // Plain text only — no HTML to avoid spam filters
        return new Content(
            text: 'emails.broadcast-text',
        );
    }
}

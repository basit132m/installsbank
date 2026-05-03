<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class DomainChangedMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $publisherName,
        public string $uniqueCode,
        public string $oldDomain,
        public string $newDomain,
        public string $newTrackingUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from:    new Address('contact@installsbank.com', 'Installs Bank'),
            replyTo: [new Address('contact@installsbank.com', 'Installs Bank')],
            subject: 'Action Required: Your Tracking Link Domain Has Changed',
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe'      => '<mailto:contact@installsbank.com?subject=unsubscribe>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence'            => 'bulk',
                'X-Mailer'              => 'Installs Bank Mailer',
            ]
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.domain-changed-text');
    }
}

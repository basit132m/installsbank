<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewPublisherMailable extends Mailable
{
    use Queueable, SerializesModels;

    public string $adminUrl;

    public function __construct(public User $publisher)
    {
        $this->adminUrl = url('/admin/publishers/' . $publisher->id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Publisher Registration — ' . $this->publisher->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-publisher',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

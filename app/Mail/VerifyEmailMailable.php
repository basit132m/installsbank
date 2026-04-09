<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;

    public function __construct(public User $user)
    {
        $this->verifyUrl = url('/email/verify/' . $user->verification_token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your email address — Installs Bank',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

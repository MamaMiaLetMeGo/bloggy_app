<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private string $code
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-verification-code',
            with: [
                'code' => $this->code
            ]
        );
    }
}

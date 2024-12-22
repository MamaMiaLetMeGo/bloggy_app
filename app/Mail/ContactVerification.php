<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ContactVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $message;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
        
        // Create a signed URL that expires in 1 hour
        $this->verificationUrl = URL::temporarySignedRoute(
            'contact.verify',
            now()->addHour(),
            [
                'email' => $email,
                'name' => $name,
                'message' => $message
            ]
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->markdown('emails.contact-verification')
                    ->subject('Verify Your Contact Form Submission');
    }
}

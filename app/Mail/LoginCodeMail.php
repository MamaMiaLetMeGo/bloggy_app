<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class LoginCodeMail extends Mailable
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->markdown('emails.login-code')
                    ->subject('Your Login Code');
    }
} 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
            'g-recaptcha-response' => 'required|recaptcha',
        ]);

        // Send email to the configured contact email address
        Mail::to(config('mail.contact_email', config('mail.from.address')))
            ->send(new ContactFormSubmission($validated));

        return back()->with('success', 'Thank you for your message. I will get back to you soon!');
    }
} 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ContactController extends Controller
{
    protected $adminEmail = 'charlieggendron@gmail.com';

    public function show()
    {
        return view('contact');
    }

    public function testEmail(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['message' => 'JSON request expected'], 406);
        }

        try {
            Log::info('Attempting to send test email to: ' . $this->adminEmail);

            // Send a test email
            Mail::to($this->adminEmail)
                ->send(new ContactFormSubmission([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'message' => 'This is a test email to verify the email configuration.'
                ]));

            Log::info('Test email sent successfully to: ' . $this->adminEmail);
            return response()->json(['success' => true, 'message' => 'Test email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(Request $request)
    {
        try {
            // Check if it's a JSON request (chat interface)
            if ($request->expectsJson()) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'message' => 'nullable|string'
                ]);

                // Send email notification
                Mail::to($this->adminEmail)
                    ->send(new ContactFormSubmission([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'message' => $validated['message'] ?? 'No message provided'
                    ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Your message has been sent successfully!'
                ]);
            }

            // Handle traditional form submission
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|min:10',
                'g-recaptcha-response' => 'required|recaptcha',
            ]);

            // Send email to admin
            Mail::to($this->adminEmail)
                ->send(new ContactFormSubmission($validated));

            return back()->with('success', 'Thank you for your message. I will get back to you soon!');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there was a problem sending your message. Please try again later.'
                ], 500);
            }
            
            return back()->with('error', 'Sorry, there was a problem sending your message. Please try again later.');
        }
    }
}
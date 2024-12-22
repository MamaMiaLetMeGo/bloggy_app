<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;
use App\Mail\ContactVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\URL;

class ContactController extends Controller
{
    protected $adminEmail = 'charlieggendron@gmail.com';

    public function show()
    {
        $user = auth()->user();
        return view('contact', compact('user'));
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
            Log::info('Contact form submission received:', $request->all());
            
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'nullable|string'
            ]);

            Log::info('Validation passed, sending verification email to: ' . $data['email']);

            // Create a signed URL for verification
            $verificationUrl = URL::temporarySignedRoute(
                'contact.verify',
                now()->addHour(),
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'message' => $data['message'] ?? null
                ]
            );

            // In local environment, just log the URL
            if (app()->environment('local')) {
                Log::info('Contact verification URL:', ['url' => $verificationUrl]);
                return response()->json([
                    'message' => 'In development mode. Check logs for verification URL.'
                ]);
            }

            // Send verification email
            Mail::to($data['email'])->send(new ContactVerification(
                $data['name'],
                $data['email'],
                $data['message'] ?? null
            ));

            Log::info('Verification email sent successfully');

            return response()->json([
                'message' => 'Please check your email to verify your contact form submission.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Invalid form data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to process contact form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to process your request. Please try again later.'
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link');
        }

        try {
            // Create contact submission
            $submission = ContactSubmission::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message
            ]);

            // Send email to admin
            Mail::to($this->adminEmail)->send(new ContactFormSubmission($submission));

            return redirect()->route('contact.show')->with('success', 'Your contact form has been verified and sent. We will get back to you soon!');
        } catch (\Exception $e) {
            Log::error('Failed to process verified contact form: ' . $e->getMessage());
            return redirect()->route('contact.show')->with('error', 'Failed to process your contact form. Please try again.');
        }
    }
}
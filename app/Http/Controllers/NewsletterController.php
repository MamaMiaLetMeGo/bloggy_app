<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function subscribeEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:newsletter_subscriptions,email'
            ]);

            $subscription = NewsletterSubscription::create([
                'email' => $validated['email'],
                'verified_at' => null
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Successfully subscribed! In production, you would receive a confirmation email.'
                ]);
            }

            return back()->with('success', 'Successfully subscribed! In production, you would receive a confirmation email.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Newsletter subscription error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while subscribing. Please try again.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while subscribing. Please try again.');
        }
    }

    public function subscribe(Request $request)
    {
        try {
            $validated = $request->validate([
                'travel_updates' => 'sometimes|boolean',
                'sailing_updates' => 'sometimes|boolean',
            ]);

            $subscription = NewsletterSubscription::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'travel_updates' => $validated['travel_updates'] ?? true,
                    'sailing_updates' => $validated['sailing_updates'] ?? true,
                ]
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Successfully updated subscription preferences!'
                ]);
            }

            return back()->with('success', 'Successfully updated subscription preferences!');

        } catch (\Exception $e) {
            Log::error('Newsletter subscription error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while updating preferences. Please try again.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while updating preferences. Please try again.');
        }
    }

    public function unsubscribe(Request $request)
    {
        try {
            NewsletterSubscription::where('user_id', auth()->id())->delete();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Successfully unsubscribed from the newsletter.'
                ]);
            }
            return back()->with('success', 'Successfully unsubscribed from the newsletter.');
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscription error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while unsubscribing. Please try again.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while unsubscribing. Please try again.');
        }
    }
}

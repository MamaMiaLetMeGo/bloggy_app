<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $oldSubscription = auth()->user()->newsletterSubscription;
        $travel = $request->has('travel_updates');
        $sailing = $request->has('sailing_updates');

        // Check what changed
        $travelUnsubscribed = $oldSubscription?->travel_updates && !$travel;
        $sailingUnsubscribed = $oldSubscription?->sailing_updates && !$sailing;

        if (!$travel && !$sailing) {
            // If no options selected, delete subscription
            NewsletterSubscription::where('user_id', auth()->id())->delete();
            return back()->with('unsubscribed', 'You have unsubscribed from all updates.');
        }

        $subscription = NewsletterSubscription::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'travel_updates' => $travel,
                'sailing_updates' => $sailing,
            ]
        );

        // Prepare notification messages
        $messages = [];
        if ($subscription->wasRecentlyCreated) {
            $messages[] = [
                'type' => 'success',
                'message' => 'Successfully subscribed to updates!'
            ];
        } else {
            if ($travelUnsubscribed) {
                $messages[] = [
                    'type' => 'unsubscribed',
                    'message' => 'Travel',
                ];
            }
            if ($sailingUnsubscribed) {
                $messages[] = [
                    'type' => 'unsubscribed',
                    'message' => 'Sailing',
                ];
            }
            if (!$travelUnsubscribed && !$sailingUnsubscribed && $oldSubscription) {
                $messages[] = [
                    'type' => 'success',
                    'message' => 'Subscription preferences updated successfully!'
                ];
            }
        }

        // Return with appropriate messages
        return back()->with([
            'messages' => $messages,
            'success' => $messages[0]['type'] === 'success' ? $messages[0]['message'] : null,
            'unsubscribed' => $messages[0]['type'] === 'unsubscribed' ? true : null,
            'category' => $messages[0]['type'] === 'unsubscribed' ? $messages[0]['message'] : null,
        ]);
    }

    public function subscribeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email'
        ]);

        $subscription = NewsletterSubscription::create([
            'email' => $request->email,
            'user_id' => auth()->id(),  // Will be null for guests
        ]);

        return response()->json([
            'message' => 'Thank you for subscribing! Please check your email to confirm your subscription.'
        ]);
    }

    public function unsubscribe()
    {
        NewsletterSubscription::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Successfully unsubscribed from the newsletter.');
    }
}

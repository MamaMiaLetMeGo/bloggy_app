<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocationSubscriber;
use App\Models\LocationUpdate;
use App\Notifications\LocationUpdate as LocationUpdateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function show()
    {
        return view('location.show', [
            'garminUrl' => 'https://share.garmin.com/mistie'
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:location_subscribers'
        ]);

        LocationSubscriber::create($validated);

        return back()->with('success', 'Successfully subscribed to location updates!');
    }

    public function unsubscribe(string $email)
    {
        LocationSubscriber::where('email', $email)
            ->update(['is_active' => false]);

        return back()->with('success', 'Successfully unsubscribed from location updates.');
    }

    public function handleGarminWebhook(Request $request)
    {
        // Log incoming webhook data for debugging
        Log::info('Garmin Webhook received', $request->all());

        try {
            // Validate webhook data
            $validatedData = $request->validate([
                'deviceId' => 'required',
                'timestamp' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                // Add other fields based on Garmin's webhook structure
            ]);

            // Store location update
            $location = LocationUpdate::create([
                'device_id' => $validatedData['deviceId'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'timestamp' => Carbon::parse($validatedData['timestamp']),
                'raw_data' => json_encode($request->all())
            ]);

            // Notify subscribers
            LocationSubscriber::where('is_active', true)
                ->chunk(100, function ($subscribers) use ($validatedData) {
                    foreach ($subscribers as $subscriber) {
                        $subscriber->notify(new LocationUpdateNotification($validatedData));
                    }
                });

            return response()->json(['message' => 'Location update processed successfully']);

        } catch (\Exception $e) {
            Log::error('Error processing Garmin webhook', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Failed to process webhook'], 500);
        }
    }
}
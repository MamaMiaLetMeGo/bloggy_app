<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class WelcomeBackController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get posts published since user's last login
        $newPosts = Post::with(['author', 'categories'])
            ->published()
            ->where('published_date', '>', $user->last_login_at ?? $user->created_at)
            ->latest('published_date')
            ->take(5)
            ->get();

        // Check if user has location notifications enabled
        $hasLocationNotifications = $user->location_notifications_enabled ?? false;

        return view('auth.welcome-back', compact('newPosts', 'hasLocationNotifications'));
    }
} 
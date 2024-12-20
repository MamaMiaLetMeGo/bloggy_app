<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $ip = $request->ip();
        $like = $post->likes()->where('ip_address', $ip)->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            $post->likes()->create([
                'ip_address' => $ip
            ]);
            $isLiked = true;
        }

        return response()->json([
            'isLiked' => $isLiked,
            'likesCount' => $post->fresh()->likes_count
        ]);
    }
}

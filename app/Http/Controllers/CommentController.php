<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|min:2',
            'author_name' => Rule::requiredIf(!auth()->check()),
            'author_email' => Rule::requiredIf(!auth()->check()) . '|email',
        ]);

        $comment = new Comment([
            'content' => $request->content,
            'author_name' => auth()->check() ? auth()->user()->name : $request->author_name,
            'author_email' => auth()->check() ? auth()->user()->email : $request->author_email,
            'user_id' => auth()->id(),
            'is_approved' => auth()->check(), // Auto-approve authenticated users
        ]);

        $post->comments()->save($comment);

        return back()->with('success', 'Comment submitted successfully.');
    }

    public function like(Request $request, Comment $comment)
    {
        $ip = $request->ip();
        
        if (!$comment->likes()->where('ip_address', $ip)->exists()) {
            $comment->likes()->create([
                'user_id' => auth()->id(),
                'ip_address' => $ip,
            ]);
            
            $comment->increment('likes_count');
        }

        return response()->json([
            'likes_count' => $comment->likes_count,
        ]);
    }

    public function index(Post $post, Request $request)
    {
        $comments = $post->comments()
            ->approved()
            ->when($request->sort === 'popular', function ($query) {
                return $query->popular();
            })
            ->when($request->sort === 'oldest', function ($query) {
                return $query->oldest();
            })
            ->when(!$request->sort || $request->sort === 'newest', function ($query) {
                return $query->latest();
            })
            ->paginate(15);

        return response()->json($comments);
    }
}

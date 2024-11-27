<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        if (Auth::check()) {
            $validated = $request->validate([
                'content' => ['required', 'string', 'min:2'],
            ]);
        } else {
            $validated = $request->validate([
                'content' => ['required', 'string', 'min:2'],
                'author_name' => ['required', 'string', 'max:255'],
                'author_email' => ['required', 'email', 'max:255'],
            ]);
        }

        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->post_id = $post->id;
        
        if (Auth::check()) {
            $comment->user_id = Auth::id();
            $comment->author_name = Auth::user()->name;
            $comment->author_email = Auth::user()->email;
        } else {
            $comment->author_name = $validated['author_name'];
            $comment->author_email = $validated['author_email'];
        }

        $comment->save();

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment->load('user')
        ]);
    }

    public function index(Request $request, Post $post)
    {
        $sort = $request->input('sort', 'newest');
        $query = $post->comments();

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'popular':
                $query->orderBy('likes_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $comments = $query->paginate(10);

        return response()->json($comments);
    }

    public function like(Comment $comment)
    {
        try {
            $comment->increment('likes_count');
            
            return response()->json([
                'success' => true,
                'likes_count' => $comment->fresh()->likes_count
            ]);
        } catch (\Exception $e) {
            \Log::error('Error liking comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to like comment'
            ], 500);
        }
    }
}

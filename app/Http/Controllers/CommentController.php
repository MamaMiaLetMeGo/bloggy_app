<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\CommentMentionNotification;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        if (Auth::check()) {
            $validated = $request->validate([
                'content' => ['required', 'string', 'min:2'],
                'parent_id' => ['nullable', 'exists:comments,id']
            ]);
        } else {
            $validated = $request->validate([
                'content' => ['required', 'string', 'min:2'],
                'author_name' => ['required', 'string', 'max:255'],
                'author_email' => ['required', 'email', 'max:255'],
                'parent_id' => ['nullable', 'exists:comments,id']
            ]);
        }

        $comment = new Comment();
        $comment->content = $this->processContent($validated['content']);
        $comment->post_id = $post->id;
        $comment->parent_id = $validated['parent_id'] ?? null;
        
        if (Auth::check()) {
            $comment->user_id = Auth::id();
            $comment->author_name = Auth::user()->name;
            $comment->author_email = Auth::user()->email;
            $comment->is_approved = true; // Auto-approve authenticated users
        } else {
            $comment->author_name = $validated['author_name'];
            $comment->author_email = $validated['author_email'];
            $comment->is_approved = false; // Require approval for guest comments
        }

        $comment->save();

        // Notify mentioned users
        $this->notifyMentionedUsers($comment);

        // Load the comment with its parent (if it's a reply)
        $comment->load(['user', 'parent']);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ]);
    }

    public function index(Request $request, Post $post)
    {
        $sort = $request->input('sort', 'newest');
        $query = $post->comments()->whereNull('parent_id'); // Get only top-level comments

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

        // Load nested replies recursively
        $comments = $query->with(['replies.replies', 'user'])->paginate(10);

        return response()->json($comments);
    }

    public function like(Comment $comment)
    {
        $userId = Auth::id();
        $ipAddress = request()->ip();

        // Check if user or IP has already liked
        $existing = DB::table('comment_likes')
            ->where('comment_id', $comment->id)
            ->where(function($query) use ($userId, $ipAddress) {
                $query->where('user_id', $userId)
                    ->orWhere('ip_address', $ipAddress);
            })
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Already liked',
                'likes_count' => $comment->likes_count
            ]);
        }

        DB::table('comment_likes')->insert([
            'comment_id' => $comment->id,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $comment->increment('likes_count');

        return response()->json([
            'success' => true,
            'likes_count' => $comment->fresh()->likes_count
        ]);
    }

    public function commenters(Post $post)
    {
        // Get unique commenters on this post
        $commenters = Comment::where('post_id', $post->id)
            ->select('author_name', 'user_id')
            ->distinct()
            ->get()
            ->map(function ($comment) {
                return [
                    'name' => $comment->author_name,
                    'id' => $comment->user_id
                ];
            });

        return response()->json($commenters);
    }

    private function processContent($content)
    {
        // Convert @mentions to links
        return preg_replace_callback('/@(\w+)/', function($matches) {
            $username = $matches[1];
            $user = User::where('name', $username)->first();
            if ($user) {
                return sprintf('<a href="%s" class="text-blue-600 hover:text-blue-800">@%s</a>', 
                    route('users.profile', $user),
                    $username
                );
            }
            return $matches[0];
        }, $content);
    }

    private function notifyMentionedUsers(Comment $comment)
    {
        preg_match_all('/@(\w+)/', $comment->content, $matches);
        
        if (!empty($matches[1])) {
            $mentionedUsers = User::whereIn('name', $matches[1])->get();
            
            foreach ($mentionedUsers as $user) {
                // Don't notify if the user is mentioning themselves
                if (!$comment->user_id || $user->id !== $comment->user_id) {
                    $user->notify(new CommentMentionNotification($comment));
                }
            }
        }
    }
}

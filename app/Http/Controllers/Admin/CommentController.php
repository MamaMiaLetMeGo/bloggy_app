<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['post', 'user'])
            ->when($request->status === 'pending', function ($query) {
                return $query->where('is_approved', false);
            })
            ->when($request->status === 'approved', function ($query) {
                return $query->where('is_approved', true);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('content', 'LIKE', "%{$search}%")
                    ->orWhereHas('post', function ($q) use ($search) {
                        $q->where('title', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Comment::count(),
            'pending' => Comment::where('is_approved', false)->count(),
            'approved' => Comment::where('is_approved', true)->count(),
        ];

        return view('admin.comments.index', compact('comments', 'stats'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        // Optionally notify the comment author
        // $comment->author->notify(new CommentApprovedNotification($comment));

        return back()->with('success', 'Comment approved successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'comments' => 'required|array',
            'action' => 'required|in:approve,delete'
        ]);

        $comments = Comment::whereIn('id', $validated['comments']);

        if ($validated['action'] === 'approve') {
            $comments->update(['is_approved' => true]);
            $message = 'Selected comments approved successfully.';
        } else {
            $comments->delete();
            $message = 'Selected comments deleted successfully.';
        }

        return back()->with('success', $message);
    }

    public function spam(Comment $comment)
    {
        $comment->markAsSpam();
        $comment->delete();

        // Optionally ban the IP address
        // SpamList::create(['ip_address' => $comment->ip_address]);

        return back()->with('success', 'Comment marked as spam and deleted.');
    }

    public function show(Comment $comment)
    {
        return view('admin.comments.show', [
            'comment' => $comment->load(['post', 'user', 'replies']),
            'userComments' => $comment->user 
                ? $comment->user->comments()->latest()->limit(5)->get() 
                : collect(),
            'ipComments' => Comment::where('ip_address', $comment->ip_address)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
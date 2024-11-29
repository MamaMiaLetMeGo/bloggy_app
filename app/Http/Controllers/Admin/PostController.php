<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = Post::with(['categories', 'author'])
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'users'));
    }


    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:posts',
            'body_content' => 'required',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'author_id' => 'required|exists:users,id',
            'featured_image' => 'nullable|image|max:2048' // validate image upload
        ]);

        // Handle featured image upload
        $featured_image = null;
        if ($request->hasFile('featured_image')) {
            $featured_image = $request->file('featured_image')->store('featured-images', 'public');
        }
        $video = null;
        if ($request->hasFile('video')) {
            $video = $request->file('video')->store('videos', 'public');
        }

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body_content' => $validated['body_content'],
            'author_id' => $validated['author_id'],
            'breadcrumb' => $request->breadcrumb,
            'featured_image' => $featured_image,  // Store the path
            'video' => $video,
            'published_date' => $request->published_date,
            'status' => 'draft',
        ]);

        $post->categories()->attach($request->categories);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
        {
            if (! Gate::allows('update', $post)) {
                abort(403);
        }
        
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories', 'users'));
        }

        public function update(Request $request, Post $post)
        {
            if (! Gate::allows('update', $post)) {
                abort(403);
            }
            
            $validated = $request->validate([
                'title' => 'required',
                'body_content' => 'required',
                'slug' => 'required|unique:posts,slug,' . $post->id,
                'author_id' => 'required|exists:users,id',
                'featured_image' => 'nullable|image|max:2048'
            ]);

            // Handle image update
            $featured_image = $post->featured_image; // Keep existing image by default

            if ($request->boolean('remove_image')) {
                // Remove existing image if it exists
                if ($featured_image && Storage::disk('public')->exists($featured_image)) {
                    Storage::disk('public')->delete($featured_image);
                }
                $featured_image = null;
            } elseif ($request->hasFile('featured_image')) {
                // Remove old image if it exists
                if ($featured_image && Storage::disk('public')->exists($featured_image)) {
                    Storage::disk('public')->delete($featured_image);
                }
                // Store new image
                $featured_image = $request->file('featured_image')->store('featured-images', 'public');
            }

            $video = $post->video;
            if ($request->hasFile('video')) {
                // Remove the existing video
                if ($post->video_path) {
                    $existingVideoPath = storage_path('app/public/' . $post->video_path);
        
                    if (file_exists($existingVideoPath)) {
                        unlink($existingVideoPath);
                    }
                }
        
                // Upload the new video
                $video = $request->file('video')->store('videos', 'public');
            }
            $post->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'body_content' => $validated['body_content'],
                'author_id' => $validated['author_id'],
                'breadcrumb' => $request->breadcrumb,
                'featured_image' => $featured_image,
                'video' => $video,
                'published_date' => $request->published_date,
            ]);

            if ($request->has('categories')) {
                $post->categories()->sync($request->categories);
            }

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'Post updated successfully');
        }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_date' => $post->published_date ?? now()
        ]);

        return back()->with('success', 'Post published successfully.');
    }

    public function unpublish(Post $post)
    {
        $post->update([
            'status' => 'draft',
            'published_date' => null
        ]);

        return back()->with('success', 'Post unpublished successfully.');
    }

    public function archive(Post $post)
    {
        $post->update([
            'status' => 'archived'
        ]);

        return back()->with('success', 'Post archived successfully.');
    }

    public function videoUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:mp4,mov,avi,wmv|max:51200' // 50MB max size
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('videos', 'public');

            return response()->json([
                'location' => asset('storage/' . $path) // URL for the uploaded video
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');

            return response()->json([
                'location' => asset('storage/' . $path) // URL for the uploaded image
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

}
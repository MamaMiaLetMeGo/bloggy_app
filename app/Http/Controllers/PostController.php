<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author', 'categories'])
            ->where('status', 'published')
            ->latest('published_date')
            ->paginate(12);

        return view('posts.index', compact('posts'));
    }

    public function show(Category $category, Post $post)
    {
        if (!$post->categories->contains($category)) {
            abort(404);
        }

        $relatedPosts = Post::with(['author', 'categories'])
            ->published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->latest('published_date')
            ->take(3)
            ->get();

        return view('posts.show', [
            'post' => $post->load(['author', 'categories']),
            'category' => $category,
            'relatedPosts' => $relatedPosts
        ]);
    }

    public function category(Category $category)
    {
        $posts = Post::with(['author', 'categories'])
            ->where('status', 'published')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('id', $category->id);
            })
            ->latest('published_date')
            ->paginate(12);

        return view('posts.index', compact('posts', 'category'));
    }
}
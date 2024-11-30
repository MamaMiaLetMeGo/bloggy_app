<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->get();
        $posts = Post::with('categories')
            ->whereNotNull('published_date')
            ->latest('published_date')
            ->get();
        
        $users = User::withCount('comments')
            ->latest()
            ->get();
        
        $publishedPostsCount = Post::whereNotNull('published_date')->count();
        $categoriesCount = Category::count();

        return view('admin.dashboard', compact(
            'categories',
            'posts',
            'users',
            'publishedPostsCount',
            'categoriesCount'
        ));
    }
}
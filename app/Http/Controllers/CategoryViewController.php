<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CategoryViewController extends Controller
{
   public function index(Request $request)
   {
       $query = Category::withCount(['posts' => function($query) {
               $query->published();
           }]);

       // Handle search
       if ($request->filled('search')) {
           $query->where(function($q) use ($request) {
               $q->where('name', 'like', '%' . $request->search . '%')
                 ->orWhere('description', 'like', '%' . $request->search . '%');
           });
       }

       // Handle sorting
       if ($request->sort === 'posts') {
           $query->orderByDesc('posts_count');
       } else {
           $query->orderBy('name');
       }

       $categories = $query->paginate(12)->withQueryString();

       return view('categories.index', compact('categories'));
   }

   public function show(Category $category, Request $request)
   {
       $sort = $request->query('sort', 'likes'); // Default to most-liked

       // Start with base query
       $query = $category->posts()
           ->with(['author', 'categories'])
           ->published();

       // Apply sorting
       switch ($sort) {
           case 'likes':
               $query->withCount('likes')
                   ->orderBy('likes_count', 'desc');
               break;
           case 'comments':
               $query->withCount('comments')
                   ->orderBy('comments_count', 'desc');
               break;
           case 'oldest':
               $query->orderBy('published_date', 'asc');
               break;
           case 'newest':
               $query->orderBy('published_date', 'desc');
               break;
           default:
               $query->withCount('likes')
                   ->orderBy('likes_count', 'desc');
       }

       // Debug the query
       \Log::info('Final query:', [
           'sort' => $sort,
           'sql' => $query->toSql(),
           'bindings' => $query->getBindings()
       ]);

       $posts = $query->paginate(9)->withQueryString();

       // Debug the results
       \Log::info('Results:', [
           'total' => $posts->total(),
           'posts' => $posts->map(fn($post) => [
               'id' => $post->id,
               'title' => $post->title,
               'likes_count' => $post->likes_count ?? 0,
               'comments_count' => $post->comments_count ?? 0
           ])
       ]);

       $recentPosts = $category->posts()
           ->with('author')
           ->withCount('likes')
           ->published()
           ->latest('published_date')
           ->take(10)
           ->get();

       $relatedCategories = Category::whereHas('posts', function($query) use ($category) {
               $query->whereIn('posts.id', $category->posts->pluck('id'));
           })
           ->where('id', '!=', $category->id)
           ->withCount(['posts' => function($query) {
               $query->published();
           }])
           ->orderByDesc('posts_count')
           ->limit(5)
           ->get();
       
       return view('categories.show', [
           'category' => $category,
           'posts' => $posts,
           'relatedCategories' => $relatedCategories,
           'recentPosts' => $recentPosts,
           'currentSort' => $sort
       ]);
   }

   public function search(Request $request)
   {
       $query = $request->get('q');
       
       $categories = Category::where(function($q) use ($query) {
           if ($query) {
               $q->where('name', 'ILIKE', "%{$query}%")
                 ->orWhere('description', 'ILIKE', "%{$query}%");
           }
       })
       ->withCount(['posts' => function($query) {
           $query->published();
       }])
       ->orderBy('name')
       ->paginate(12)
       ->withQueryString();
       
       return view('categories.index', compact('categories', 'query'));
   }
}
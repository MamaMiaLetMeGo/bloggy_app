<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryViewController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\AuthorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard redirect route
Route::get('/dashboard', function () {
   return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Public routes
Route::get('/', function () {
   $posts = Post::with(['author', 'categories'])
       ->published()
       ->latest('published_date')
       ->take(6)
       ->get();

   return view('home', compact('posts'));
})->name('home');


// Public post routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/category/{category:slug}', [PostController::class, 'category'])->name('posts.category');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Public category routes
Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');
Route::get('/categories/search', [CategoryViewController::class, 'search'])->name('categories.search');
Route::get('/categories/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');

// Public author routes 
Route::get('/author/{user:id}', [AuthorController::class, 'show'])->name('author.show');

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
   // Dashboard
   Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
   
   // Posts Management
   Route::resource('posts', AdminPostController::class);
   Route::post('/upload-video', [AdminPostController::class, 'videoUpload'])->name('video.upload');
   Route::post('/upload-image', [AdminPostController::class, 'uploadImages'])->name('image.upload');



   // Category Management
   Route::resource('categories', AdminCategoryController::class);
   
   // Post Status Management
   Route::post('/posts/{post}/publish', [AdminPostController::class, 'publish'])->name('posts.publish');
   Route::post('/posts/{post}/unpublish', [AdminPostController::class, 'unpublish'])->name('posts.unpublish');
   Route::post('/posts/{post}/archive', [AdminPostController::class, 'archive'])->name('posts.archive');

   // Category Featured Toggle
   Route::post('categories/{category}/toggle-featured', [AdminCategoryController::class, 'toggleFeatured'])
       ->name('categories.toggleFeatured');
});

// Profile routes
Route::middleware('auth')->group(function () {
   Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
   Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
   Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
   Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
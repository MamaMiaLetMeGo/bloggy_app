<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryViewController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Static routes first
Route::get('/', function () {
   $posts = Post::with(['author', 'categories'])
       ->published()
       ->latest('published_date')
       ->take(6)
       ->get();

   return view('home', compact('posts'));
})->name('home');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Other static routes
Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

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
   Route::post('/categories/upload-image', [AdminCategoryController::class, 'uploadImages'])->name('categories.image.upload');
   
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

// Add these author routes before the dynamic routes
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/author/{user}', [AuthorController::class, 'show'])->name('authors.show');

// Author dashboard routes (for authenticated authors)
Route::middleware('auth')->group(function () {
    Route::get('/author/dashboard', [AuthorController::class, 'dashboard'])->name('author.dashboard');
    Route::get('/author/edit', [AuthorController::class, 'edit'])->name('author.edit');
    Route::patch('/author/update', [AuthorController::class, 'update'])->name('author.update');
});

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store')
    ->middleware('throttle:60,1'); // Rate limiting

Route::get('/posts/{post}/comments', [CommentController::class, 'index'])
    ->name('comments.index');

Route::post('/comments/{comment}/like', [CommentController::class, 'like'])
    ->name('comments.like')
    ->middleware('throttle:60,1');

Route::get('/posts/{post}/commenters', [CommentController::class, 'commenters'])
->name('posts.commenters');

// Admin comment management
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});

// Dynamic routes last (keep these at the bottom)
Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');

require __DIR__.'/auth.php';
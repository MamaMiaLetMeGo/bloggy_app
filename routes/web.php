<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryViewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WelcomeBackController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Controllers\Auth\TwoFactorChallengeController;

// Include auth routes
require __DIR__.'/auth.php';

// Public routes (no auth required)
Route::middleware('web')->group(function () {
    // Newsletter subscription for guests
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
    });

    // Static routes and public routes
    Route::get('/', function () {
        $posts = Post::with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->take(6)
            ->get();

        return view('home', compact('posts'));
    })->name('home');

    // Post routes (public)
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    // Post likes (no auth required)
    Route::post('/posts/{post}/like', [PostLikeController::class, 'toggle'])->name('posts.like');

    // Post comments
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('posts.comments.index');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{post}/commenters', [CommentController::class, 'commenters'])->name('posts.commenters');

    // Fixed routes (non-dynamic segments)
    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/contact/verify', [ContactController::class, 'verify'])->name('contact.verify');
    Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.slug.show');

    // Author routes
    Route::get('/authors/{user}', [ProfileController::class, 'show'])->name('authors.show');

    // Profile routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/2fa', [ProfileController::class, 'showTwoFactor'])->name('profile.2fa.show');
        Route::post('/profile/2fa', [ProfileController::class, 'enableTwoFactor'])->name('profile.2fa.enable');
        Route::delete('/profile/2fa', [ProfileController::class, 'disableTwoFactor'])->name('profile.2fa.disable');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Location routes
    Route::prefix('location')->name('location.')->group(function () {
        Route::get('/', [LocationController::class, 'show'])->name('show');
        Route::post('/subscribe', [LocationController::class, 'subscribe'])->name('subscribe');
        Route::get('/unsubscribe/{email}', [LocationController::class, 'unsubscribe'])->name('unsubscribe');
    });

    // Webhook routes
    Route::post('/webhooks/garmin', [LocationController::class, 'handleGarminWebhook'])->name('webhook.garmin');

    // Catch-all routes for posts and categories (must be last)
    Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('posts.category.show');
    Route::get('/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');
});

// Development only routes
if (app()->environment('local')) {
    Route::get('/test-ip', function () {
        dd([
            'ip()' => request()->ip(),
            'getClientIp' => request()->getClientIp(),
            'server.REMOTE_ADDR' => request()->server('REMOTE_ADDR'),
            'headers' => request()->headers->all(),
        ]);
    });
}
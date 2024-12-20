<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryViewController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WelcomeBackController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PostLikeController; // Add this line

Route::middleware('web')->group(function () {
    // Include auth and admin routes
    require __DIR__.'/auth.php';
    require __DIR__.'/admin.php';

    // Static routes and public routes
    Route::get('/', function () {
        $posts = Post::with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->take(6)
            ->get();

        return view('home', compact('posts'));
    })->name('home');

    // Post likes (no auth required)
    Route::post('/posts/{post}/like', [PostLikeController::class, 'toggle'])->name('posts.like');

    // Post comments
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('posts.comments');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{post}/commenters', [CommentController::class, 'commenters'])->name('posts.commenters');

    // Fixed routes (non-dynamic segments)
    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');

    // Location routes
    Route::prefix('location')->name('location.')->group(function () {
        Route::get('/', [LocationController::class, 'show'])->name('show');
        Route::post('/subscribe', [LocationController::class, 'subscribe'])->name('subscribe');
        Route::get('/unsubscribe/{email}', [LocationController::class, 'unsubscribe'])->name('unsubscribe');
    });

    // Webhook routes
    Route::post('/webhooks/garmin', [LocationController::class, 'handleGarminWebhook'])->name('webhook.garmin');

    // 2FA verification routes (without 2FA middleware)
    Route::middleware(['auth'])->group(function () {
        Route::prefix('2fa')->name('2fa.')->group(function () {
            Route::get('/', [TwoFactorChallengeController::class, 'create'])->name('challenge');
            Route::post('/', [TwoFactorChallengeController::class, 'store'])->name('verify');
            Route::get('/recovery', [TwoFactorChallengeController::class, 'showRecoveryForm'])->name('recovery');
            Route::post('/recovery', [TwoFactorChallengeController::class, 'recovery'])->name('recovery.store');
        });

    });

    // Protected routes that require 2FA
    Route::middleware(['auth', 'two-factor'])->group(function () {
        // Move admin routes inside this group
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            // Update to use the correct namespace for CategoryController
            Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
            Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
            // ... any other admin routes
        });

        // Existing protected routes
        Route::get('/welcome', [WelcomeController::class, 'newUser'])->name('welcome.new-user');
        Route::get('/welcome-back', [WelcomeBackController::class, 'index'])->name('welcome.back');
        
        // Newsletter routes
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
            Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
            Route::get('/security', [SecurityController::class, 'show'])->name('security');

            // 2FA Profile Settings
            Route::prefix('2fa')->name('2fa.')->group(function () {
                Route::get('/', [TwoFactorAuthController::class, 'show'])->name('show');
                Route::post('/enable', [TwoFactorAuthController::class, 'enable'])->name('enable');
                Route::post('/disable', [TwoFactorAuthController::class, 'disable'])->name('disable');
                Route::get('/recovery-codes', [TwoFactorAuthController::class, 'showRecoveryCodes'])->name('recovery-codes');
                Route::post('/recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('recovery-codes.regenerate');
            });
        });

        // Author routes
        Route::prefix('authors')->name('authors.')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('index');
            Route::get('/{user}', [AuthorController::class, 'show'])->name('show');
            Route::get('/dashboard', [AuthorController::class, 'dashboard'])->name('dashboard');
            Route::get('/edit', [AuthorController::class, 'edit'])->name('edit');
            Route::patch('/update', [AuthorController::class, 'update'])->name('update');
        });

        // Comment routes
        Route::prefix('comments')->name('comments.')->group(function () {
            Route::get('/{post}', [CommentController::class, 'index'])->name('index');
            Route::middleware('throttle:60,1')->group(function () {
                Route::post('/{post}', [CommentController::class, 'store'])->name('store');
                Route::post('/{comment}/like', [CommentController::class, 'like'])->name('like');
            });
        });

        
    });

    // Catch-all routes for posts and categories (must be last)
    Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('posts.show');
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
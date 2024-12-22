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

// Public routes (no auth required)
Route::middleware('web')->group(function () {
    // Newsletter subscription for guests
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribeEmail'])
        ->name('newsletter.subscribe');

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
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('posts.comments');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{post}/commenters', [CommentController::class, 'commenters'])->name('posts.commenters');

    // Fixed routes (non-dynamic segments)
    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/contact/verify', [ContactController::class, 'verify'])->name('contact.verify');
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

    // Catch-all routes for posts and categories (must be last)
    Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');
});

// Auth required routes
Route::middleware(['web', 'auth'])->group(function () {
    // Include auth and admin routes
    require __DIR__.'/auth.php';
    require __DIR__.'/admin.php';

    // Email verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('profile.edit')->with('status', 'Your email has been verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Newsletter routes for authenticated users
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::post('/preferences', [NewsletterController::class, 'subscribe'])->name('preferences');
        Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
    });

    // Protected routes that require email verification
    Route::middleware('verified')->group(function () {
        // Profile routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Blog post routes
        Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Comment routes
        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Like routes
        Route::post('/posts/{post}/like', [PostLikeController::class, 'store'])->name('likes.store');
        Route::delete('/posts/{post}/like', [PostLikeController::class, 'destroy'])->name('likes.destroy');

        // Move admin routes inside this group
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
            // Update to use the correct namespace for CategoryController
            Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
            Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
            // ... any other admin routes
        });

        // Existing protected routes
        Route::get('/welcome', [WelcomeController::class, 'newUser'])->name('welcome.new-user');
        Route::get('/welcome-back', [WelcomeBackController::class, 'index'])->name('welcome.back');

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

        // 2FA verification routes (without 2FA middleware)
        Route::prefix('2fa')->name('2fa.')->group(function () {
            Route::get('/', [TwoFactorChallengeController::class, 'create'])->name('challenge');
            Route::post('/', [TwoFactorChallengeController::class, 'store'])->name('verify');
            Route::get('/recovery', [TwoFactorChallengeController::class, 'showRecoveryForm'])->name('recovery');
            Route::post('/recovery', [TwoFactorChallengeController::class, 'recovery'])->name('recovery.store');
        });
    });
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
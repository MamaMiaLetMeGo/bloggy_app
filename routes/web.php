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

Route::middleware('web')->group(function () {
    // Include auth and admin routes
    require __DIR__.'/auth.php';
    require __DIR__.'/admin.php';

    // Static routes
    Route::get('/', function () {
        $posts = Post::with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->take(6)
            ->get();

        return view('home', compact('posts'));
    })->name('home');

    // Public routes
    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');

    // Location routes
    Route::prefix('location')->name('location.')->group(function () {
        Route::get('/', [LocationController::class, 'show'])->name('show');
        Route::post('/subscribe', [LocationController::class, 'subscribe'])->name('subscribe');
        Route::get('/unsubscribe/{email}', [LocationController::class, 'unsubscribe'])->name('unsubscribe');
    });

    // Webhook routes
    Route::post('/webhooks/garmin', [LocationController::class, 'handleGarminWebhook'])->name('webhook.garmin');

    // Auth required routes
    Route::middleware('auth')->group(function () {
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
        });
    });

    // Author routes
    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/', [AuthorController::class, 'index'])->name('index');
        Route::get('/{user}', [AuthorController::class, 'show'])->name('show');
        
        // Auth required author routes
        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', [AuthorController::class, 'dashboard'])->name('dashboard');
            Route::get('/edit', [AuthorController::class, 'edit'])->name('edit');
            Route::patch('/update', [AuthorController::class, 'update'])->name('update');
        });
    });

    // Comment routes
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/{post}', [CommentController::class, 'index'])->name('index');
        Route::middleware(['auth', 'throttle:60,1'])->group(function () {
            Route::post('/{post}', [CommentController::class, 'store'])->name('store');
            Route::post('/{comment}/like', [CommentController::class, 'like'])->name('like');
        });
    });

    // Keep these at the bottom (catch-all routes)
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
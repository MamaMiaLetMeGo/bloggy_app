<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\IsAdmin;

// Debug route loading
Log::info('Admin routes are being loaded');

Route::middleware(['web', 'auth', \App\Http\Middleware\IsAdmin::class, 'two-factor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Debug route access
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Posts Management
        Route::resource('posts', PostController::class);
        Route::post('/upload-video', [PostController::class, 'videoUpload'])->name('video.upload');
        Route::post('/upload-image', [PostController::class, 'uploadImages'])->name('image.upload');
        Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
        Route::post('/posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');
        Route::post('/posts/{post}/archive', [PostController::class, 'archive'])->name('posts.archive');

        // Category Management
        Route::resource('categories', CategoryController::class);
        Route::post('/categories/upload-image', [CategoryController::class, 'uploadImages'])->name('categories.image.upload');
        Route::post('categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])
            ->name('categories.toggleFeatured');

        // Comments Management
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // User Management
        Route::post('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])
            ->name('users.toggle-admin');
});
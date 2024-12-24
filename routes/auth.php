<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Middleware\IPBlocklist;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:registration');
    
    // Login routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login.form')
        ->middleware('guest');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(['throttle:login', IPBlocklist::class])
        ->name('login.submit');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::match(['POST', 'PUT'], 'reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    Route::get('/login/code', [AuthenticatedSessionController::class, 'showCodeForm'])
        ->name('login.code');
    Route::post('/login/code/verify', [AuthenticatedSessionController::class, 'verifyCode'])
        ->name('login.code.verify');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [EmailVerificationNotificationController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Two Factor Authentication
    Route::get('2fa/challenge', [TwoFactorChallengeController::class, 'create'])
        ->name('2fa.challenge');
    Route::post('2fa/challenge', [TwoFactorChallengeController::class, 'store']);
    Route::get('2fa/recovery', [TwoFactorChallengeController::class, 'showRecoveryForm'])
        ->name('2fa.recovery');
    Route::post('2fa/recovery', [TwoFactorChallengeController::class, 'recovery']);

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update.profile');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ReCaptcha\ReCaptcha;

class ReCaptchaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReCaptcha::class, function ($app) {
            return new ReCaptcha(config('services.recaptcha.secret_key'));
        });
    }
}
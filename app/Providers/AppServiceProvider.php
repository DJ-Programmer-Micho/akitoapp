<?php

namespace App\Providers;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton('glocales', function () {
            return config('app.locales'); // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('cloudfront', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('userImg', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/users/user.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('logo_72', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/web-setting/72.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('logo_114', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/web-setting/114.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('logo_144', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/web-setting/144.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('logo_57', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/web-setting/57.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
        $this->app->singleton('logo_1024', function () {
            return 'https://d1gdghw8f3v9rw.cloudfront.net/web-setting/1024.png'; // Replace "abc" with your desired value or logic to fetch the data.
        });
    }
}

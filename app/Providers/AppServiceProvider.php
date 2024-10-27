<?php

namespace App\Providers;

use App\Models\WebSetting;
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
        $settings = WebSetting::find(1); // Assuming there's only one settings record

        // Fallback if settings not found
        $settings = $settings ?? new WebSetting();

        $this->app->singleton('glocales', function () {
            return config('app.locales'); 
        });

        $this->app->singleton('phoneNumber', function () use ($settings) {
            return $settings->phone_number ?? '009647507747742'; // Fallback to default
        });

        $this->app->singleton('emailAddress', function () use ($settings) {
            return $settings->email_address ?? 'default_email@example.com'; // Fallback to default
        });

        $this->app->singleton('address', function () use ($settings) {
            return $settings->address ?? 'Erbil Ankawa'; // Fallback to default
        });

        $this->app->singleton('workingDays', function () use ($settings) {
            return $settings->working_days ?? 'Default working days'; // Fallback to default
        });

        $this->app->singleton('workingTime', function () use ($settings) {
            return $settings->working_time ?? 'Default working time'; // Fallback to default
        });

        $this->app->singleton('facebookUrl', function () use ($settings) {
            return $settings->facebook_url ?? 'https://facebook.com'; // Fallback to default
        });

        $this->app->singleton('instagramUrl', function () use ($settings) {
            return $settings->instagram_url ?? 'https://instagram.com'; // Fallback to default
        });

        $this->app->singleton('tiktokUrl', function () use ($settings) {
            return $settings->tiktok_url ?? 'https://tiktok.com'; // Fallback to default
        });

        $this->app->singleton('cloudfront', function () {
            return 'https://d1h4q8vrlfl3k9.cloudfront.net/';
        });

        $this->app->singleton('userImg', function () {
            return 'https://d1h4q8vrlfl3k9.cloudfront.net/users/user.png';
        });

        $this->app->singleton('logo_72', function () use ($settings) {
            return $this->getLogoUrl($settings->app_icon);
        });
        $this->app->singleton('logo_114', function () use ($settings) {
            return $this->getLogoUrl($settings->app_icon);
        });
        $this->app->singleton('logo_144', function () use ($settings) {
            return $this->getLogoUrl($settings->app_icon);
        });
        $this->app->singleton('logo_57', function () use ($settings) {
            return $this->getLogoUrl($settings->app_icon);
        });
        $this->app->singleton('logo_1024', function () use ($settings) {
            return $this->getLogoUrl($settings->app_icon);
        });
    }

    /**
     * Helper method to get the logo URL
     */
    private function getLogoUrl(?string $appIcon): string
    {
        return 'https://d1h4q8vrlfl3k9.cloudfront.net/' . ($appIcon ?? 'default_logo.png');
    }
}

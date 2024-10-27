<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\WebSetting;

class EmailConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Ensure the `web_settings` table exists to prevent errors during migrations
        if (Schema::hasTable('web_settings')) {
            // Fetch the settings
            $settings = WebSetting::find(1); // Assuming you have only one settings record

            if ($settings) {
                Config::set('mail.mailers.smtp.host', $settings->email_host ?? env('MAIL_HOST'));
                Config::set('mail.mailers.smtp.port', $settings->email_port ?? env('MAIL_PORT'));
                Config::set('mail.mailers.smtp.username', $settings->email_username ?? env('MAIL_USERNAME'));
                Config::set('mail.mailers.smtp.password', $settings->email_password ?? env('MAIL_PASSWORD'));
                Config::set('mail.mailers.smtp.encryption', $settings->email_encryption ?? env('MAIL_ENCRYPTION'));
                Config::set('mail.from.address', $settings->email_from_address ?? env('MAIL_FROM_ADDRESS'));
                Config::set('mail.from.name', $settings->email_from_name ?? env('MAIL_FROM_NAME'));
            }
        }
    }
}

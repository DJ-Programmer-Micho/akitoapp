<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_settings', function (Blueprint $table) {
            $table->id();
            
            // Basic settings
            $table->string('logo_image')->nullable();
            $table->string('app_icon')->nullable();
            
            // Email configuration
            $table->string('email_mailer')->nullable();
            $table->string('email_host')->nullable();
            $table->string('email_port')->nullable();
            $table->string('email_username')->nullable();
            $table->string('email_password')->nullable();
            $table->string('email_encryption')->nullable();
            $table->string('email_from_address')->nullable();
            $table->string('email_from_name')->nullable();

            // Hero images in different languages
            $table->json('hero_images')->nullable();
            $table->json('banner_images')->nullable();

            // Google reCAPTCHA keys
            $table->string('google_recaptcha_key')->nullable();
            $table->string('google_recaptcha_secret')->nullable();
            
            // Additional settings
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('snapchat_url')->nullable();


            $table->string('email_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_number_2')->nullable();
            $table->string('address')->nullable();
            $table->string('working_days')->nullable();
            $table->string('working_time')->nullable();
            $table->boolean('is_maintenance_mode')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_settings');
    }
};

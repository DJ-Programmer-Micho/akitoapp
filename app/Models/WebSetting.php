<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'logo_image',
        'app_icon',
        'email_mailer',
        'email_host',
        'email_port',
        'email_username',
        'email_password',
        'email_encryption',
        'email_from_address',
        'email_from_name',
        'hero_images',
        'banner_images',
        'google_recaptcha_key',
        'google_recaptcha_secret',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'email_address',
        'phone_number',
        'address',
        'working_days', // Sat - Thu
        'working_time', // 9am - 7pm UTC +3
        'is_maintenance_mode',
    ];
}

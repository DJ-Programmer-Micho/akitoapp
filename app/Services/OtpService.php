<?php

namespace App\Services;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    public static function makeCode(int $digits = 6): string
    {
        $min = (int) pow(10, $digits - 1);
        $max = (int) pow(10, $digits) - 1;
        return (string) random_int($min, $max);
    }

    protected static function key(string $scope, int $customerId, string $type): string
    {
        return "otp:{$scope}:{$customerId}:{$type}";
    }

    protected static function setCooldown(string $scope, int $customerId): void
    {
        $cooldown = (int) config('otp.resend_cooldown', 60);
        Cache::put(self::key($scope, $customerId, 'cooldown'), 1, $cooldown);
        Cache::put(self::key($scope, $customerId, 'cooldown') . ':ttl', time() + $cooldown, $cooldown);
    }

    protected static function setExpiry(string $scope, int $customerId): void
    {
        $mins = (int) config('otp.expires_minutes', 10);
        Cache::put(self::key($scope, $customerId, 'expires'), 1, now()->addMinutes($mins));
        Cache::put(self::key($scope, $customerId, 'expires') . ':ttl', time() + ($mins * 60), now()->addMinutes($mins));
    }

    protected static function isCoolingDown(string $scope, int $customerId): bool
    {
        return Cache::has(self::key($scope, $customerId, 'cooldown'));
    }

    protected static function isExpired(string $scope, int $customerId): bool
    {
        return !Cache::has(self::key($scope, $customerId, 'expires'));
    }

    public static function secondsLeft(string $scope, int $customerId, string $type): int
    {
        $ttl = Cache::get(self::key($scope, $customerId, $type) . ':ttl');
        return $ttl ? max(0, $ttl - time()) : 0;
    }

    // ---------- email ----------
    public static function sendEmailOtp(Customer $customer): array
    {
        $id = $customer->id;

        if (self::isCoolingDown('email', $id)) {
            return ['ok' => false, 'reason' => 'cooldown', 'retry_in' => self::secondsLeft('email', $id, 'cooldown')];
        }

        $code = self::makeCode(6);
        $customer->email_otp_number = $code;
        $customer->save();

        self::setCooldown('email', $id);
        self::setExpiry('email', $id);

        Mail::to($customer->email)->send(new \App\Mail\EmailVerificationMail($code));

        return ['ok' => true];
    }

    public static function verifyEmailOtp(Customer $customer, string $entered): array
    {
        if (!$customer->email_otp_number) return ['ok' => false, 'reason' => 'no_code'];
        if (self::isExpired('email', $customer->id)) return ['ok' => false, 'reason' => 'expired'];

        if (hash_equals($customer->email_otp_number, trim($entered))) {
            $customer->email_verify = 1;
            $customer->email_otp_number = null;
            $customer->email_verified_at = now();
            $customer->save();

            Cache::forget(self::key('email', $customer->id, 'expires'));
            Cache::forget(self::key('email', $customer->id, 'expires') . ':ttl');
            return ['ok' => true];
        }
        return ['ok' => false, 'reason' => 'mismatch'];
    }

    // ---------- phone ----------
    protected static function formatMsisdn(string $raw): string
    {
        $raw = trim($raw);
        if (Str::startsWith($raw, '00')) $raw = '+' . ltrim($raw, '0');
        return $raw;
    }

    protected static function phoneMessage(string $code, ?string $lang = null): string
    {
        $lang = $lang ?: config('otp.default_lang', 'en');
        $mins = (int) config('otp.expires_minutes', 10);

        return match ($lang) {
            'ar'      => "رمز التحقق الخاص بك هو: {$code}. ينتهي خلال {$mins} دقائق. لا تشاركه مع أحد.",
            'ku','ckb'=> "کۆدی پشت‌دەرکردنەکەت: {$code}. دەکۆتەوە لە {$mins} خولەکدا. تکایە پێوە مەبڵا.",
            default   => "Your verification code is: {$code}. It expires in {$mins} minutes. Do not share it.",
        };
    }

    public static function sendPhoneOtp(Customer $customer, ?string $channel = null, ?string $lang = null): array
    {
        $id   = $customer->id;
        $chan = $channel ?: config('otp.phone_channel', 'whatsapp');
        $lang = $lang ?: config('otp.default_lang', 'en');

        if (self::isCoolingDown('phone', $id)) {
            return ['ok' => false, 'reason' => 'cooldown', 'retry_in' => self::secondsLeft('phone', $id, 'cooldown')];
        }

        $code = self::makeCode(6);
        $to   = self::formatMsisdn($customer->customer_profile->phone_number);
        $msg  = self::phoneMessage($code, $lang);

        $resp = StandingTechService::sendMessage(
            recipient: $to,
            channel:  $chan,  // whatsapp|telegram|sms
            message:  $msg,
            lang:     $lang
        );

        if ($resp->successful()) {
            $customer->phone_otp_number = $code;
            $customer->save();

            self::setCooldown('phone', $id);
            self::setExpiry('phone', $id);

            return ['ok' => true];
        }

        return ['ok' => false, 'reason' => 'gateway', 'status' => $resp->status(), 'body' => $resp->json()];
    }

    public static function verifyPhoneOtp(Customer $customer, string $entered): array
    {
        if (!$customer->phone_otp_number) return ['ok' => false, 'reason' => 'no_code'];
        if (self::isExpired('phone', $customer->id)) return ['ok' => false, 'reason' => 'expired'];

        if (hash_equals($customer->phone_otp_number, trim($entered))) {
            $customer->phone_verify = 1;
            $customer->phone_verified_at = now();
            $customer->phone_otp_number = null;
            $customer->save();

            Cache::forget(self::key('phone', $customer->id, 'expires'));
            Cache::forget(self::key('phone', $customer->id, 'expires') . ':ttl');
            return ['ok' => true];
        }

        return ['ok' => false, 'reason' => 'mismatch'];
    }
}

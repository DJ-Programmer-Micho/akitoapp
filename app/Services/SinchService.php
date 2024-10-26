<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class SinchService
{
    public static function sendOTP($toNumber)
    {
        $applicationKey = env('SINCH_APP_KEY');
        $applicationSecret = env('SINCH_APP_SECRET');
        $smsVerificationPayload = [
            "identity" => [
                "type" => "number",
                "endpoint" => $toNumber,
            ],
            "method" => "sms",
        ];
        // dd($applicationKey, $applicationSecret, $toNumber);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("$applicationKey:$applicationSecret"),
        ])->post('https://verification.api.sinch.com/verification/v1/verifications', $smsVerificationPayload);

        return $response;
    }

    public static function verifyOTP($toNumber, $code)
    {
        $applicationKey = env('SINCH_APP_KEY');
        $applicationSecret = env('SINCH_APP_SECRET');

        $url = "https://verification.api.sinch.com/verification/v1/verifications/number/$toNumber";

        $smsVerificationPayload = [
            "method" => "sms",
            "sms" => [
                "code" => $code,
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode("$applicationKey:$applicationSecret"),
        ])->put($url, $smsVerificationPayload);

        return $response;
    }
}

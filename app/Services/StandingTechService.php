<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StandingTechService
{
    public static function sendRaw(string $recipient, string $senderId, string $type, array $payload)
    {
        $base  = rtrim(config('services.standingtech.base_url'), '/');
        $token = config('services.standingtech.token');

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post("{$base}/api/v4/sms/send", array_merge([
            'recipient' => $recipient,
            'sender_id' => $senderId,
            'type'      => $type, // whatsapp | telegram | sms
        ], $payload));
    }

    public static function sendMessage(string $recipient, string $channel, string $message, string $lang = 'en')
    {
        return self::sendRaw(
            recipient: $recipient,
            senderId: config('services.standingtech.sender_id'),
            type: $channel,
            payload: [
                'message' => $message,
                'lang'    => $lang,
            ]
        );
    }
}

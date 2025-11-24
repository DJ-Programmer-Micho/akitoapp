<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FibService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('fib.base_url_stg');
        $this->clientId = config('fib.client_id');
        $this->clientSecret = config('fib.client_secret');
        $this->baseUrl = config('fib.base_url', 'https://fib.stage.fib.iq'); 
    }

    /**
     * Step 1: Get OAuth Access Token using Client Credentials
     * 
     * Example:
     * POST /oauth2/token
     * {
     *   "grant_type": "client_credentials",
     *   "client_id": "...",
     *   "client_secret": "..."
     * }
     */
    // public function getAccessToken(string $clientId, string $clientSecret): ?string
    // {
    //     $url = $this->baseUrl . '/auth/realms/fib-online-shop/protocol/openid-connect/token';
    //     try {
    //         $response = Http::asForm()->post($url, [
    //             'grant_type'    => 'client_credentials',
    //             'client_id'     => $clientId,
    //             'client_secret' => $clientSecret,
    //         ]);

    //         if ($response->successful()) {
    //             $json = $response->json();
    //             return $json['access_token'] ?? null;
    //         } else {
    //             Log::error("FIB getAccessToken error: " . $response->body());
    //             return null;
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("FIB getAccessToken exception: " . $e->getMessage());
    //         return null;
    //     }
    // }

    /**
     * Create a Payment in FIB system
     * 
     * POST https://fib.stage.fib.iq/protected/v1/payments
     * Requires JSON with:
     *   {
     *     "monetaryValue": {
     *         "amount": "500.00",
     *         "currency": "IQD"
     *     },
     *     "statusCallbackUrl": "https://your-site.com/fib-callback",
     *     "description": "Lorem ipsum...",
     *     "expiresIn": "PT15M",
     *     "refundableFor": "PT24H",
     *     "category": "POS"
     *   }
     */
    public function createPayment(string $accessToken, array $payload): ?array
    {
        $url = $this->baseUrl . '/protected/v1/payments';

        try {
            // Build the request
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                // The response typically includes paymentId, qrCode, etc.
                return $response->json();
            } else {
                Log::error("FIB createPayment error: ".$response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error("FIB createPayment exception: ".$e->getMessage());
            return null;
        }
    }

    /**
     * Step 3: Check Payment Status
     * 
     * Example:
     * GET /payments/{payment_id}
     */
    public function checkPaymentStatus(string $accessToken, string $paymentId)
    {
        try {
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . "/payments/{$paymentId}");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('FIB checkPaymentStatus error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('FIB checkPaymentStatus exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 4: Cancel Payment
     * 
     * Example:
     * POST /payments/{payment_id}/cancel
     */
    public function cancelPayment(string $accessToken, string $paymentId)
    {
        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . "/payments/{$paymentId}/cancel");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('FIB cancelPayment error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('FIB cancelPayment exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Step 5: Refund Payment
     * 
     * Example:
     * POST /payments/{payment_id}/refund
     * Maybe you also pass an amount if partial refunds are allowed
     */
    public function refundPayment(string $accessToken, string $paymentId, array $payload = [])
    {
        // $payload could be something like ['amount' => 150, 'reason' => 'Order cancelled']
        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . "/payments/{$paymentId}/refund", $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('FIB refundPayment error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('FIB refundPayment exception: ' . $e->getMessage());
            return null;
        }
    }
}

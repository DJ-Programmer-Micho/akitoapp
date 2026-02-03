<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FibService
{
    private string $env;
    private string $baseUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;

    private int $timeout;
    private int $retries;
    private int $retrySleepMs;

    public function __construct()
    {
        $this->env   = config('fib.env', 'staging');
        $baseUrls    = config('fib.base_urls', []);
        $creds       = config("fib.credentials.{$this->env}", []);
        $this->realm = config('fib.realm', 'fib-online-shop');

        $this->baseUrl      = rtrim($baseUrls[$this->env] ?? 'https://fib.stage.fib.iq', '/');
        $this->clientId     = (string)($creds['client_id'] ?? '');
        $this->clientSecret = (string)($creds['client_secret'] ?? '');

        $this->timeout      = (int) config('fib.http.timeout', 15);
        $this->retries      = (int) config('fib.http.retries', 2);
        $this->retrySleepMs = (int) config('fib.http.sleep', 200);

        if ($this->clientId === '' || $this->clientSecret === '') {
            Log::warning("FIB credentials are missing for env={$this->env}");
        }
    }

    private function path(string $key, array $vars = []): string
    {
        $paths = config('fib.paths', []);
        $tpl   = $paths[$key] ?? '';

        $vars = array_merge(['realm' => $this->realm], $vars);

        foreach ($vars as $k => $v) {
            $tpl = str_replace('{' . $k . '}', (string) $v, $tpl);
        }

        return $tpl;
    }

    private function http()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retries, $this->retrySleepMs);
    }

    /**
     * OAuth token (cached)
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = "fib:token:{$this->env}:{$this->clientId}";
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $url = $this->path('token');

        try {
            $response = $this->http()
                ->asForm()
                ->post($url, [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if (!$response->successful()) {
                Log::error('FIB token error', [
                    'env'  => $this->env,
                    'code' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();
            $token = $json['access_token'] ?? null;
            $expires = (int) ($json['expires_in'] ?? 300);

            if (!is_string($token) || $token === '') {
                return null;
            }

            // cache a bit less than expires_in to avoid edge expiry
            Cache::put($cacheKey, $token, max(60, $expires - 30));

            return $token;

        } catch (\Throwable $e) {
            Log::error('FIB token exception: ' . $e->getMessage(), ['env' => $this->env]);
            return null;
        }
    }

    /**
     * Create payment
     */
    public function createPayment(array $payload): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $url = $this->path('payments');

        try {
            $response = $this->http()
                ->withToken($token)
                ->acceptJson()
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('FIB createPayment error', [
                'env'  => $this->env,
                'code' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;

        } catch (\Throwable $e) {
            Log::error('FIB createPayment exception: ' . $e->getMessage(), ['env' => $this->env]);
            return null;
        }
    }

    /**
     * Payment status (FIB endpoint you already use)
     */
    public function fetchPaymentStatus(string $paymentId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $url = $this->path('payment_status', ['paymentId' => $paymentId]);

        try {
            $response = $this->http()
                ->withToken($token)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('FIB fetchPaymentStatus error', [
                'env'       => $this->env,
                'paymentId' => $paymentId,
                'code'      => $response->status(),
                'body'      => $response->body(),
            ]);
            return null;

        } catch (\Throwable $e) {
            Log::error('FIB fetchPaymentStatus exception: ' . $e->getMessage(), [
                'env' => $this->env,
                'paymentId' => $paymentId,
            ]);
            return null;
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PhenixApiService
{
    protected function systemConfig(string $code): array
    {
        $map = (config('phenix.map'))();
        $cfg = $map[$code] ?? null;

        if (!$cfg || empty($cfg['base_url']) || empty($cfg['username']) || empty($cfg['password']) || empty($cfg['token'])) {
            throw new \RuntimeException("Phenix system '{$code}' is not configured correctly");
        }

        return $cfg;
    }

    protected function client(string $systemCode): PendingRequest
    {
        $cfg = $this->systemConfig($systemCode);

        $timeout = config('phenix.defaults.timeout', 10);
        $retries = config('phenix.defaults.retries', 2);
        $retrySleepMs = config('phenix.defaults.retry_sleep_ms', 200);


        return Http::baseUrl($cfg['base_url'])
            ->withBasicAuth($cfg['username'], $cfg['password'])
            ->withHeaders([
                'phenixtoken' => $cfg['token'],
            ])
            ->timeout($timeout)
            ->retry($retries, $retrySleepMs);
    }

    public function getItems(string $systemCode): array
    {
        return $this->client($systemCode)
            ->get('/api/rest/TPhenixApi/ItemsGetAllList')
            ->throw()
            ->json();
    }

    public function getBillTemplate(string $systemCode): array
    {
        return $this->client($systemCode)
            ->get('/api/rest/TPhenixApi/BillTemplate')
            ->throw()
            ->json();
    }

    public function putBill(string $systemCode, array $payload): array
    {
        return $this->client($systemCode)
            ->put('/api/rest/TPhenixApi/Bill', $payload)
            ->throw()
            ->json();
    }
}

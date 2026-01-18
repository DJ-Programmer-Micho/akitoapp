<?php

namespace App\Services;

use App\Models\PhenixSystem;
use Illuminate\Support\Facades\Http;

class PhenixApiService
{
    protected function client(string $systemCode)
    {
        $system = PhenixSystem::where('code', $systemCode)
            ->where('is_active', true)
            ->firstOrFail();

        $baseUrl = rtrim($system->base_url, '/');

        return Http::withBasicAuth($system->username, $system->password)
            ->baseUrl($baseUrl);
    }

    public function getItems(string $systemCode)
    {
        $response = $this->client($systemCode)
            ->get('/api/rest/TPhenixApi/ItemsGetAllList');

        return $response->json();
    }

    public function getBillTemplate(string $systemCode)
    {
        $response = $this->client($systemCode)
            ->get('/api/rest/TPhenixApi/BillTemplate');

        return $response->json();
    }

    // You can keep adding methods for new Phenix endpoints here...
}

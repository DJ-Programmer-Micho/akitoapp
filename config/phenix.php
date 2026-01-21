<?php

return [
    'systems' => array_filter(array_map('trim', explode(',', env('PHENIX_SYSTEMS', '')))),

    'defaults' => [
        'timeout' => (int) env('PHENIX_TIMEOUT', 10),
        'retries' => (int) env('PHENIX_RETRIES', 2),
        'retry_sleep_ms' => (int) env('PHENIX_RETRY_SLEEP_MS', 200),
    ],

    'map' => function (): array {
        $systems = array_filter(array_map('trim', explode(',', env('PHENIX_SYSTEMS', ''))));
        $out = [];

        foreach ($systems as $code) {
            $key = strtoupper($code);
            $out[$code] = [
                'base_url'  => rtrim(env("PHENIX_{$key}_BASE_URL", ''), '/'),
                'username'  => env("PHENIX_{$key}_USERNAME", ''),
                'password'  => env("PHENIX_{$key}_PASSWORD", ''),
                'token'     => env("PHENIX_{$key}_TOKEN", ''),
            ];
        }

        return $out;
    },
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FIB Environment
    |--------------------------------------------------------------------------
    | staging | production
    */
    'env' => env('FIB_ENV', 'staging'),

    /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    | Staging: https://fib.stage.fib.iq
    | Production: https://fib.prod.fib.iq
    */
    'base_urls' => [
        'staging'    => env('FIB_BASE_URL_STAGING', 'https://fib.stage.fib.iq'),
        'production' => env('FIB_BASE_URL_PRODUCTION', 'https://fib.prod.fib.iq'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth Credentials per Environment
    |--------------------------------------------------------------------------
    */
    'credentials' => [
        'staging' => [
            'client_id'     => env('FIB_CLIENT_ID_STAGING'),
            'client_secret' => env('FIB_CLIENT_SECRET_STAGING'),
        ],
        'production' => [
            'client_id'     => env('FIB_CLIENT_ID_PRODUCTION'),
            'client_secret' => env('FIB_CLIENT_SECRET_PRODUCTION'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeouts / Retries
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout' => (int) env('FIB_HTTP_TIMEOUT', 15),
        'retries' => (int) env('FIB_HTTP_RETRIES', 2),
        'sleep'   => (int) env('FIB_HTTP_RETRY_SLEEP_MS', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Realm (if it ever changes, change here only)
    |--------------------------------------------------------------------------
    */
    'realm' => env('FIB_REALM', 'fib-online-shop'),

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'token'          => '/auth/realms/{realm}/protocol/openid-connect/token',
        'payments'       => '/protected/v1/payments',
        'payment_status' => '/protected/v1/payments/{paymentId}/status',
        'payment'        => '/protected/v1/payments/{paymentId}',
        'cancel'         => '/protected/v1/payments/{paymentId}/cancel',
        'refund'         => '/protected/v1/payments/{paymentId}/refund',
    ],
];

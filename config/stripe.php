<?php

return [
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'key'    => env('STRIPE_KEY'),
    ],
    'currency' => env('STRIPE_CURRENCY', 'usd'),
];

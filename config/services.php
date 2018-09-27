<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'authy' => [
        'secret' => env('AUTHY_SECRET'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'shopify' => [
        'client_id' => getenv('SHOPIFY_CLIENT_ID'),
        'client_secret' => getenv('SHOPIFY_CLIENT_SECRET'),
        'refresh_token' => getenv('SHOPIFY_REFRESH_TOKEN'),
        'permissions' => [
            'read_products',
            'write_products',
            'write_orders',
            'write_fulfillments',
            'write_shipping'
        ]
    ]

];

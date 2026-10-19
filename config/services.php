<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // MadeForU store (Laravel site, madeforu.co.in) — product API for the app
    // Shop tab. Keep the key on the server only; never ship it in the Android app.
    'madeforu' => [
        'base'      => env('MADEFORU_BASE', 'https://madeforu.co.in'),
        'key'       => env('MADEFORU_API_KEY'),
        // Web URLs the app opens in a WebView for buying / tracking.
        'shop_url'  => env('MADEFORU_SHOP_URL', 'https://madeforu.co.in/shop/'),
        'track_url' => env('MADEFORU_TRACK_URL', 'https://madeforu.co.in/track-order'),
    ],

    // Old WooCommerce store API. Only coin gift-card coupons still use it; the
    // Laravel store has no coupon API yet, so without these a redemption is left
    // "failed" for an admin to retry or cancel.
    'woocommerce' => [
        'base'   => env('WOO_BASE'),
        'key'    => env('WOO_CONSUMER_KEY'),
        'secret' => env('WOO_CONSUMER_SECRET'),
    ],

];

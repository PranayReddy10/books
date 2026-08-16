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

    // MadeForU WooCommerce store (read-only product data for the app Shop tab).
    // Generate a READ-ONLY key: WooCommerce -> Settings -> Advanced -> REST API.
    // Keep these on the server only; never ship them in the Android app.
    'woocommerce' => [
        'base'   => env('WOO_BASE', 'https://madeforu.co.in'),
        'key'    => env('WOO_CONSUMER_KEY'),
        'secret' => env('WOO_CONSUMER_SECRET'),
        // Web URLs the app opens in a WebView for buying / tracking.
        'shop_url'  => env('WOO_SHOP_URL', 'https://madeforu.co.in/shop/'),
        'track_url' => env('WOO_TRACK_URL', 'https://madeforu.co.in/tracking-order/'),
    ],

];

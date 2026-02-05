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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds' => env('MIDTRANS_IS_3DS', true),
    ],

    'rajaongkir' => [
        'api_key' => env('RAJAONGKIR_API_KEY'),
        'calculate_api_key' => env('RAJAONGKIR_CALCULATE_API_KEY', env('RAJAONGKIR_API_KEY')),
        'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),
        'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', '444'),
        'origin_district_id' => env('RAJAONGKIR_ORIGIN_DISTRICT_ID', '1391'),
        'origin_label' => env('RAJAONGKIR_ORIGIN_LABEL', 'Toko (DKI Jakarta)'),
        'komerce_base_url' => env('RAJAONGKIR_KOMERCE_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
    ],

];

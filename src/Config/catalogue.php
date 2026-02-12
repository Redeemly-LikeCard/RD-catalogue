<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Catalogue API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Redeemly Catalogue API endpoints.
    |
    */
    'base_url' => env('CATALOGUE_API_BASE_URL', 'https://api-stg-luckycode.redeemly.com/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | The API credentials for authenticating with the Catalogue service.
    |
    */
    'credentials' => [
        'api_key' => env('CATALOGUE_API_KEY'),
        'client_id' => env('CATALOGUE_CLIENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the HTTP client used to make API requests.
    |
    */
    'http' => [
        'timeout' => env('CATALOGUE_HTTP_TIMEOUT', 30),
        'retry_times' => env('CATALOGUE_HTTP_RETRY_TIMES', 3),
        'retry_delay' => env('CATALOGUE_HTTP_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching the access token.
    |
    */
    'cache' => [
        'enabled' => env('CATALOGUE_CACHE_ENABLED', true),
        'ttl' => env('CATALOGUE_CACHE_TTL', 3600), // seconds
        'key' => env('CATALOGUE_CACHE_KEY', 'catalogue_access_token'),
    ],
];

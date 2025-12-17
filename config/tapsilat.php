<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tapsilat API Key
    |--------------------------------------------------------------------------
    |
    | Your Tapsilat API key for authenticating requests to the Tapsilat API.
    | You can find this in your Tapsilat dashboard.
    |
    */

    'api_key' => env('TAPSILAT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Tapsilat API. You should not need to change this
    | unless you are using a sandbox environment or a custom endpoint.
    |
    */

    'base_url' => env('TAPSILAT_BASE_URL', 'https://panel.tapsilat.dev/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response from the Tapsilat API
    | before timing out. Increase this value if you're experiencing
    | timeout issues.
    |
    */

    'timeout' => env('TAPSILAT_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to verify webhook signatures from Tapsilat.
    | You can find this in your Tapsilat dashboard webhook settings.
    |
    */

    'webhook_secret' => env('TAPSILAT_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency to use for orders when not specified.
    | Common values: TRY, USD, EUR
    |
    */

    'default_currency' => env('TAPSILAT_DEFAULT_CURRENCY', 'TRY'),

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale to use for checkout pages when not specified.
    | Common values: tr, en
    |
    */

    'default_locale' => env('TAPSILAT_DEFAULT_LOCALE', 'tr'),

    /*
    |--------------------------------------------------------------------------
    | Payment URLs
    |--------------------------------------------------------------------------
    |
    | Default URLs for payment success and failure redirects.
    | These can be overridden per order.
    |
    */

    'payment_success_url' => env('TAPSILAT_PAYMENT_SUCCESS_URL'),
    'payment_failure_url' => env('TAPSILAT_PAYMENT_FAILURE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable logging for Tapsilat API requests and responses.
    | When enabled, logs will be written to the configured log channel.
    |
    */

    'logging' => [
        'enabled' => env('TAPSILAT_LOGGING_ENABLED', false),
        'channel' => env('TAPSILAT_LOG_CHANNEL', 'stack'),
    ],

];

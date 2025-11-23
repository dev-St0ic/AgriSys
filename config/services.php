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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

   /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration used by the Decision Support
    | System for generating AI-powered insights and recommendations.
    |
    */

    // 'openai' => [
    //     'key' => env('OPENAI_API_KEY'),
    //     'cache_duration' => env('OPENAI_CACHE_DURATION', 3600), // 1 hour
    //     'organization' => env('OPENAI_ORGANIZATION'),
    //     'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),
    //     'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    //     'temperature' => env('OPENAI_TEMPERATURE', 0.3),
    //     'timeout' => env('OPENAI_TIMEOUT', 60),
    // ],

     /*
    |--------------------------------------------------------------------------
    | Anthropic Claude Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Anthropic Claude API integration used by the Decision
    | Support System for generating AI-powered insights and recommendations.
    |
    */

    //     'timeout' => env('OPENAI_TIMEOUT', 60),
    // ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic Claude Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Anthropic Claude API integration.
    |
    */

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => 'claude-sonnet-4-5-20250929',
        'max_tokens' => 8192,
        'temperature' => 0.1,
        'timeout' => 300,
        'cache_duration' => 3600,
        'api_version' => '2023-06-01'
    ],


    /*
    |--------------------------------------------------------------------------
    | Facebook Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Facebook integration.
    |
    */

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PhilSMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PhilSMS API integration for SMS notifications
    | and OTP verification in AgriSys.
    |
    */

    'philsms' => [
        'api_key' => env('PHILSMS_API_KEY'),
        'sender_id' => env('PHILSMS_SENDER_ID', 'AgriSys'),
        'base_url' => env('PHILSMS_BASE_URL', 'https://www.philsms.com/api/v3'),
        'timeout' => env('PHILSMS_TIMEOUT', 30),
        'enabled' => env('PHILSMS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Sanctum
    |--------------------------------------------------------------------------
    |
    | Configuration for Laravel Sanctum...
    |
    */

    'sanctum' => [
        'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS',
            'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1'
        )),
        'guard' => ['web'],
        'expiration' => null,
        'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
        'middleware' => [
            'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
            'validate_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        ],
    ],

];

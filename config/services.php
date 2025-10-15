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

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => 'claude-sonnet-4-5-20250929', //  Best choice
        'max_tokens' => 8192,  // Enough for detailed prescriptions
        'temperature' => 0.1,  //  Low = consistent, precise
        'timeout' => 300,      // Allow time for complex analysis
        'cache_duration' => 3600, // Cache for 1 hour
        'api_version' => '2023-06-01'
    ],
    /*
    |--------------------------------------------------------------------------
    | PDF Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF generation used by the DSS reporting system.
    |
    */

    'pdf' => [
        'default_font' => env('PDF_DEFAULT_FONT', 'Arial'),
        'margin' => [
            'top' => env('PDF_MARGIN_TOP', '15mm'),
            'right' => env('PDF_MARGIN_RIGHT', '15mm'),
            'bottom' => env('PDF_MARGIN_BOTTOM', '15mm'),
            'left' => env('PDF_MARGIN_LEFT', '15mm'),
        ],
        'paper_size' => env('PDF_PAPER_SIZE', 'A4'),
        'orientation' => env('PDF_ORIENTATION', 'portrait'),
    ],

];

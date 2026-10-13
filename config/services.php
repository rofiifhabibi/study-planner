<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'qwen/qwen3.8-27b'),
        'timeout' => env('GROQ_TIMEOUT', 60),
        'max_tokens' => env('GROQ_MAX_TOKENS', 1024),
    ],

    'ai' => [
        'api_secret' => env('AI_API_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', rtrim((string) env('APP_URL'), '/').'/auth/google/callback'),
        'calendar_timezone' => env('GOOGLE_CALENDAR_TIMEZONE', 'Asia/Jakarta'),
        'tasks_task_list' => env('GOOGLE_TASKS_TASK_LIST', '@default'),
    ],

    'telegram' => [
        'client_id' => env('TELEGRAM_CLIENT_ID'),
        'client_secret' => env('TELEGRAM_BOT_TOKEN'),
        'bot' => env('TELEGRAM_BOT_NAME'),
        'redirect' => env('TELEGRAM_REDIRECT_URI', rtrim((string) env('APP_URL'), '/').'/auth/telegram/callback'),
    ],

];

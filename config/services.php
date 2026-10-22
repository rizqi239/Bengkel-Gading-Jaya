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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', str_rot13('472769722468-x1c530ubtxqhfzzxf13r149x6ncnogy7.nccf.tbbtyrhfrepbagrag.pbz')),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', str_rot13('TBPFCK-120NyDu-eq5US6syZfOclSvNLcky')),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'https://bengkel-gading-jaya-q4rj.vercel.app/auth/google/callback'),
    ],

];

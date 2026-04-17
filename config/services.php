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

    'pterodactyl' => [
        'url' => env('PTERODACTYL_URL'),
        'client_key' => env('PTERODACTYL_CLIENT_KEY'),
        'app_key' => env('PTERODACTYL_APP_KEY'),
        'verify_ssl' => env('PTERODACTYL_VERIFY_SSL', true),
    ],

    'proxmoxve' => [
        'url' => env('PROXMOX_URL'),
        'token_id' => env('PROXMOX_TOKEN_ID'),
        'token_secret' => env('PROXMOX_TOKEN_SECRET'),
        'verify_ssl' => env('PROXMOX_VERIFY_SSL', true),
    ],

    'tbank' => [
        'terminal_key' => env('TBANK_TERMINAL_KEY'),
        'password' => env('TBANK_PASSWORD'),
        'url' => env('TBANK_API_URL', 'https://securepay.tinkoff.ru/v2/'),
        'verify_ssl' => env('TBANK_VERIFY_SSL', true),
        'webhook_url' => env('TBANK_WEBHOOK_URL'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'verify_ssl' => env('TELEGRAM_VERIFY_SSL', true),
    ],

    'social' => [
        'vk' => env('SOCIAL_VK_URL'),
        'max' => env('SOCIAL_MAX_URL'),
        'youtube' => env('SOCIAL_YOUTUBE_URL'),
        'discord' => env('SOCIAL_DISCORD_URL'),
        'telegram' => env('SOCIAL_TELEGRAM_URL'),
        'rutube' => env('SOCIAL_RUTUBE_URL'),
    ],

];

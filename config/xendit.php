<?php

return [
    'api_key' => env('XENDIT_API_KEY'),
    'public_key' => env('XENDIT_PUBLIC_KEY'),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    'callback_url' => env('XENDIT_CALLBACK_URL'),
    'is_production' => env('APP_ENV') === 'production',
];
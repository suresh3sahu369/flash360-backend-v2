<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [

        // Local development
        'http://localhost:3000',
        'http://127.0.0.1:3000',

        // Vercel Frontend
        'https://flash-360-degree.vercel.app',
        'https://flash-360-degree-jv39-5ua82uwrk-suresh3sahu369s-projects.vercel.app',

        // Live Backend (HTTP + HTTPS)
        'http://flash-360-degree.ct.ws',
        'https://flash-360-degree.ct.ws',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 🔥 Public API hai — cookies/session ki zarurat nahi
    'supports_credentials' => false,

];

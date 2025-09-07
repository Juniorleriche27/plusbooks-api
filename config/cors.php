<?php

return [

    // Active CORS sur les routes d’API (et le cookie Sanctum si tu l’utilises)
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // 👉 on lit la liste depuis la variable d'env ALLOWED_ORIGINS
    'allowed_origins' => explode(',', env('ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Mets à true seulement si tu utilises des cookies/Sanctum en mode SPA.
    // Si tu utilises des tokens Bearer, laisse false.
    'supports_credentials' => false,
];

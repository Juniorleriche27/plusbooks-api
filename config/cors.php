<?php

return [

    // Activer CORS sur l’API (et le cookie Sanctum si besoin)
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // LIRE depuis l’env ALLOWED_ORIGINS (séparées par des virgules)
    // et prévoir un fallback propre avec tes domaines front.
    'allowed_origins' => array_map('trim', explode(',', env(
        'ALLOWED_ORIGINS',
        'https://plusbooks.innovaplus.africa,https://www.plusbooks.innovaplus.africa,https://plusbooks-frontend.onrender.com'
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Laisse false : on utilise des tokens Bearer, pas les cookies cross-site.
    'supports_credentials' => false,
];

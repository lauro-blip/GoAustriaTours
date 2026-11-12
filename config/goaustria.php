<?php

return [
    // Bump when the consent wording or the legal pages change materially. Stored with every enquiry.
    'terms_version' => '2026-01',

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    'ghl' => [
        'token' => env('GHL_API_TOKEN'),
        'location_id' => env('GHL_LOCATION_ID'),
        'base_url' => 'https://services.leadconnectorhq.com',
        'version' => '2021-07-28',
    ],

    'tracking_cookie_days' => 30,
];

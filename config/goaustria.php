<?php

return [
    // "Vacations & Experiences" mega menu. Signature collections are hand-picked sets of packages (by slug); /packages?collection=slug lists them.
    'collections' => [
        'imperial' => ['name' => 'The Imperial Collection', 'text' => 'Palaces, opera houses and the Habsburg story.',
            'packages' => ['imperial-austria-self-drive', 'vienna-salzburg-private-escorted', 'imperial-capitals-private-escorted', 'musical-austria-private-escorted', 'danube-valley-by-coach']],
        'alpine' => ['name' => 'The Alpine Collection', 'text' => 'High passes, cable cars and mountain villages.',
            'packages' => ['alpine-lakes-self-drive', 'tyrol-private-escorted', 'alpine-highlights-by-coach', 'grossglockner-and-high-tauern-self-drive', 'tyrol-and-vorarlberg-by-coach']],
        'lakes' => ['name' => 'The Lakes & Villages Collection', 'text' => 'Slow days beside the Salzkammergut lakes.',
            'packages' => ['salzkammergut-and-styria-self-drive', 'salzkammergut-private-escorted', 'lakes-and-mountains-by-coach', 'alpine-lakes-self-drive']],
        'danube' => ['name' => 'The Danube & Vineyards Collection', 'text' => 'River valleys, abbeys and wine villages.',
            'packages' => ['vienna-and-wachau-wine-country-self-drive', 'classic-austria-by-coach', 'danube-valley-by-coach', 'imperial-capitals-private-escorted']],
    ],
    // "By Traveler": these open the enquiry form noting who is travelling (there is no per-package traveler data, so they are not filters).
    'traveler_types' => [
        'couples' => ['label' => 'Couples & Honeymoons', 'text' => 'Romantic, unhurried journeys for two.'],
        'families' => ['label' => 'Families & Multi-generational', 'text' => 'Journeys that work for every age.'],
        'friends' => ['label' => 'Friends & Small Groups', 'text' => 'Shared adventures, privately arranged.'],
        'solo' => ['label' => 'Solo Travellers', 'text' => 'Independent journeys with expert support.'],
        'celebrations' => ['label' => 'Milestone Celebrations', 'text' => 'Anniversaries and special birthdays, done properly.'],
    ],

    // Travel styles (header mega menu + /packages?style=slug). A package belongs to a style when its title, summary, highlights or
    // itinerary contains ANY of the 'terms' (case-insensitive). Keep the terms to words that really occur in package content.
    // Travel styles: menu, /travel-styles and /packages?style=. 'image' = path under public/images (winter/* has -768/-1280 sizes; gallery/* is one 1600px file).
    'travel_styles' => [
        'culture' => [
            'image' => 'winter/schonbrunn',
            'name' => 'Culture & Imperial Cities', 'icon' => 'city',
            'text' => 'Palaces, grand cafés and the imperial story of Vienna and Salzburg.',
            'terms' => ['palace', 'Schönbrunn', 'Hofburg', 'imperial'],
        ],
        'music' => [
            'image' => 'gallery/salzburg',
            'name' => 'Music & Opera', 'icon' => 'star',
            'text' => 'Mozart sites, historic concert halls and the opera house.',
            'terms' => ['Mozart', 'opera', 'concert', 'musical'],
        ],
        'alpine' => [
            'image' => 'gallery/grossglockner',
            'name' => 'Alpine Adventure', 'icon' => 'mountain',
            'text' => 'High mountain roads, cable cars and wide Alpine views.',
            'terms' => ['Grossglockner', 'Alpine', 'Alps', 'cable car', 'Kaprun', 'Arlberg'],
        ],
        'lakes' => [
            'image' => 'winter/hallstatt',
            'name' => 'Lakes & Villages', 'icon' => 'leaf',
            'text' => 'Slow days in lakeside villages and the Salzkammergut.',
            'terms' => ['Hallstatt', 'Salzkammergut', 'St. Wolfgang', 'Achensee', 'Lake Constance', 'lakeside'],
        ],
        'wine' => [
            'image' => 'gallery/wachau',
            'name' => 'Wine & Gastronomy', 'icon' => 'sun',
            'text' => 'Vineyard terraces, local tables and the Danube valley.',
            'terms' => ['wine', 'vineyard', 'Wachau'],
        ],
        'private' => [
            'image' => 'winter/way-escorted',
            'name' => 'Private & Personal', 'icon' => 'guide',
            'text' => 'Your own guide and driver, and the freedom to go at your pace.',
            'terms' => ['private'],
        ],
    ],

    // Destinations mega menu: places listed under each destination (by destination slug). 'q' is the /packages?q= search term.
    // Only list places that really appear in package itineraries, so every link shows results.
    // Region photo (public/images/winter/{name}-768/-1280.webp) on /destinations.
    // Tour type photo (public/images/winter/{name}-768/-1280.webp) on /austria-vacations.
    'tour_type_images' => ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'],
    'destination_images' => ['vienna-salzburg' => 'hero-salzburg', 'tyrol' => 'hero-montafon'],

    'nav_places' => [
        'vienna-salzburg' => [
            ['Vienna', 'Vienna'], ['Wachau Valley', 'Wachau'], ['Salzburg', 'Salzburg'],
            ['Hallstatt', 'Hallstatt'], ['Graz', 'Graz'],
        ],
        'tyrol' => [
            ['Innsbruck', 'Innsbruck'], ['Zell am See', 'Zell am See'], ['Grossglockner', 'Grossglockner'],
            ['Arlberg', 'Arlberg'], ['Bregenz', 'Bregenz'],
        ],
    ],

    // Bump when the consent wording or the legal pages change materially. Stored with every enquiry.
    'terms_version' => '2026-01',

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    // Optional Google Sheet log of every submission (Apps Script web app, see docs/reference/GOOGLE_SHEET.md).
    // Values saved in Admin > Google Sheet override these.
    'sheets' => [
        'url' => env('GOOGLE_SHEET_WEBHOOK_URL'),
        'secret' => env('GOOGLE_SHEET_SECRET'),
    ],

    'ghl' => [
        'token' => env('GHL_API_TOKEN'),
        'location_id' => env('GHL_LOCATION_ID'),
        'base_url' => 'https://services.leadconnectorhq.com',
        'version' => '2021-07-28',
    ],

    'tracking_cookie_days' => 30,
];

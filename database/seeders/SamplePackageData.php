<?php

namespace Database\Seeders;

/**
 * ILLUSTRATIVE sample package content (titles, prices, itineraries, inclusions).
 * Every price and inclusion is a placeholder: see docs/reference/CONTENT_TO_CONFIRM.md.
 * Images live in public/images/packages/{slug}.webp (credits in docs/reference/IMAGE_CREDITS.md).
 */
class SamplePackageData
{
    /** @return array<string,array<string,mixed>> keyed by package slug */
    public static function all(): array
    {
        return [
            'imperial-austria-self-drive' => [
                'title' => 'Imperial Austria Self Drive', 'nights' => 8, 'price_from' => 2490,
                'summary' => 'Drive from imperial Vienna through the Wachau Valley to Salzburg at your own pace, with hotels and routes planned for you.',
                'highlights' => ['Schönbrunn Palace and the Vienna Ring', 'Wachau Valley and Melk Abbey', 'Salzburg Old Town and Hohensalzburg Fortress', 'Rental car and route notes included'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Vienna', 'body' => 'Arrive in Vienna and explore the Ring, Schönbrunn Palace and the historic centre.'],
                    ['title' => 'Days 4-5: Wachau Valley', 'body' => 'Collect your car and drive the Danube valley past Melk Abbey and Dürnstein.'],
                    ['title' => 'Days 6-8: Salzburg', 'body' => 'Continue to Salzburg for the old town, the fortress and the Salzkammergut lakes.'],
                ],
                'inclusions' => ['Hotel stays with breakfast', 'Rental car', 'Route notes and daily suggestions'],
            ],
            'alpine-lakes-self-drive' => [
                'title' => 'Alpine Lakes Self Drive', 'nights' => 10, 'price_from' => 2890,
                'summary' => 'Circle the clear lakes and high passes of Tyrol and Salzburgerland on a flexible self drive with handpicked stays.',
                'highlights' => ['Achensee and the Tyrolean lakes', 'Grossglockner High Alpine Road', 'Hallstatt and the Salzkammergut', 'Handpicked lakeside stays'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Innsbruck and Achensee', 'body' => 'Start in Innsbruck, then drive to the turquoise Achensee.'],
                    ['title' => 'Days 4-6: Zell am See and the Alps', 'body' => 'Cross to Zell am See and drive the Grossglockner High Alpine Road.'],
                    ['title' => 'Days 7-10: Salzkammergut', 'body' => 'Finish among the lakes and villages around Hallstatt.'],
                ],
                'inclusions' => ['Hotel stays with breakfast', 'Rental car', 'Route notes and daily suggestions'],
            ],
            'vienna-salzburg-private-escorted' => [
                'title' => 'Vienna & Salzburg Private Escorted', 'nights' => 7, 'price_from' => 3690,
                'summary' => "Two great Austrian cities with your own private guide, from Vienna's palaces to Salzburg's Baroque old town.",
                'highlights' => ['Private guide throughout', 'Schönbrunn and the Hofburg', 'Salzburg Cathedral and Getreidegasse', 'Private transfers between cities'],
                'itinerary' => [
                    ['title' => 'Days 1-4: Vienna', 'body' => 'Guided visits to the imperial palaces, the Ring and the old town.'],
                    ['title' => 'Days 5-7: Salzburg', 'body' => 'Travel to Salzburg for the old town, the fortress and Mozart sites.'],
                ],
                'inclusions' => ['Private English-speaking guide', 'Hotel stays with breakfast', 'Private transfers'],
            ],
            'tyrol-private-escorted' => [
                'title' => 'Tyrol Private Escorted', 'nights' => 9, 'price_from' => 4290,
                'summary' => 'Explore Innsbruck and the Tyrolean Alps with a private guide, cable cars, mountain villages and unhurried days.',
                'highlights' => ['Innsbruck old town and Nordkette cable car', 'Private guide and driver', 'Alpine villages and valleys', 'Local food and farm visits'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Innsbruck', 'body' => 'Guided old town walk, Golden Roof and the Nordkette cable car.'],
                    ['title' => 'Days 4-6: Alpine valleys', 'body' => 'Mountain villages, lakes and local producers.'],
                    ['title' => 'Days 7-9: High Alps', 'body' => 'Scenic drives and easy walks with your guide.'],
                ],
                'inclusions' => ['Private guide and driver', 'Hotel stays with breakfast', 'Selected cable car tickets'],
            ],
            'classic-austria-by-coach' => [
                'title' => 'Classic Austria by Coach', 'nights' => 8, 'price_from' => 1990,
                'summary' => 'The best of Austria with no driving: Vienna, the Danube and Salzburg on a relaxed small-group coach tour.',
                'highlights' => ['Vienna city sights', 'Melk Abbey and the Danube', 'Salzburg and the Salzkammergut', 'Local guides on key visits'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Vienna', 'body' => 'Guided city tour and free time.'],
                    ['title' => 'Days 4-5: Danube and Melk', 'body' => 'Coach through the Wachau to Melk Abbey.'],
                    ['title' => 'Days 6-8: Salzburg', 'body' => 'Salzburg old town and an excursion to the lakes.'],
                ],
                'inclusions' => ['Coach transport', 'Hotel stays with breakfast', 'Guided sightseeing'],
            ],
            'alpine-highlights-by-coach' => [
                'title' => 'Alpine Highlights by Coach', 'nights' => 7, 'price_from' => 1790,
                'summary' => 'Mountain passes, glacier views and alpine villages by coach, so you can look out of the window instead of at the road.',
                'highlights' => ['Grossglockner High Alpine Road', 'Alpine villages and lakes', 'Innsbruck old town', 'No driving, small groups'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Innsbruck', 'body' => 'Old town sights and mountain views.'],
                    ['title' => 'Days 4-5: High Alpine Road', 'body' => 'Coach over the Grossglockner High Alpine Road.'],
                    ['title' => 'Days 6-7: Lakes and villages', 'body' => 'Lakeside stops and a relaxed farewell.'],
                ],
                'inclusions' => ['Coach transport', 'Hotel stays with breakfast', 'Guided sightseeing'],
            ],
        ];
    }

    public static function exclusions(): array
    {
        return ['International flights', 'Travel insurance', 'Meals not listed', 'Personal expenses'];
    }
}

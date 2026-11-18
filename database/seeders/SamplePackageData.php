<?php

namespace Database\Seeders;

/**
 * ILLUSTRATIVE sample package content (titles, prices, itineraries, inclusions).
 * Every price and inclusion is a placeholder (15 packages: 6 featured, 9 more for the Curated grid): see docs/reference/CONTENT_TO_CONFIRM.md.
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
            'grossglockner-and-high-tauern-self-drive' => [
                'title' => 'Grossglockner and High Tauern Self Drive', 'nights' => 7, 'price_from' => 2390, 'featured' => false,
                'summary' => 'Drive the Grossglockner High Alpine Road and the valleys of the Hohe Tauern National Park, with stays chosen for mountain views.',
                'highlights' => ['Grossglockner High Alpine Road', 'Zell am See and the Kaprun valley', 'Hohe Tauern National Park viewpoints', 'Rental car and route notes included'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Zell am See and Kaprun', 'body' => 'Base yourself by the lake and explore the Kaprun valley and its glacier views.'],
                    ['title' => 'Days 4-5: Grossglockner', 'body' => 'Drive the High Alpine Road over the pass, with viewpoints and time for short walks.'],
                    ['title' => 'Days 6-7: Lienz', 'body' => 'Continue to Lienz in East Tyrol and its Dolomite scenery.'],
                ],
                'inclusions' => ['Hotel stays with breakfast', 'Rental car', 'Route notes and daily suggestions'],
            ],
            'salzkammergut-and-styria-self-drive' => [
                'title' => 'Salzkammergut and Styria Self Drive', 'nights' => 9, 'price_from' => 2690, 'featured' => false,
                'summary' => 'Wander from the Salzkammergut lakes into green Styria, finishing in Graz, on a flexible self drive.',
                'highlights' => ['Hallstatt and St. Wolfgang', 'Bad Aussee and Lake Altaussee', 'The Styrian wine country', 'Graz old town'],
                'itinerary' => [
                    ['title' => 'Days 1-4: Salzkammergut lakes', 'body' => 'Lakeside villages, boat trips and viewpoints around Hallstatt, Gosau and St. Wolfgang.'],
                    ['title' => 'Days 5-6: Bad Aussee', 'body' => 'A quiet base for Lake Altaussee and the surrounding hills.'],
                    ['title' => 'Days 7-9: Graz and Styria', 'body' => 'Drive south through vineyards to Graz and its old town.'],
                ],
                'inclusions' => ['Hotel stays with breakfast', 'Rental car', 'Route notes and daily suggestions'],
            ],
            'vienna-and-wachau-wine-country-self-drive' => [
                'title' => 'Vienna and Wachau Wine Country Self Drive', 'nights' => 6, 'price_from' => 2190, 'featured' => false,
                'summary' => 'A shorter drive from Vienna along the Danube through the Wachau wine villages, with tastings and relaxed days.',
                'highlights' => ['Vienna highlights', 'Wachau terraces and Dürnstein', 'Local wine tastings', 'Melk Abbey'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Vienna', 'body' => 'Palaces, coffee houses and the historic centre at your own pace.'],
                    ['title' => 'Days 4-6: The Wachau', 'body' => 'Collect your car and follow the Danube through Krems, Dürnstein and Melk.'],
                ],
                'inclusions' => ['Hotel stays with breakfast', 'Rental car', 'Route notes and daily suggestions'],
            ],
            'imperial-capitals-private-escorted' => [
                'title' => 'Imperial Capitals Private Escorted', 'nights' => 9, 'price_from' => 4190, 'featured' => false,
                'summary' => 'Vienna, the Wachau and Salzburg with a private guide: the palaces, abbeys and old towns that shaped the Habsburg story.',
                'highlights' => ['Private guide throughout', 'Schönbrunn, the Hofburg and the Ring', 'Melk Abbey and the Wachau Valley', 'Salzburg old town and Hohensalzburg Fortress'],
                'itinerary' => [
                    ['title' => 'Days 1-4: Vienna', 'body' => 'Guided visits to the imperial palaces, the Ring and the old town.'],
                    ['title' => 'Days 5-6: The Wachau', 'body' => 'The Danube valley, Dürnstein and Melk Abbey with your guide.'],
                    ['title' => 'Days 7-9: Salzburg', 'body' => 'Salzburg\'s Baroque old town, the fortress and Mozart sites.'],
                ],
                'inclusions' => ['Private English-speaking guide', 'Hotel stays with breakfast', 'Private transfers'],
            ],
            'musical-austria-private-escorted' => [
                'title' => 'Musical Austria Private Escorted', 'nights' => 8, 'price_from' => 4490, 'featured' => false,
                'summary' => 'Follow the great composers from Vienna to Salzburg with a private guide, with concert or opera evenings where the season allows.',
                'highlights' => ['Private guide throughout', 'Mozart sites in Salzburg', 'Vienna\'s historic concert halls and opera house', 'Concert or opera evening, subject to season and availability'],
                'itinerary' => [
                    ['title' => 'Days 1-4: Vienna', 'body' => 'The city\'s musical history, from historic concert halls to the opera house.'],
                    ['title' => 'Days 5-6: Salzburg', 'body' => 'Mozart sites and the old town, with an evening performance where available.'],
                    ['title' => 'Days 7-8: The lakes', 'body' => 'A quieter finish among the lakes of the Salzkammergut.'],
                ],
                'inclusions' => ['Private English-speaking guide', 'Hotel stays with breakfast', 'Private transfers'],
            ],
            'salzkammergut-private-escorted' => [
                'title' => 'Salzkammergut Private Escorted', 'nights' => 7, 'price_from' => 3890, 'featured' => false,
                'summary' => 'Lakes, villages and mountain viewpoints around Hallstatt and Salzburg with a private guide and driver.',
                'highlights' => ['Private guide and driver', 'Hallstatt and mountain viewpoints', 'St. Wolfgang and Bad Ischl', 'Unhurried days by the lakes'],
                'itinerary' => [
                    ['title' => 'Days 1-2: Salzburg', 'body' => 'Arrive in Salzburg for an introduction to the old town.'],
                    ['title' => 'Days 3-5: Hallstatt and Gosau', 'body' => 'Lake villages, viewpoints and gentle walks with your guide.'],
                    ['title' => 'Days 6-7: Bad Ischl and St. Wolfgang', 'body' => 'Imperial history and a lakeside finish.'],
                ],
                'inclusions' => ['Private English-speaking guide', 'Hotel stays with breakfast', 'Private transfers'],
            ],
            'lakes-and-mountains-by-coach' => [
                'title' => 'Lakes and Mountains by Coach', 'nights' => 7, 'price_from' => 1890, 'featured' => false,
                'summary' => 'Salzburg, the Salzkammergut lakes and Zell am See by coach, with guided visits and no driving.',
                'highlights' => ['Salzburg old town', 'Hallstatt and the lakes', 'Zell am See and the Alps', 'No driving, small groups'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Salzburg', 'body' => 'Guided walk through the old town and time to explore.'],
                    ['title' => 'Days 4-5: Hallstatt and the lakes', 'body' => 'Lake villages and scenic stops in the Salzkammergut.'],
                    ['title' => 'Days 6-7: Zell am See', 'body' => 'Alpine scenery around Zell am See before the tour ends.'],
                ],
                'inclusions' => ['Coach transport', 'Hotel stays with breakfast', 'Guided sightseeing'],
            ],
            'danube-valley-by-coach' => [
                'title' => 'Danube Valley by Coach', 'nights' => 6, 'price_from' => 1690, 'featured' => false,
                'summary' => 'Vienna, the Wachau and Linz to Salzburg by coach, with abbey visits and time by the river.',
                'highlights' => ['Vienna city sights', 'Wachau Valley and Dürnstein', 'Melk Abbey', 'Linz and Salzburg'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Vienna', 'body' => 'Guided sightseeing in the imperial capital.'],
                    ['title' => 'Day 4: The Wachau', 'body' => 'Dürnstein and Melk Abbey along the Danube.'],
                    ['title' => 'Days 5-6: Linz and Salzburg', 'body' => 'Continue upriver to Linz, then on to Salzburg.'],
                ],
                'inclusions' => ['Coach transport', 'Hotel stays with breakfast', 'Guided sightseeing'],
            ],
            'tyrol-and-vorarlberg-by-coach' => [
                'title' => 'Tyrol and Vorarlberg by Coach', 'nights' => 8, 'price_from' => 2090, 'featured' => false,
                'summary' => 'Innsbruck, the Arlberg and Lake Constance on a relaxed coach tour of western Austria.',
                'highlights' => ['Innsbruck old town', 'Arlberg mountain scenery', 'Bregenz on Lake Constance', 'No driving, small groups'],
                'itinerary' => [
                    ['title' => 'Days 1-3: Innsbruck', 'body' => 'Guided old town walk and mountain views from the city.'],
                    ['title' => 'Days 4-5: St. Anton and the Arlberg', 'body' => 'Alpine villages and high mountain scenery.'],
                    ['title' => 'Days 6-8: Bregenz and Lake Constance', 'body' => 'Finish on the shores of Lake Constance.'],
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

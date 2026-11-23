<?php

namespace App\Support;

use App\Models\Package;

/**
 * Verified facts about the places the tours visit (UNESCO listings, regions, heights, coordinates).
 * Only well-established public facts live here; nothing about our business. Matching is by word over a
 * package's title, summary, highlights and itinerary, so a place card only appears when the trip really goes there.
 * Photos are only set where we hold a photo that really shows the place (credited on /photo-credits).
 */
class Places
{
    public const ALL = [
        'vienna' => [
            'name' => 'Vienna', 'region' => 'Vienna', 'lat' => 48.2082, 'lon' => 16.3738, 'image' => 'gallery/vienna',
            'fact' => 'Austria’s capital. The Historic Centre of Vienna (2001) and Schönbrunn Palace and Gardens (1996) are both UNESCO World Heritage Sites.',
            'terms' => ['vienna', 'schönbrunn', 'schonbrunn', 'hofburg'],
        ],
        'salzburg' => [
            'name' => 'Salzburg', 'region' => 'Salzburgerland', 'lat' => 47.8095, 'lon' => 13.0550, 'image' => 'gallery/salzburg',
            'fact' => 'The Historic Centre of the City of Salzburg is a UNESCO World Heritage Site (1996) and the birthplace of Mozart, born here in 1756.',
            'terms' => ['salzburg'],
        ],
        'hallstatt' => [
            'name' => 'Hallstatt', 'region' => 'Upper Austria · Salzkammergut', 'lat' => 47.5622, 'lon' => 13.6493, 'image' => 'gallery/hallstatt',
            'fact' => 'A lakeside village that belongs to the Hallstatt–Dachstein / Salzkammergut Cultural Landscape, a UNESCO World Heritage Site since 1997.',
            'terms' => ['hallstatt', 'salzkammergut'],
        ],
        'wachau' => [
            'name' => 'The Wachau', 'region' => 'Lower Austria', 'lat' => 48.3944, 'lon' => 15.5189, 'image' => null,
            'fact' => 'A stretch of the Danube valley recognised as a UNESCO World Heritage cultural landscape (2000), known for its vineyards and apricot orchards.',
            'terms' => ['wachau', 'dürnstein', 'durnstein'],
        ],
        'melk' => [
            'name' => 'Melk', 'region' => 'Lower Austria', 'lat' => 48.2278, 'lon' => 15.3317, 'image' => 'gallery/wachau',
            'fact' => 'The gateway to the Wachau, home to the Benedictine Melk Abbey above the Danube.',
            'terms' => ['melk'],
        ],
        'innsbruck' => [
            'name' => 'Innsbruck', 'region' => 'Tyrol', 'lat' => 47.2692, 'lon' => 11.4041, 'image' => 'gallery/innsbruck',
            'fact' => 'The capital of Tyrol, set among the Nordkette mountains. Host city of the Winter Olympics in 1964 and 1976.',
            'terms' => ['innsbruck'],
        ],
        'zell-am-see' => [
            'name' => 'Zell am See', 'region' => 'Salzburgerland', 'lat' => 47.3250, 'lon' => 12.7940, 'image' => 'winter/zellamsee',
            'fact' => 'A resort town on the shore of Lake Zell (Zeller See), close to Kaprun and the Kitzsteinhorn glacier.',
            'terms' => ['zell am see', 'kaprun'],
        ],
        'grossglockner' => [
            'name' => 'The Grossglockner', 'region' => 'Carinthia · Salzburgerland · Hohe Tauern', 'lat' => 47.0742, 'lon' => 12.6939, 'image' => 'gallery/grossglockner',
            'fact' => 'Austria’s highest mountain at 3,798 m. The Grossglockner High Alpine Road climbs about 48 km through the Hohe Tauern National Park.',
            'terms' => ['grossglockner', 'großglockner', 'high alpine road'],
        ],
        'graz' => [
            'name' => 'Graz', 'region' => 'Styria', 'lat' => 47.0707, 'lon' => 15.4395, 'image' => null,
            'fact' => 'The capital of Styria. Its historic centre has been a UNESCO World Heritage Site since 1999.',
            'terms' => ['graz'],
        ],
        'kitzbuhel' => [
            'name' => 'Kitzbühel', 'region' => 'Tyrol', 'lat' => 47.4464, 'lon' => 12.3925, 'image' => 'winter/kitzbuhel',
            'fact' => 'A medieval town in the Tyrolean Alps, known for ski racing on the Hahnenkamm.',
            'terms' => ['kitzbühel', 'kitzbuhel'],
        ],
        'arlberg' => [
            'name' => 'The Arlberg', 'region' => 'Tyrol · Vorarlberg', 'lat' => 47.1286, 'lon' => 10.2640, 'image' => null,
            'fact' => 'An alpine region on the border of Tyrol and Vorarlberg, home to St. Anton and its skiing.',
            'terms' => ['arlberg', 'st. anton', 'st anton'],
        ],
        'bregenz' => [
            'name' => 'Bregenz', 'region' => 'Vorarlberg', 'lat' => 47.5031, 'lon' => 9.7471, 'image' => null,
            'fact' => 'The capital of Vorarlberg, on the shore of Lake Constance (the Bodensee).',
            'terms' => ['bregenz', 'lake constance'],
        ],
        'achensee' => [
            'name' => 'Achensee', 'region' => 'Tyrol', 'lat' => 47.4506, 'lon' => 11.7233, 'image' => null,
            'fact' => 'The largest lake in Tyrol, set between the Karwendel and Rofan mountains.',
            'terms' => ['achensee'],
        ],
    ];

    /** Known places a package really visits (matched over its text), with image URL and map link added. */
    public static function forPackage(Package $p): array
    {
        $hay = mb_strtolower(implode(' ', array_filter([
            $p->title, $p->summary,
            implode(' ', (array) ($p->highlights ?? [])),
            implode(' ', array_map(fn ($d) => ($d['title'] ?? '').' '.($d['body'] ?? ''), (array) ($p->itinerary ?? []))),
        ])));

        return self::match($hay);
    }

    /** Places named in free text (a tour type's description, for example). */
    public static function inText(string $text): array
    {
        return self::match(mb_strtolower($text));
    }

    /** Every place we have facts for. */
    public static function all(): array
    {
        return array_map(fn ($k) => self::decorate($k), array_keys(self::ALL));
    }

    private static function match(string $hay): array
    {
        $out = [];
        foreach (self::ALL as $key => $pl) {
            foreach ($pl['terms'] as $term) {
                if (preg_match('/(?<![\p{L}])'.preg_quote($term, '/').'(?![\p{L}])/u', $hay)) {
                    $out[] = self::decorate($key);
                    break;
                }
            }
        }

        return $out;
    }

    private static function decorate(string $key): array
    {
        $pl = self::ALL[$key] + ['key' => $key];
        $pl['imageUrl'] = ($pl['image'] && is_file(public_path('images/'.$pl['image'].'.webp'))) ? asset('images/'.$pl['image'].'.webp') : null;
        $pl['mapUrl'] = 'https://www.openstreetmap.org/?mlat='.$pl['lat'].'&mlon='.$pl['lon'].'#map=11/'.$pl['lat'].'/'.$pl['lon'];
        $pl['searchUrl'] = route('packages.index', ['q' => $pl['terms'][0]]);

        return $pl;
    }

    /** Card data for the shared package card partial: the package photo (uploaded, else the bundled one), the places visited and the price. */
    public static function card(Package $p): object
    {
        $names = collect($p->itinerary ?? [])
            ->map(fn ($d) => trim(\Illuminate\Support\Str::after($d['title'] ?? '', ':')))
            ->flatMap(fn ($t) => preg_split('/\s+(?:and|&)\s+|,\s*/', $t))
            ->map(fn ($t) => \Illuminate\Support\Str::ucfirst(trim(preg_replace('/^the\s+/i', '', $t))))
            ->filter()->unique()->values();

        return (object) [
            'p' => $p,
            'image' => $p->hero_image ? asset('storage/'.$p->hero_image) : asset('images/packages/'.$p->slug.'.webp'),
            'places' => $names,
            'price' => $p->price_from ? '$'.number_format($p->price_from) : null,
            'dest' => $p->destination?->slug,
        ];
    }
}

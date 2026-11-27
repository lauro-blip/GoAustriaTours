<?php

namespace App\Support;

/**
 * The home page's built-in sections as page-builder blocks (type "home_{key}", view resources/views/home/{key}.blade.php).
 * Admin > Pages > Home lists them in order: every text, photo and button label is editable, they can be moved and hidden
 * (the hero stays first), and normal blocks (App\Support\Blocks) can be inserted between them. Defaults = the original wording.
 * "{brand}" in a text is replaced with the brand name from Settings.
 */
class HomeSections
{
    /** Built-in sections that start hidden (switch them on in Admin > Pages > Home). */
    public const HIDDEN = ['moments'];

    /** Built-in order. */
    public const ORDER = ['hero', 'trust', 'band', 'why', 'ways', 'tours', 'difference', 'destinations', 'popular', 'cta', 'gallery', 'stories', 'moments', 'journal'];

    public static function types(): array
    {
        $t = fn (string $label, int $max = 160) => ['kind' => 'text', 'label' => $label, 'max' => $max];
        $ta = fn (string $label, int $max = 600) => ['kind' => 'textarea', 'label' => $label, 'max' => $max];
        $img = fn (string $label, string $ref, string $alt = '', bool $decorative = false) => ['kind' => 'image', 'label' => $label, 'default' => ['ref' => $ref, 'alt' => $alt], 'decorative' => $decorative];
        $head = fn (string $eyebrow, string $heading, string $em, string $lede) => [
            'eyebrow' => $t('Small label above the heading', 80) + ['default' => $eyebrow],
            'heading' => $t('Heading') + ['default' => $heading],
            'heading_em' => $t('Heading, gold italic part') + ['default' => $em],
            'lede' => $ta('Intro text') + ['default' => $lede],
        ];
        $section = fn (string $label, string $hint, array $fields, array $extra = []) => ['label' => $label, 'hint' => $hint, 'home' => true, 'fixed' => true, 'fields' => $fields] + $extra;
        $sp = fn (string $r) => 'site:winter/'.$r;

        return [
            'home_hero' => $section('Hero', 'The top of the page: photos, headline and the trip planner quiz. It always stays first.', [
                'h1_before' => $t('Headline, first words', 60) + ['default' => 'Your'],
                'h1_highlight' => $t('Headline, highlighted word (gold brushstroke)', 40) + ['default' => 'Austria'],
                'h1_after' => $t('Headline, after the highlighted word', 60) + ['default' => 'Trip,'],
                'h1_italic' => $t('Headline, second line (italic)', 80) + ['default' => 'Planned for You'],
                'sub' => $ta('Text under the headline', 300) + ['default' => 'Answer a few quick questions about how you like to travel, and a personal travel specialist will design your Austria itinerary.', 'help' => 'Ads that open /?tour=self-drive (etc.) show that tour type\'s tagline here instead.'],
                'quiz_tag' => $t('Quiz: small line on top', 60) + ['default' => 'Complimentary · No obligation'],
                'quiz_title' => $t('Quiz: title', 60) + ['default' => 'Plan my journey'],
                'slide1' => $img('Photo 1 (shown first)', $sp('hero-wagrain'), '', true),
                'slide2' => $img('Photo 2', $sp('hero-salzburg'), '', true),
                'slide3' => $img('Photo 3', $sp('hero-montafon'), '', true),
                'strip_label' => $t('"Where to?" strip label (empty hides the strip)', 30) + ['default' => 'Where to?'],
            ], ['pinned' => true]),
            'home_trust' => $section('Trust strip', 'Memberships, established line and review summary. The content comes from Admin > Trust & tracking and only shows when filled in.', []),
            'home_band' => $section('Four points band', 'Four short points under the hero.', [
                'items' => ['kind' => 'items', 'label' => 'Points', 'max_items' => 4, 'add' => 'Add point', 'fields' => [
                    'title' => $t('Title', 60), 'text' => $ta('Text', 200),
                ], 'default' => [
                    ['title' => 'Custom Itineraries', 'text' => 'Self drive, escorted or by coach: every route is tailored to your dates, pace and interests.'],
                    ['title' => 'Handpicked Stays', 'text' => 'Hotels chosen for their location and character, with the insight of local experts.'],
                    ['title' => 'Personal Contact', 'text' => 'A real person who knows your plans and answers your questions before and during your trip.'],
                    ['title' => 'Three Ways to Travel', 'text' => 'Self drive, escorted or coach. Choose the style that suits you and we plan the rest.'],
                ]],
            ]),
            'home_why' => $section('Why travel with us', 'A photo beside the story of how you plan.', [
                'image' => $img('Photo', $sp('hallstatt'), "Hallstatt's lakeside houses and church below snowy mountains"),
            ] + $head('Why travel with {brand}', 'Austria,', 'planned with care', 'From snow-dusted palaces to lakeside villages and Alpine peaks, we shape every journey around the way you want to travel.') + [
                'p1' => $ta('Paragraph 1', 800) + ['default' => 'Whether you would rather take the wheel, travel with a guide or sit back on a comfortable coach, we plan the route, the hotels and the pace together with you. Nothing is fixed until you are happy with it.'],
                'p2' => $ta('Paragraph 2', 800) + ['default' => 'Tell us your dates, your interests and how you like to travel, and a personal travel specialist will come back with an itinerary built around you, with no obligation.'],
                'button_label' => $t('Button label', 60) + ['default' => 'Get a free quote'],
            ]),
            'home_tours' => $section('Tours grid', 'All published journeys with region filters and pages of 6.', $head('Handpicked journeys', 'Our Top-Rated', 'Austrian Tours', 'Hand-built journeys through the cities, lakes and Alps, in every season.') + [
                'button_label' => $t('Button label', 60) + ['default' => 'Get a free quote'],
                'link_label' => $t('Link label (to all packages)', 60) + ['default' => 'View all packages'],
            ]),
            'home_difference' => $section('The difference + stats', 'A dark photo section with points and real counts (journeys, places, ways to travel).', [
                'image' => $img('Background photo', $sp('steinernes-meer'), '', true),
            ] + $head('The {brand} difference', 'Why travel', 'with us?', 'We plan Austria for travellers who would rather be looked after than left to work it out alone.') + [
                'points' => ['kind' => 'items', 'label' => 'Points', 'max_items' => 5, 'add' => 'Add point', 'fields' => ['text' => $t('Point', 140)], 'default' => [
                    ['text' => 'Routes shaped around your dates, pace and interests'],
                    ['text' => 'Handpicked hotels and local Austria expertise'],
                    ['text' => 'One personal contact from first enquiry to your return home'],
                ]],
                'button_label' => $t('Button label', 60) + ['default' => 'Get a free quote'],
                'link_label' => $t('Second link label', 60) + ['default' => 'Meet the team'],
                'link' => ['kind' => 'link', 'label' => 'Second link', 'default' => '/about'],
                'stat1_label' => $t('Stat 1 label (number of journeys)', 60) + ['default' => 'Journeys'],
                'stat1_text' => $t('Stat 1 text', 140) + ['default' => 'Itineraries to choose from, each open to change before you book.'],
                'stat2_label' => $t('Stat 2 label (number of places our journeys visit)', 60) + ['default' => 'Places to discover'],
                'stat2_text' => $t('Stat 2 text', 140) + ['default' => 'From imperial Vienna to the high peaks of Tyrol.'],
                'stat3_label' => $t('Stat 3 label (number of ways to travel)', 60) + ['default' => 'Ways to travel'],
                'stat3_text' => $t('Stat 3 text', 140) + ['default' => 'Self drive, escorted or by coach.'],
            ]),
            'home_destinations' => $section('Destinations', 'Tiles for the 6 most-visited places, with journey counts.', $head('Where to go', 'Explore Top', 'Destinations', 'The imperial cities, the high Alps, or a route entirely your own.') + [
                'link_label' => $t('Link under the tiles', 60) + ['default' => 'View all destinations'],
            ]),
            'home_popular' => $section('More itineraries', 'Six more journey cards (shown when there are more than six journeys).', $head('Handpicked itineraries', 'Austria,', 'Perfectly Planned', 'More of our most-loved itineraries, each one open to change before you book.')),
            'home_cta' => $section('Call to action (photo)', 'A full-width photo call to action with the quote button and phone number.', [
                'image' => $img('Background photo', $sp('hero-montafon'), '', true),
            ] + $head('Complimentary · No obligation', 'Tell Us Your Dream.', 'We Will Plan the Rest.', 'Share when you would like to travel and how you like to see a country, and a personal travel specialist will design your Austria itinerary.') + [
                'button_label' => $t('Button label', 60) + ['default' => 'Plan my journey'],
            ]),
            'home_ways' => $section('Ways to travel', 'Self drive / escorted / coach cards (from Admin > Tour types).', $head('Choose your style', 'Your Journey.', 'Your Way.', 'Drive at your own pace, travel with a guide, or ride in comfort. Every style is planned around you.')),
            'home_gallery' => $section('Photo gallery', 'A mosaic of up to 7 photos, each linking to matching journeys.', $head('The Austria gallery', 'Austria’s Finest Places,', 'Beautifully Curated', 'Snow-covered villages, frozen lakes and high Alpine views: the places our guests ask for first.') + [
                'items' => ['kind' => 'items', 'label' => 'Photos (7 fill the mosaic)', 'max_items' => 7, 'add' => 'Add photo', 'fields' => [
                    'image' => ['kind' => 'image', 'label' => 'Photo'],
                    'label' => $t('Small label (region)', 40),
                    'title' => $t('Title', 60),
                ], 'default' => collect([
                    ['hallstatt', 'Salzkammergut', 'Hallstatt in Winter'], ['zellamsee', 'Salzburgerland', 'Zell am See & Kaprun'],
                    ['xmas-salzburg', 'Salzburg', 'Advent Markets'], ['kitzbuhel', 'Tyrol', 'Kitzbühel Old Town'],
                    ['untersberg', 'Salzburg', 'Untersberg Views'], ['tauplitz', 'Styria', 'Tauplitz & the Grimming'],
                    ['schonbrunn', 'Vienna', 'Schönbrunn Palace'],
                ])->map(fn ($g) => ['image' => ['ref' => $sp($g[0]), 'alt' => $g[2].', '.$g[1]], 'label' => $g[1], 'title' => $g[2]])->all()],
                'button_label' => $t('Button label', 60) + ['default' => 'Plan my journey'],
            ]),
            'home_stories' => $section('Guest stories', 'Active stories from Admin > Testimonials (up to 6).', $head('Guest reviews', 'Guest', 'Stories', 'Words from travellers who let us shape their trip to Austria.')),
            'home_moments' => $section('Featured journeys', 'Six journey cards with photo, nights and price.', $head('Featured journeys', 'Moments Worth', 'Travelling For', 'Journeys built around the places you will remember longest.')),
            'home_journal' => $section('Journal', 'The three latest Journal posts.', $head('Stories and tips', 'Austria Travel', 'Journal', 'Ideas, advice and winter inspiration for planning your trip.') + [
                'button_label' => $t('Button label', 60) + ['default' => 'Read the journal'],
            ]),
        ];
    }

    /** Every built-in section in the original order, with default content. */
    public static function defaults(): array
    {
        return array_map(fn ($k) => self::blank($k), self::ORDER);
    }

    /** A built-in section with its default content (hidden when listed in HIDDEN). */
    private static function blank(string $key): array
    {
        return array_merge(Blocks::blank('home_'.$key), in_array($key, self::HIDDEN, true) ? ['hidden' => true] : []);
    }

    /**
     * Saved home blocks made safe to render/edit: each built-in section exactly once (missing ones added in their default
     * place, at the end), the hero first and never hidden.
     */
    public static function normalize(?array $blocks): array
    {
        if (empty($blocks)) {
            return self::defaults();
        }
        $seen = [];
        $out = [];
        foreach ($blocks as $b) {
            if (str_starts_with($b['type'], 'home_')) {
                if (isset($seen[$b['type']])) {
                    continue;
                }
                $seen[$b['type']] = true;
            }
            $out[] = $b;
        }
        foreach (self::ORDER as $k) {
            if (! isset($seen['home_'.$k])) {
                $out[] = self::blank($k);
            }
        }
        $hero = collect($out)->firstWhere('type', 'home_hero');
        $hero['hidden'] = false;

        return array_merge([$hero], array_values(array_filter($out, fn ($b) => $b['type'] !== 'home_hero')));
    }
}

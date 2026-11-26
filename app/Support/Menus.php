<?php

namespace App\Support;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Str;

/**
 * Admin > Menus: the header menu, header button, footer columns, footer call to action, bottom links, copyright and the
 * sticky consult bar. Saved as JSON in the settings "menu_header" and "menu_footer"; anything missing falls back to the
 * defaults below (the original site). Built-in header items keep their mega menus (layouts/nav/*); custom items are links.
 * "{brand}" and "{year}" in texts are replaced when shown.
 */
class Menus
{
    public const HEADER_ITEMS = [
        'home' => 'Home', 'destinations' => 'Destinations', 'vacations' => 'Austria Vacations',
        'styles' => 'Travel Style', 'about' => 'About Us', 'contact' => 'Contact us',
    ];

    /** Built-in items with a dropdown panel. */
    public const MEGA = ['destinations', 'vacations', 'styles', 'about'];

    public const FOOTER_COLUMNS = [
        'contact' => 'Contact details', 'destinations' => 'Destinations', 'styles' => 'Travel Styles', 'brand' => 'Logo and text',
    ];

    public static function defaultHeader(): array
    {
        return [
            'items' => collect(self::HEADER_ITEMS)->map(fn ($label, $key) => ['key' => $key, 'label' => $label, 'url' => '', 'hidden' => false])->values()->all(),
            'cta_label' => 'Plan your journey',
            'cta_url' => '',
        ];
    }

    public static function defaultFooter(): array
    {
        return [
            'cta_title' => 'Ready to Plan Your Perfect Austria Journey?',
            'cta_text' => 'Need help deciding? Speak directly with our Austria travel specialists.',
            'cta_button' => 'Get your free quote',
            'columns' => [
                ['key' => 'contact', 'title' => '', 'hidden' => false, 'links' => []],
                ['key' => 'destinations', 'title' => 'Destinations', 'hidden' => false, 'links' => []],
                ['key' => 'styles', 'title' => 'Travel Styles', 'hidden' => false, 'links' => []],
                ['key' => 'brand', 'title' => '', 'hidden' => false, 'links' => []],
            ],
            'brand_text' => 'Thoughtfully crafted Austria journeys by self drive, escorted tour and coach. Discover Austria beautifully with {brand}.',
            'bottom_links' => [
                ['label' => 'About us', 'url' => '/about'], ['label' => 'Journal', 'url' => '/journal'], ['label' => 'Contact us', 'url' => '/contact'],
                ['label' => 'Terms & Conditions', 'url' => '/terms-and-conditions'], ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                ['label' => 'Photo credits', 'url' => '/photo-credits'],
            ],
            'copyright' => '© {year} {brand}. All rights reserved.',
            'consult_text' => 'Consult with our Travel Specialists at',
            'consult_button' => 'Request a quote',
        ];
    }

    public static function header(): array
    {
        return self::cleanHeader(self::read('menu_header') ?? []);
    }

    public static function footer(): array
    {
        return self::cleanFooter(self::read('menu_footer') ?? []);
    }

    private static function read(string $key): ?array
    {
        $v = Setting::get($key);
        $d = is_string($v) ? json_decode($v, true) : null;

        return is_array($d) ? $d : null;
    }

    private static function text(mixed $v, int $max = 120): string
    {
        return Str::limit(trim(strip_tags((string) $v)), $max, '');
    }

    /** Header: every built-in item exactly once (missing ones added, hidden), custom links kept, at most 12 items. */
    public static function cleanHeader(array $in): array
    {
        $def = self::defaultHeader();
        $items = [];
        $seen = [];
        foreach (array_slice((array) ($in['items'] ?? $def['items']), 0, 12) as $it) {
            $key = (string) ($it['key'] ?? '');
            if (isset(self::HEADER_ITEMS[$key])) {
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $items[] = ['key' => $key, 'label' => self::text($it['label'] ?? '', 40) ?: self::HEADER_ITEMS[$key], 'url' => '', 'hidden' => (bool) ($it['hidden'] ?? false)];
            } elseif ($key === 'link') {
                $url = Blocks::cleanLink((string) ($it['url'] ?? ''));
                $label = self::text($it['label'] ?? '', 40);
                if ($url !== '' && $label !== '') {
                    $items[] = ['key' => 'link', 'label' => $label, 'url' => $url, 'hidden' => (bool) ($it['hidden'] ?? false)];
                }
            }
        }
        foreach (self::HEADER_ITEMS as $key => $label) {
            if (! isset($seen[$key])) {
                $items[] = ['key' => $key, 'label' => $label, 'url' => '', 'hidden' => true];
            }
        }

        return [
            'items' => $items,
            'cta_label' => self::text($in['cta_label'] ?? $def['cta_label'], 40),
            'cta_url' => Blocks::cleanLink((string) ($in['cta_url'] ?? '')),
        ];
    }

    public static function cleanFooter(array $in): array
    {
        $def = self::defaultFooter();
        $links = fn ($list, $max) => collect(array_slice((array) $list, 0, $max))
            ->map(fn ($l) => ['label' => self::text($l['label'] ?? '', 60), 'url' => Blocks::cleanLink((string) ($l['url'] ?? ''))])
            ->filter(fn ($l) => $l['label'] !== '' && $l['url'] !== '')->values()->all();

        $cols = [];
        $seen = [];
        foreach (array_slice((array) ($in['columns'] ?? $def['columns']), 0, 8) as $c) {
            $key = (string) ($c['key'] ?? '');
            if (isset(self::FOOTER_COLUMNS[$key])) {
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $cols[] = ['key' => $key, 'title' => self::text($c['title'] ?? '', 40), 'hidden' => (bool) ($c['hidden'] ?? false), 'links' => []];
            } elseif ($key === 'links') {
                $cols[] = ['key' => 'links', 'title' => self::text($c['title'] ?? '', 40), 'hidden' => (bool) ($c['hidden'] ?? false), 'links' => $links($c['links'] ?? [], 10)];
            }
        }
        foreach (self::FOOTER_COLUMNS as $key => $label) {
            if (! isset($seen[$key])) {
                $cols[] = ['key' => $key, 'title' => collect($def['columns'])->firstWhere('key', $key)['title'], 'hidden' => true, 'links' => []];
            }
        }

        $out = ['columns' => $cols, 'bottom_links' => $links($in['bottom_links'] ?? $def['bottom_links'], 12)];
        foreach (['cta_title' => 120, 'cta_text' => 240, 'cta_button' => 40, 'brand_text' => 400, 'copyright' => 160, 'consult_text' => 80, 'consult_button' => 40] as $k => $max) {
            $out[$k] = self::text(array_key_exists($k, $in) ? $in[$k] : $def[$k], $max);
        }

        return $out;
    }

    /** "{brand}" / "{year}" placeholders. */
    public static function fill(string $text, string $brand): string
    {
        return strtr($text, ['{brand}' => $brand, '{year}' => date('Y')]);
    }

    /** Full URL for a saved link (site paths become absolute, like route() output). */
    public static function href(string $url): string
    {
        return str_starts_with($url, '/') ? url($url) : $url;
    }

    /** Whether a custom link points at the page being viewed (for the gold "current" state). */
    public static function isCurrent(string $url): bool
    {
        if (! str_starts_with($url, '/')) {
            return false;
        }

        $target = rtrim((string) (parse_url($url, PHP_URL_PATH) ?: '/'), '/');
        $here = rtrim('/'.trim(request()->path(), '/'), '/');

        return ($target === '' ? '/' : $target) === ($here === '' ? '/' : $here);
    }

    /**
     * Links the admin can pick from (site pages + published custom pages).
     *
     * @return array<int, array{label:string, url:string}>
     */
    public static function linkChoices(): array
    {
        $fixed = [
            ['Home', '/'], ['Destinations', '/destinations'], ['Austria Vacations', '/austria-vacations'], ['Travel Styles', '/travel-styles'],
            ['All packages', '/packages'], ['Journal', '/journal'], ['Guest stories', '/testimonials'], ['About us', '/about'], ['Contact us', '/contact'],
            ['Terms & Conditions', '/terms-and-conditions'], ['Privacy Policy', '/privacy-policy'], ['Photo credits', '/photo-credits'],
        ];
        $pages = Page::live()->orderBy('title')->get(['title', 'slug'])->map(fn ($p) => [$p->title, '/'.$p->slug])->all();

        return array_map(fn ($l) => ['label' => $l[0], 'url' => $l[1]], array_merge($fixed, $pages));
    }
}

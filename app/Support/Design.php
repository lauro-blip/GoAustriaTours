<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Admin > Design: the public site's colours, fonts, button shape, heading case and text weight.
 * Saved as settings "design_*". The site's CSS reads everything from the variables at the top of `.t3` (home.css);
 * css() returns overrides ONLY for what differs from the brand defaults, so an untouched design adds nothing to the page.
 * Fonts are limited to the self-hosted ones and system fonts (no extra downloads, keeps Lighthouse scores).
 */
class Design
{
    public const DEFAULTS = [
        'navy' => '#0f3b59', 'dark' => '#0a2a40', 'accent' => '#dba12c', 'accent_text' => '#7a5505',
        'text' => '#3e251e', 'bg' => '#faf8f4', 'tint' => '#f1ece4', 'line' => '#dbd4cc', 'muted_accent' => '#7d929e',
        'font_heading' => 'playfair', 'font_body' => 'inter', 'buttons' => 'square', 'heading_case' => 'upper', 'body_weight' => '300',
    ];

    public const COLORS = [
        'navy' => ['Primary (headings, links)', 'Also the text colour on accent buttons.'],
        'dark' => ['Dark sections and overlays', 'Footer, photo overlays, menus.'],
        'accent' => ['Accent (buttons, gold details)', 'Buttons carry the primary colour as text.'],
        'accent_text' => ['Accent for small text', 'A darker accent that stays readable on the light background.'],
        'text' => ['Body text', ''],
        'bg' => ['Page background', ''],
        'tint' => ['Tinted sections', 'Alternate section background.'],
        'line' => ['Lines and borders', ''],
        'muted_accent' => ['Soft accent', 'Small decorative lines and icons.'],
    ];

    public const FONTS_HEADING = [
        'playfair' => ['Playfair Display (brand)', '"Playfair Display Variable", "Playfair Display", Georgia, serif'],
        'georgia' => ['Georgia (classic serif)', 'Georgia, "Times New Roman", serif'],
        'inter' => ['Inter (modern sans)', '"Inter Variable", "Inter", system-ui, sans-serif'],
    ];

    public const FONTS_BODY = [
        'inter' => ['Inter (brand)', '"Inter Variable", "Inter", system-ui, sans-serif'],
        'system' => ['System font (fastest)', 'system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif'],
        'georgia' => ['Georgia (serif)', 'Georgia, "Times New Roman", serif'],
    ];

    public const BUTTONS = ['square' => 'Square (brand)', 'soft' => 'Softly rounded', 'pill' => 'Pill'];
    public const HEADING_CASE = ['upper' => 'CAPITALS (brand)', 'normal' => 'Normal case'];
    public const BODY_WEIGHT = ['300' => 'Light (brand)', '400' => 'Regular (easier to read)'];

    /** Ready-made palettes (each keeps readable contrast for text and buttons). */
    public const PRESETS = [
        'brand' => ['label' => 'GoAustriaTours brand', 'navy' => '#0f3b59', 'dark' => '#0a2a40', 'accent' => '#dba12c', 'accent_text' => '#7a5505', 'text' => '#3e251e', 'bg' => '#faf8f4', 'tint' => '#f1ece4', 'line' => '#dbd4cc', 'muted_accent' => '#7d929e'],
        'alpine' => ['label' => 'Alpine forest', 'navy' => '#1f3d33', 'dark' => '#14281f', 'accent' => '#c8a25a', 'accent_text' => '#6e5219', 'text' => '#2f2a24', 'bg' => '#f8f6f1', 'tint' => '#ece8df', 'line' => '#d6d0c4', 'muted_accent' => '#8a9a8f'],
        'burgundy' => ['label' => 'Imperial burgundy', 'navy' => '#5a1f2b', 'dark' => '#3d141d', 'accent' => '#d4a548', 'accent_text' => '#7a5512', 'text' => '#33251f', 'bg' => '#faf7f3', 'tint' => '#f0e8e1', 'line' => '#ddd1c7', 'muted_accent' => '#a08a86'],
        'lake' => ['label' => 'Lake blue', 'navy' => '#1d4e6e', 'dark' => '#123349', 'accent' => '#e0b44f', 'accent_text' => '#7c5a0c', 'text' => '#2b3138', 'bg' => '#f7f9fa', 'tint' => '#e9eff2', 'line' => '#d3dde3', 'muted_accent' => '#7f98a6'],
        'midnight' => ['label' => 'Midnight & champagne', 'navy' => '#1b2235', 'dark' => '#0f1422', 'accent' => '#d8c08a', 'accent_text' => '#6b5a2e', 'text' => '#2a2a30', 'bg' => '#faf9f6', 'tint' => '#efede7', 'line' => '#dcd8cf', 'muted_accent' => '#8d93a3'],
    ];

    /** The saved design (defaults for anything not saved). An admin's unsaved preview wins when present. */
    public static function current(): array
    {
        if (auth('admin')->check() && is_array($p = session('design_preview'))) {
            return self::clean($p);
        }
        $saved = [];
        foreach (array_keys(self::DEFAULTS) as $k) {
            $v = Setting::get('design_'.$k);
            if ($v !== null && $v !== '') {
                $saved[$k] = $v;
            }
        }

        return self::clean($saved);
    }

    public static function isPreview(): bool
    {
        return auth('admin')->check() && is_array(session('design_preview'));
    }

    /** Only valid values survive; anything else falls back to the default. */
    public static function clean(array $in): array
    {
        $out = self::DEFAULTS;
        foreach (array_keys(self::COLORS) as $k) {
            $v = strtolower(trim((string) ($in[$k] ?? '')));
            if (preg_match('/^#[0-9a-f]{6}$/', $v)) {
                $out[$k] = $v;
            }
        }
        foreach (['font_heading' => self::FONTS_HEADING, 'font_body' => self::FONTS_BODY, 'buttons' => self::BUTTONS, 'heading_case' => self::HEADING_CASE, 'body_weight' => self::BODY_WEIGHT] as $k => $opts) {
            if (isset($in[$k]) && array_key_exists((string) $in[$k], $opts)) {
                $out[$k] = (string) $in[$k];
            }
        }

        return $out;
    }

    public static function isDefault(array $d): bool
    {
        return $d === self::DEFAULTS;
    }

    /** Whether the brand Playfair font is used (the layout only preloads it then). */
    public static function usesPlayfair(?array $d = null): bool
    {
        return ($d ?? self::current())['font_heading'] === 'playfair';
    }

    /** CSS overriding the `.t3` variables and a few rules. Empty when the design is the brand default. */
    public static function css(?array $d = null): string
    {
        $d ??= self::current();
        $def = self::DEFAULTS;
        $vars = [];
        $map = ['navy' => ['--navy', '--green'], 'dark' => ['--dark'], 'accent' => ['--accent'], 'accent_text' => ['--accent-d'], 'text' => ['--ink'], 'bg' => ['--cream'], 'tint' => ['--tint'], 'line' => ['--line'], 'muted_accent' => ['--ice']];
        foreach ($map as $k => $names) {
            if ($d[$k] !== $def[$k]) {
                foreach ($names as $n) {
                    $vars[] = "{$n}: {$d[$k]}";
                }
                if ($k === 'text') {
                    $vars[] = '--muted: color-mix(in srgb, '.$d['text'].' 82%, transparent)';
                }
            }
        }
        if ($d['font_heading'] !== $def['font_heading']) {
            $vars[] = '--head: '.self::FONTS_HEADING[$d['font_heading']][1];
        }
        if ($d['font_body'] !== $def['font_body']) {
            $vars[] = '--body: '.self::FONTS_BODY[$d['font_body']][1];
        }
        $css = $vars ? 'html .t3 { '.implode('; ', $vars).'; }' : '';
        if ($d['body_weight'] !== $def['body_weight']) {
            $css .= ' html .t3 { font-weight: '.$d['body_weight'].'; }';
        }
        if ($d['heading_case'] === 'normal') {
            $css .= ' html .t3 .h, html .t3 .pk-h, html .t3 .dst-copy .h { text-transform: none; letter-spacing: 0; }';
        }
        if ($d['buttons'] !== 'square') {
            $r = $d['buttons'] === 'pill' ? '999px' : '6px';
            $css .= " html .t3 .btn, html .t3 .hdr .cta, html .t3 .consult .rq, html .t3 .q-submit, html .t3 .pill { border-radius: {$r}; }";
        }

        return trim($css);
    }

    /** WCAG contrast ratio of two #rrggbb colours. */
    public static function contrast(string $a, string $b): float
    {
        $lum = function (string $hex) {
            $c = array_map(fn ($x) => hexdec($x) / 255, str_split(ltrim($hex, '#'), 2));
            $c = array_map(fn ($v) => $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4, $c);

            return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
        };
        [$l1, $l2] = [$lum($a), $lum($b)];

        return round((max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05), 2);
    }
}

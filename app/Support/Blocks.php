<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\Media;
use App\Models\Package;
use App\Models\TourType;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Page builder section blocks. ONE definition per block type drives the admin editor (field list), the server-side
 * cleaning of what is saved (sanitize) and the public rendering (resources/views/blocks/{type}.blade.php).
 * Field kinds: text, textarea, rich (Trix HTML, cleaned), image (a "media:ID" or "site:path" reference), select, number, link, items (repeater).
 */
class Blocks
{
    public const TONES = ['light' => 'Light (cream)', 'tint' => 'Tinted (warm)', 'dark' => 'Navy', 'photo' => 'Photo (choose below)'];

    /** Columns section layouts: key => [label, number of columns]. */
    public const LAYOUTS = ['1' => 'One column', '2' => 'Two equal', '2l' => 'Two: wide left', '2r' => 'Two: wide right', '3' => 'Three', '4' => 'Four'];
    public const LAYOUT_COLS = ['1' => 1, '2' => 2, '2l' => 2, '2r' => 2, '3' => 3, '4' => 4];

    /** Shared style options of every normal section (fixed CSS classes in inner.css "Section style"). */
    public static function styleFields(): array
    {
        return [
            'tone' => ['kind' => 'select', 'label' => 'Background', 'options' => self::TONES, 'default' => 'light', 'group' => 'style'],
            'bg_image' => ['kind' => 'image', 'label' => 'Background photo (when Background is Photo)', 'group' => 'style', 'decorative' => true],
            'spacing' => ['kind' => 'select', 'label' => 'Spacing above and below', 'options' => ['s' => 'Compact', 'm' => 'Normal', 'l' => 'Spacious'], 'default' => 'm', 'group' => 'style'],
            'width' => ['kind' => 'select', 'label' => 'Content width', 'options' => ['narrow' => 'Narrow', 'normal' => 'Normal', 'wide' => 'Wide'], 'default' => 'normal', 'group' => 'style'],
            'head_align' => ['kind' => 'select', 'label' => 'Heading alignment', 'options' => ['center' => 'Centered', 'left' => 'Left'], 'default' => 'center', 'group' => 'style'],
            'show_on' => ['kind' => 'select', 'label' => 'Show on', 'options' => ['all' => 'All devices', 'desktop' => 'Desktop and tablet only', 'mobile' => 'Mobile only'], 'default' => 'all', 'group' => 'style'],
        ];
    }

    /** Section classes for a block's style choices (only non-default choices add a class). */
    public static function styleClasses(array $d): string
    {
        $c = ['tint' => 'tint', 'dark' => 'blk-dark', 'photo' => 'blk-dark blk-photo'][$d['tone'] ?? 'light'] ?? '';
        $c .= ['s' => ' blk-sp-s', 'l' => ' blk-sp-l'][$d['spacing'] ?? 'm'] ?? '';
        $c .= ['narrow' => ' blk-w-n', 'wide' => ' blk-w-w'][$d['width'] ?? 'normal'] ?? '';
        $c .= ($d['head_align'] ?? 'center') === 'left' ? ' blk-hl' : '';
        $c .= ['desktop' => ' blk-hide-m', 'mobile' => ' blk-hide-d'][$d['show_on'] ?? 'all'] ?? '';

        return trim($c);
    }

    /** Elements of a Columns section. */
    public static function elements(): array
    {
        return [
            'heading' => ['label' => 'Heading', 'fields' => [
                'text' => ['kind' => 'text', 'label' => 'Heading', 'max' => 160],
                'size' => ['kind' => 'select', 'label' => 'Size', 'options' => ['l' => 'Large', 'm' => 'Medium', 's' => 'Small'], 'default' => 'm'],
            ]],
            'text' => ['label' => 'Text', 'fields' => ['body' => ['kind' => 'rich', 'label' => 'Text']]],
            'image' => ['label' => 'Image', 'fields' => [
                'image' => ['kind' => 'image', 'label' => 'Photo'],
                'ratio' => ['kind' => 'select', 'label' => 'Shape', 'options' => ['auto' => 'Original', 'land' => 'Landscape', 'square' => 'Square', 'port' => 'Portrait'], 'default' => 'land'],
                'caption' => ['kind' => 'text', 'label' => 'Caption', 'max' => 140],
                'link' => ['kind' => 'link', 'label' => 'Link (optional)'],
            ]],
            'button' => ['label' => 'Button', 'fields' => [
                'label' => ['kind' => 'text', 'label' => 'Label', 'max' => 60, 'default' => 'Get a free quote'],
                'link' => ['kind' => 'link', 'label' => 'Link', 'help' => 'Leave empty to open the quote form.'],
                'style' => ['kind' => 'select', 'label' => 'Style', 'options' => ['gold' => 'Gold button', 'outline' => 'Outline button', 'link' => 'Text link'], 'default' => 'gold'],
            ]],
            'list' => ['label' => 'List', 'fields' => [
                'items' => ['kind' => 'textarea', 'label' => 'Items (one per line)', 'max' => 1500],
                'icon' => ['kind' => 'select', 'label' => 'Bullet', 'options' => ['check' => 'Gold check', 'dot' => 'Dot', 'number' => 'Numbers'], 'default' => 'check'],
            ]],
            'quote' => ['label' => 'Quote', 'fields' => [
                'text' => ['kind' => 'textarea', 'label' => 'Quote', 'max' => 600],
                'author' => ['kind' => 'text', 'label' => 'Who said it', 'max' => 100],
            ]],
            'spacer' => ['label' => 'Spacer', 'fields' => [
                'size' => ['kind' => 'select', 'label' => 'Height', 'options' => ['s' => 'Small', 'm' => 'Medium', 'l' => 'Large'], 'default' => 'm'],
            ]],
            'video' => ['label' => 'Video', 'fields' => [
                'url' => ['kind' => 'link', 'label' => 'YouTube or Vimeo link', 'help' => 'Loads only when the visitor presses play, so the page stays fast.'],
                'poster' => ['kind' => 'image', 'label' => 'Cover photo (optional)', 'decorative' => true],
                'title' => ['kind' => 'text', 'label' => 'Video title (for screen readers)', 'max' => 120],
            ]],
        ];
    }

    /** A new element with its defaults. */
    public static function blankElement(string $type): array
    {
        $data = [];
        foreach (self::elements()[$type]['fields'] ?? [] as $k => $f) {
            $data[$k] = $f['kind'] === 'image' ? ['ref' => '', 'alt' => ''] : ($f['default'] ?? '');
        }

        return ['type' => $type, 'data' => $data];
    }

    /** YouTube / Vimeo embed URL (privacy-enhanced) for a link, or null. */
    public static function videoEmbed(string $url): ?string
    {
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?autoplay=1&rel=0';
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d{6,12})~', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1&dnt=1';
        }

        return null;
    }

    /** @return array<string, array{label:string, hint:string, fields:array<string, array<string, mixed>>}> */
    public static function types(): array
    {
        $eyebrow = ['kind' => 'text', 'label' => 'Small label above the heading', 'max' => 80];
        $heading = ['kind' => 'text', 'label' => 'Heading', 'max' => 160];

        return [
            'columns' => ['label' => 'Columns', 'hint' => 'One to four columns you fill with headings, text, images, buttons, lists, quotes, spacers and videos.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'layout' => ['kind' => 'select', 'label' => 'Layout', 'options' => self::LAYOUTS, 'default' => '2'],
                'valign' => ['kind' => 'select', 'label' => 'Vertical alignment', 'options' => ['top' => 'Top', 'center' => 'Middle'], 'default' => 'top'],
                'cols' => ['kind' => 'columns', 'label' => 'Columns'],
            ]],
            'text' => ['label' => 'Text', 'hint' => 'A heading with formatted text.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'body' => ['kind' => 'rich', 'label' => 'Text'],
                'align' => ['kind' => 'select', 'label' => 'Alignment', 'options' => ['left' => 'Left', 'center' => 'Centered'], 'default' => 'left'],
            ]],
            'image_text' => ['label' => 'Image + text', 'hint' => 'A photo beside a heading, text and an optional button.', 'fields' => [
                'image' => ['kind' => 'image', 'label' => 'Photo'],
                'side' => ['kind' => 'select', 'label' => 'Photo side', 'options' => ['left' => 'Left', 'right' => 'Right'], 'default' => 'left'],
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'body' => ['kind' => 'rich', 'label' => 'Text'],
                'button_label' => ['kind' => 'text', 'label' => 'Button label', 'max' => 60],
                'button_link' => ['kind' => 'link', 'label' => 'Button link', 'help' => 'A page on this site (/destinations), a full https:// address, or leave empty to open the quote form.'],
            ]],
            'cards' => ['label' => 'Cards', 'hint' => 'Two to six cards with a photo, title, text and link.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'intro' => ['kind' => 'textarea', 'label' => 'Intro text', 'max' => 600],
                'items' => ['kind' => 'items', 'label' => 'Cards', 'max_items' => 6, 'add' => 'Add card', 'fields' => [
                    'image' => ['kind' => 'image', 'label' => 'Photo'],
                    'title' => ['kind' => 'text', 'label' => 'Title', 'max' => 120],
                    'text' => ['kind' => 'textarea', 'label' => 'Text', 'max' => 400],
                    'link' => ['kind' => 'link', 'label' => 'Link (optional)'],
                ]],
            ]],
            'journeys' => ['label' => 'Journeys', 'hint' => 'Package cards picked automatically: all, by region, by way to travel, by travel style or featured.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'filter' => ['kind' => 'select', 'label' => 'Which journeys', 'options' => self::journeyFilters(), 'default' => 'all'],
                'limit' => ['kind' => 'number', 'label' => 'How many (max)', 'min' => 1, 'max' => 12, 'default' => 6],
            ]],
            'steps' => ['label' => 'How it works', 'hint' => 'Numbered steps (three works best).', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'items' => ['kind' => 'items', 'label' => 'Steps', 'max_items' => 4, 'add' => 'Add step', 'fields' => [
                    'title' => ['kind' => 'text', 'label' => 'Title', 'max' => 100],
                    'text' => ['kind' => 'textarea', 'label' => 'Text', 'max' => 300],
                ]],
            ]],
            'faq' => ['label' => 'FAQ', 'hint' => 'Questions and answers (also added to Google as FAQ data).', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'items' => ['kind' => 'items', 'label' => 'Questions', 'max_items' => 20, 'add' => 'Add question', 'fields' => [
                    'question' => ['kind' => 'text', 'label' => 'Question', 'max' => 200],
                    'answer' => ['kind' => 'textarea', 'label' => 'Answer', 'max' => 1500],
                ]],
            ]],
            'testimonials' => ['label' => 'Guest stories', 'hint' => 'Active guest stories from Admin > Testimonials.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'limit' => ['kind' => 'number', 'label' => 'How many (max)', 'min' => 1, 'max' => 9, 'default' => 3],
            ]],
            'gallery' => ['label' => 'Gallery', 'hint' => 'A grid of photos with optional captions.', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'items' => ['kind' => 'items', 'label' => 'Photos', 'max_items' => 12, 'add' => 'Add photo', 'fields' => [
                    'image' => ['kind' => 'image', 'label' => 'Photo'],
                    'caption' => ['kind' => 'text', 'label' => 'Caption', 'max' => 120],
                ]],
            ]],
            'cta' => ['label' => 'Call to action', 'hint' => 'A navy strip with a button that opens the quote form.', 'fields' => [
                'heading' => ['kind' => 'text', 'label' => 'Heading', 'max' => 160],
                'text' => ['kind' => 'textarea', 'label' => 'Text', 'max' => 400],
                'button_label' => ['kind' => 'text', 'label' => 'Button label', 'max' => 60, 'default' => 'Get a free quote'],
                'interest' => ['kind' => 'text', 'label' => 'Enquiry topic (shown to your team)', 'max' => 100],
            ]],
            'quote_form' => ['label' => 'Quote form', 'hint' => 'The enquiry form, on the page (same consent, reCAPTCHA and CRM sync as every form).', 'fields' => [
                'eyebrow' => $eyebrow, 'heading' => $heading,
                'text' => ['kind' => 'textarea', 'label' => 'Text beside the form', 'max' => 600],
                'button_label' => ['kind' => 'text', 'label' => 'Button label', 'max' => 60, 'default' => 'Send enquiry'],
            ]],
        ];
    }

    /** "all", "featured", "destination:tyrol", "tour_type:coach", "style:music" => label. */
    public static function journeyFilters(): array
    {
        $out = ['all' => 'All journeys', 'featured' => 'Featured journeys'];
        foreach (Destination::active()->get() as $d) {
            $out['destination:'.$d->slug] = 'Region: '.$d->name;
        }
        foreach (TourType::active()->get() as $t) {
            $out['tour_type:'.$t->slug] = 'Way to travel: '.$t->name;
        }
        foreach (TravelStyles::all() as $slug => $s) {
            $out['style:'.$slug] = 'Travel style: '.$s['name'];
        }

        return $out;
    }

    /** Every block type: the normal ones plus the home page's built-in sections (App\Support\HomeSections). */
    public static function all(): array
    {
        static $all = null;

        return $all ??= self::types() + HomeSections::types();
    }

    /** Field definitions; normal blocks get the shared "Background" option (home sections keep their own design). */
    public static function fields(string $type): array
    {
        $fields = self::all()[$type]['fields'] ?? [];

        return str_starts_with($type, 'home_') ? $fields : $fields + self::styleFields();
    }

    /** A new block with its default content (used by the admin editor and the home page defaults). */
    public static function blank(string $type): array
    {
        $data = [];
        foreach (self::fields($type) as $key => $f) {
            $data[$key] = match ($f['kind']) {
                'items' => $f['default'] ?? [],
                'columns' => [],
                'image' => $f['default'] ?? ['ref' => '', 'alt' => ''],
                default => $f['default'] ?? '',
            };
        }

        return ['type' => $type, 'data' => $data, 'hidden' => false];
    }

    /**
     * Clean blocks submitted by the editor: unknown types and fields are dropped, every value is cleaned for its kind.
     * Home page sections are only accepted when $home is true. "hidden" blocks are kept but not shown.
     *
     * @return array<int, array{type:string, data:array<string, mixed>, hidden:bool}>
     */
    public static function sanitize(mixed $raw, bool $home = false): array
    {
        $allowed = $home ? self::all() : self::types();
        $out = [];
        foreach (array_slice(is_array($raw) ? $raw : [], 0, 60) as $block) {
            $type = is_array($block) ? (string) ($block['type'] ?? '') : '';
            if (! isset($allowed[$type])) {
                continue;
            }
            $out[] = ['type' => $type, 'data' => self::cleanFields(self::fields($type), (array) ($block['data'] ?? [])), 'hidden' => (bool) ($block['hidden'] ?? false)];
        }

        return $out;
    }

    private static function cleanFields(array $fields, array $in): array
    {
        $data = [];
        foreach ($fields as $key => $f) {
            $v = $in[$key] ?? null;
            $data[$key] = match ($f['kind']) {
                'text' => Str::limit(trim(strip_tags((string) $v)), $f['max'] ?? 190, ''),
                'textarea' => Str::limit(trim(strip_tags((string) $v)), $f['max'] ?? 2000, ''),
                'rich' => self::cleanHtml((string) $v),
                'image' => self::cleanImage($v),
                'select' => array_key_exists((string) $v, $f['options']) ? (string) $v : ($f['default'] ?? array_key_first($f['options'])),
                'number' => max($f['min'] ?? 0, min($f['max'] ?? 100, (int) ($v ?: ($f['default'] ?? 0)))),
                'link' => self::cleanLink((string) $v),
                'items' => self::cleanItems($f, $v),
                'columns' => self::cleanColumns($v),
                default => null,
            };
        }

        return $data;
    }

    private static function cleanItems(array $f, mixed $v): array
    {
        $items = [];
        foreach (array_slice(is_array($v) ? $v : [], 0, $f['max_items'] ?? 12) as $item) {
            $clean = self::cleanFields($f['fields'], is_array($item) ? $item : []);
            $hasContent = collect($clean)->except(['alt'])->filter(fn ($x) => is_array($x) ? ! empty($x['ref']) : $x !== '' && $x !== null)->isNotEmpty();
            if ($hasContent) {
                $items[] = $clean;
            }
        }

        return $items;
    }

    /** Columns: up to 4 columns of up to 12 known elements each. */
    private static function cleanColumns(mixed $v): array
    {
        $out = [];
        foreach (array_slice(is_array($v) ? $v : [], 0, 4) as $col) {
            $els = [];
            foreach (array_slice((array) ($col['elements'] ?? []), 0, 12) as $el) {
                $type = is_array($el) ? (string) ($el['type'] ?? '') : '';
                if (isset(self::elements()[$type])) {
                    $els[] = ['type' => $type, 'data' => self::cleanFields(self::elements()[$type]['fields'], (array) ($el['data'] ?? []))];
                }
            }
            $out[] = ['elements' => $els];
        }

        return $out;
    }

    /** Images are stored as {ref, alt}. Only references to a real media item or a bundled site photo are kept. */
    private static function cleanImage(mixed $v): array
    {
        $ref = is_array($v) ? (string) ($v['ref'] ?? '') : '';
        $alt = is_array($v) ? Str::limit(trim(strip_tags((string) ($v['alt'] ?? ''))), 190, '') : '';
        if (preg_match('/^media:(\d+)$/', $ref, $m)) {
            return Media::whereKey((int) $m[1])->exists() ? ['ref' => $ref, 'alt' => $alt] : ['ref' => '', 'alt' => ''];
        }
        if (str_starts_with($ref, 'site:') && collect(self::siteImages())->contains('ref', $ref)) {
            return ['ref' => $ref, 'alt' => $alt];
        }

        return ['ref' => '', 'alt' => ''];
    }

    /** Relative paths, anchors, http(s), mailto and tel only (no javascript: etc.). */
    public static function cleanLink(string $v): string
    {
        $v = trim($v);
        if ($v === '' || preg_match('#^(/(?!/)|\#|https?://|mailto:|tel:)#i', $v)) {
            return Str::limit($v, 300, '');
        }

        return '';
    }

    /** Keeps simple formatting only: headings, paragraphs, lists, links, bold/italic, quotes. */
    public static function cleanHtml(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, '<p><br><h2><h3><h4><strong><b><em><i><ul><ol><li><a><blockquote><div>');
        $html = preg_replace('#\s(on\w+|style|class|id)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);

        return trim(preg_replace_callback('#<a\b[^>]*href\s*=\s*("([^"]*)"|\'([^\']*)\')[^>]*>#i', function ($m) {
            $href = self::cleanLink(html_entity_decode($m[2] !== '' ? $m[2] : ($m[3] ?? '')));

            return $href === '' ? '<a>' : '<a href="'.e($href).'">';
        }, (string) $html));
    }

    /**
     * Photos bundled with the site that can be used in blocks (no upload needed).
     *
     * @return array<int, array{ref:string, label:string, thumb:string}>
     */
    public static function siteImages(): array
    {
        static $list = null;
        if ($list !== null) {
            return $list;
        }
        $list = [];
        foreach (['winter', 'gallery', 'packages', 'journal'] as $dir) {
            foreach (glob(public_path("images/{$dir}/*.webp")) ?: [] as $file) {
                $name = basename($file, '.webp');
                if (preg_match('/-(768|1280|1600|thumb)$/', $name)) {
                    continue;
                }
                $thumb = collect(["{$name}-768", "{$name}-thumb", $name])->first(fn ($n) => is_file(public_path("images/{$dir}/{$n}.webp")));
                $list[] = ['ref' => "site:{$dir}/{$name}", 'label' => Str::headline($name), 'thumb' => asset("images/{$dir}/{$thumb}.webp")];
            }
        }

        return $list;
    }

    /**
     * Resolve an image value to what a template needs.
     *
     * @return array{src:string, srcset:?string, alt:string}|null
     */
    public static function image(mixed $v): ?array
    {
        $ref = is_array($v) ? (string) ($v['ref'] ?? '') : (string) $v;
        $alt = is_array($v) ? (string) ($v['alt'] ?? '') : '';
        if (preg_match('/^media:(\d+)$/', $ref, $m) && ($media = Media::find((int) $m[1]))) {
            $variants = collect((array) $media->variants)->sortKeys();

            return [
                'src' => asset('storage/'.$media->path),
                'srcset' => $variants->count() > 1 ? $variants->map(fn ($p, $w) => asset('storage/'.$p).' '.$w.'w')->implode(', ') : null,
                'alt' => $alt !== '' ? $alt : (string) $media->alt,
            ];
        }
        if (str_starts_with($ref, 'site:')) {
            $path = substr($ref, 5);
            if (! preg_match('#^[a-z0-9_-]+/[a-z0-9_-]+$#i', $path) || ! is_file(public_path("images/{$path}.webp"))) {
                return null;
            }
            $url = fn ($p) => asset("images/{$p}.webp");
            if (is_file(public_path("images/{$path}-1280.webp"))) {
                $set = [$url("{$path}-768").' 768w', $url("{$path}-1280").' 1280w'];
                if (is_file(public_path("images/{$path}-1600.webp"))) {
                    $set[] = $url("{$path}-1600").' 1600w';
                }
                $full = @getimagesize(public_path("images/{$path}.webp"));
                if ($full && $full[0] > 1600) {
                    $set[] = $url($path).' '.$full[0].'w';
                }

                return ['src' => $url("{$path}-1280"), 'srcset' => implode(', ', $set), 'alt' => $alt];
            }

            return ['src' => $url($path), 'srcset' => null, 'alt' => $alt];
        }

        return null;
    }

    /** Journeys for a "journeys" block. */
    public static function journeys(string $filter, int $limit): Collection
    {
        $q = Package::published()->with(['tourType', 'destination']);
        [$kind, $value] = array_pad(explode(':', $filter, 2), 2, null);
        match ($kind) {
            'featured' => $q->where('is_featured', true),
            'destination' => $q->whereHas('destination', fn ($d) => $d->where('slug', $value)),
            'tour_type' => $q->whereHas('tourType', fn ($t) => $t->where('slug', $value)),
            default => null,
        };
        $list = $q->get();
        if ($kind === 'style') {
            $list = $list->filter(fn ($p) => TravelStyles::matches($p, (string) $value))->values();
        }

        return $list->take($limit);
    }
}

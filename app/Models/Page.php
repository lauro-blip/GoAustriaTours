<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Built-in "system" pages (home, about, contact, legal, thank-you) have their own routes and cannot be deleted.
 * Pages created in Admin > Pages are built from section blocks (App\Support\Blocks) and live at /{slug}.
 */
class Page extends Model
{
    protected $guarded = [];
    protected $casts = ['sections' => 'array', 'blocks' => 'array', 'noindex' => 'boolean', 'is_system' => 'boolean', 'is_published' => 'boolean'];

    /** Where a custom page can be linked from: key => label. */
    public const NAV_LOCATIONS = ['' => 'Not in a menu', 'about' => 'About Us menu', 'footer' => 'Footer links'];

    /** System pages whose content can also take blocks (shown below their body). */
    public const BLOCKS_ON_SYSTEM = ['home', 'about', 'terms', 'privacy'];

    /** URLs already used by the site (and folders in public/): a new page can never take them. */
    public const RESERVED = [
        'home', 'admin', 'about', 'contact', 'testimonials', 'terms', 'privacy', 'terms-and-conditions', 'privacy-policy', 'photo-credits',
        'thank-you', 'journal', 'packages', 'destinations', 'austria-vacations', 'travel-styles', 'tours', 'enquiry', 'sitemap', 'robots',
        'build', 'images', 'fonts', 'storage', 'favicon', 'login', 'logout', 'api', 'up',
    ];

    public static function slug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public function scopeCustom($q)
    {
        return $q->where('is_system', false);
    }

    public function scopeLive($q)
    {
        return $q->where('is_system', false)->where('is_published', true);
    }

    /** Fetch a text section for the home page etc. */
    public function section(string $key, string $default = ''): string
    {
        return (string) (($this->sections ?? [])[$key] ?? $default);
    }

    public function url(): string
    {
        return match ($this->slug) {
            'home' => route('home'), 'about' => route('about'), 'contact' => route('contact'),
            'terms' => route('terms'), 'privacy' => route('privacy'), 'thank-you' => route('thank-you'),
            default => $this->is_system ? url($this->slug) : route('pages.show', $this->slug),
        };
    }

    public function takesBlocks(): bool
    {
        return ! $this->is_system || in_array($this->slug, self::BLOCKS_ON_SYSTEM, true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings. Use Setting::get('key', 'default').
 * Cached forever and flushed on every write.
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = [];

    public static function all_(): array
    {
        return Cache::rememberForever('site_settings', fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $v = static::all_()[$key] ?? null;
        return ($v === null || $v === '') ? $default : $v;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('site_settings');
    }
}

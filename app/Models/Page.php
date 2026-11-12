<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];
    protected $casts = ['sections' => 'array', 'noindex' => 'boolean'];

    public static function slug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /** Fetch a text section for the home page etc. */
    public function section(string $key, string $default = ''): string
    {
        return (string) (($this->sections ?? [])[$key] ?? $default);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];

    public function scopePublished($q)
    {
        return $q->where('is_published', true)->where('published_at', '<=', now())->orderByDesc('published_at');
    }

    public function getRouteKeyName(): string { return 'slug'; }

    /** Uploaded image, else the bundled sample image named after the slug. */
    public function imageUrl(): string
    {
        return $this->image ? asset('storage/'.$this->image) : asset('images/journal/'.$this->slug.'.webp');
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }
}

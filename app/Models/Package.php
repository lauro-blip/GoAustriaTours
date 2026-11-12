<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $guarded = [];
    protected $casts = [
        'highlights' => 'array', 'itinerary' => 'array', 'inclusions' => 'array',
        'exclusions' => 'array', 'faqs' => 'array',
        'is_published' => 'boolean', 'is_featured' => 'boolean', 'noindex' => 'boolean',
        'price_from' => 'decimal:2',
    ];

    public function tourType(): BelongsTo { return $this->belongsTo(TourType::class); }
    public function destination(): BelongsTo { return $this->belongsTo(Destination::class); }
    public function images(): HasMany { return $this->hasMany(PackageImage::class)->orderBy('sort_order'); }

    public function scopePublished($q) { return $q->where('is_published', true)->orderBy('sort_order'); }

    public function getRouteKeyName(): string { return 'slug'; }
}

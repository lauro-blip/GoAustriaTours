<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}

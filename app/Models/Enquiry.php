<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    public const STATUSES = ['new', 'contacted', 'quoted', 'won', 'lost'];

    protected $guarded = [];
    protected $casts = [
        'quiz_answers' => 'array', 'tracking_first' => 'array', 'tracking_last' => 'array',
        'consent_given' => 'boolean', 'consented_at' => 'datetime', 'ghl_synced_at' => 'datetime',
    ];

    public function package(): BelongsTo { return $this->belongsTo(Package::class); }

    public function getFullNameAttribute(): string { return trim("$this->first_name $this->last_name"); }
}

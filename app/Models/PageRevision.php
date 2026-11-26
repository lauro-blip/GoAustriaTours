<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A saved copy of a page (Admin > Pages > History). "content" holds the editable fields listed in Page::REVISION_FIELDS. */
class PageRevision extends Model
{
    protected $guarded = [];
    protected $casts = ['content' => 'array'];

    public const KEEP = 30;

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}

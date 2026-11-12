<?php

namespace App\Jobs;

use App\Models\Enquiry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Upserts the enquiry as a GoHighLevel contact. Implemented in Phase 5.
 * Disabled whenever GHL_API_TOKEN or GHL_LOCATION_ID is empty.
 */
class SyncEnquiryToGhl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public int $enquiryId) {}

    public static function enabled(): bool
    {
        return filled(config('goaustria.ghl.token')) && filled(config('goaustria.ghl.location_id'));
    }

    public function handle(): void
    {
        // Phase 5.
    }
}

<?php

namespace App\Jobs;

use App\Models\Enquiry;
use App\Services\SheetsClient;
use App\Support\SheetRow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Appends the enquiry to the Google Sheet (one tab per form). OFF whenever the web app URL or secret is empty.
 * Never blocks or fails the enquiry itself: failures are recorded on the enquiry and can be retried from the admin.
 */
class SyncEnquiryToSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public int $enquiryId) {}

    public static function enabled(): bool
    {
        return SheetsClient::configured();
    }

    /** Backoff (seconds) between retries. */
    public function backoff(): array
    {
        return [30, 120, 600, 1800];
    }

    public function handle(): void
    {
        $enquiry = Enquiry::find($this->enquiryId);
        if (! $enquiry) {
            return;
        }
        if (! self::enabled()) {
            $enquiry->update(['sheet_status' => 'disabled']);

            return;
        }

        try {
            (new SheetsClient)->send(SheetRow::tab($enquiry), SheetRow::row($enquiry));
            $enquiry->update(['sheet_status' => 'synced', 'sheet_error' => null, 'sheet_synced_at' => now()]);
        } catch (Throwable $e) {
            $message = mb_substr($e->getMessage(), 0, 500);
            Log::warning('Google Sheet sync failed', ['enquiry' => $enquiry->id, 'error' => $message]);

            if ($this->job && config('queue.default') !== 'sync' && $this->attempts() < $this->tries) {
                $enquiry->update(['sheet_status' => 'pending', 'sheet_error' => $message]);
                $this->release($this->backoff()[min($this->attempts() - 1, 3)]);

                return;
            }
            $enquiry->update(['sheet_status' => 'failed', 'sheet_error' => $message]);
        }
    }
}

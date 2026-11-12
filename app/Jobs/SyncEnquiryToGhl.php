<?php

namespace App\Jobs;

use App\Models\Enquiry;
use App\Services\GhlClient;
use App\Services\GhlException;
use App\Services\GhlPayload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Upserts the enquiry as a GoHighLevel contact. OFF whenever GHL_API_TOKEN or GHL_LOCATION_ID is empty.
 * Never blocks or fails the enquiry itself: failures are recorded on the enquiry and can be retried from the admin.
 */
class SyncEnquiryToGhl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 5;

    public function __construct(public int $enquiryId) {}

    public static function enabled(): bool
    {
        return GhlClient::configured();
    }

    /** Backoff (seconds) between retries. */
    public function backoff(): array
    {
        return [30, 120, 600, 1800];
    }

    public function handle(GhlPayload $builder): void
    {
        $enquiry = Enquiry::find($this->enquiryId);
        if (! $enquiry) {
            return;
        }
        if (! self::enabled()) {
            $enquiry->update(['ghl_status' => 'disabled']);

            return;
        }

        try {
            $client = new GhlClient;
            $ids = $client->fieldIds();

            // Returning contact? Keep their first-touch values.
            $existingContact = $client->findByEmail($enquiry->email);
            $existing = collect($existingContact['customFields'] ?? [])->mapWithKeys(fn ($f) => [($f['id'] ?? '') => $f['value'] ?? ($f['field_value'] ?? null)])->all();

            $built = $builder->build($enquiry, $ids, $existing);
            if ($built['missing']) {
                // Fields not created in GHL yet: re-read the list once, then carry on without them.
                $ids = $client->fieldIds(fresh: true);
                $built = $builder->build($enquiry, $ids, $existing);
                if ($built['missing']) {
                    Log::warning('GHL custom fields missing (run php artisan ghl:setup)', ['fields' => $built['missing']]);
                }
            }

            $result = $client->upsertContact($built['contact']);
            $client->addTags($result['id'], $built['tags']);

            $enquiry->update(['ghl_status' => 'synced', 'ghl_contact_id' => $result['id'], 'ghl_error' => null, 'ghl_synced_at' => now()]);
        } catch (Throwable $e) {
            $this->recordFailure($enquiry, $e);
        }
    }

    private function recordFailure(Enquiry $enquiry, Throwable $e): void
    {
        $message = mb_substr($e->getMessage(), 0, 500);
        $retryable = ! ($e instanceof GhlException) || $e->retryable();
        $canRetry = $retryable && $this->job && config('queue.default') !== 'sync' && $this->attempts() < $this->tries;

        Log::warning('GHL sync failed', ['enquiry' => $enquiry->id, 'attempt' => $this->job ? $this->attempts() : 1, 'error' => $message]);

        if ($canRetry) {
            $enquiry->update(['ghl_status' => 'pending', 'ghl_error' => $message]);
            $this->release($this->backoff()[min($this->attempts() - 1, 3)]);

            return;
        }

        $enquiry->update(['ghl_status' => 'failed', 'ghl_error' => $message]);
    }
}

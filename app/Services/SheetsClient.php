<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Sends one row to the Google Sheet web app (Apps Script). OFF whenever the URL or secret is empty. */
class SheetsClient
{
    public static function url(): ?string
    {
        return Setting::get('sheets_webhook_url') ?: (config('goaustria.sheets.url') ?: null);
    }

    public static function savedSecret(): ?string
    {
        $stored = Setting::get('sheets_secret');
        if (! $stored) {
            return null;
        }
        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function secret(): ?string
    {
        return static::savedSecret() ?: (config('goaustria.sheets.secret') ?: null);
    }

    public static function configured(): bool
    {
        return filled(static::url()) && filled(static::secret());
    }

    /**
     * Appends the row to the tab (created on first use; new columns are added automatically).
     * Rows carrying an "Enquiry ID" that is already in the tab are not added twice, so retries are safe.
     *
     * @param  array<string, string|null>  $row  column name => value
     */
    public function send(string $tab, array $row): void
    {
        $response = Http::asJson()->timeout(25)->post((string) static::url(), [
            'secret' => static::secret(),
            'tab' => $tab,
            'row' => $row,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Google Sheet web app answered HTTP '.$response->status().'.');
        }
        $json = $response->json();
        if (! is_array($json)) {
            throw new RuntimeException('Google Sheet web app did not answer with JSON. Check the URL is the /exec web app address and access is set to "Anyone".');
        }
        if (empty($json['ok'])) {
            throw new RuntimeException('Google Sheet web app refused the row: '.($json['error'] ?? 'unknown error'));
        }
    }
}

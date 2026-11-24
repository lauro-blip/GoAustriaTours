<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SheetsClient;
use App\Support\SheetRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/** Admin > Google Sheet: the Apps Script web app URL and the shared secret. Values saved here override the .env ones. */
class SheetsController extends Controller
{
    public function edit()
    {
        $saved = SheetsClient::savedSecret();

        return view('admin.sheets', [
            'on' => SheetsClient::configured(),
            'url' => Setting::get('sheets_webhook_url', ''),
            'secretHint' => $saved ? '...'.substr($saved, -3) : null,
            'envOn' => filled(config('goaustria.sheets.url')),
            'hasSaved' => filled(Setting::get('sheets_webhook_url')) || filled(Setting::get('sheets_secret')),
            'tabs' => array_values(SheetRow::TABS),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sheets_webhook_url' => ['nullable', 'url:https', 'max:500', 'regex:#^https://script\.google\.com/#'],
            'sheets_secret' => ['nullable', 'string', 'min:12', 'max:200', 'regex:/^\S+$/'],
        ], [
            'sheets_webhook_url.regex' => 'This must be the web app address from Apps Script (it starts with https://script.google.com/ and ends in /exec).',
            'sheets_secret.regex' => 'The secret must not contain spaces.',
            'sheets_secret.min' => 'Use a secret of at least 12 characters.',
        ]);

        if ($request->boolean('clear')) {
            Setting::put('sheets_webhook_url', null);
            Setting::put('sheets_secret', null);
            $message = 'Saved Google Sheet values removed.';
        } else {
            Setting::put('sheets_webhook_url', filled($data['sheets_webhook_url'] ?? null) ? $data['sheets_webhook_url'] : null);
            if (filled($data['sheets_secret'] ?? null)) {
                Setting::put('sheets_secret', Crypt::encryptString($data['sheets_secret']));
            }
            $message = 'Google Sheet settings saved. Use "Send a test row" to check them.';
        }

        return back()->with('status', $message);
    }

    /** Writes one clearly labelled row to a "Connection test" tab. */
    public function test()
    {
        if (! SheetsClient::configured()) {
            return back()->withErrors(['sheets' => 'Save both the web app URL and the secret first.']);
        }

        try {
            (new SheetsClient)->send('Connection test', ['Enquiry ID' => 'test-'.now()->format('YmdHis'), 'Sent At' => now()->format('Y-m-d H:i:s'), 'Note' => 'Test row from the website admin. Safe to delete.']);
        } catch (Throwable $e) {
            return back()->withErrors(['sheets' => $e->getMessage()]);
        }

        return back()->with('status', 'Connected. A test row was added to the "Connection test" tab of your sheet.');
    }
}

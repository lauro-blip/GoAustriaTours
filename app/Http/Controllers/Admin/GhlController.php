<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GhlClient;
use App\Services\GhlException;
use App\Support\GhlFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/** Admin > GoHighLevel: the Private Integration Token (PIT) and Location ID. Values saved here override the .env ones. */
class GhlController extends Controller
{
    public function edit()
    {
        $saved = GhlClient::savedToken();

        return view('admin.ghl', [
            'on' => GhlClient::configured(),
            'locationId' => Setting::get('ghl_location_id', ''),
            'tokenHint' => $saved ? '...'.substr($saved, -4) : null,
            'envLocation' => filled(config('goaustria.ghl.location_id')),
            'envToken' => filled(config('goaustria.ghl.token')),
            'hasSaved' => filled(Setting::get('ghl_api_token')) || filled(Setting::get('ghl_location_id')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ghl_location_id' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+$/'],
            'ghl_api_token' => ['nullable', 'string', 'max:600', 'regex:/^\S+$/'],
        ], [
            'ghl_location_id.regex' => 'The Location ID is letters and numbers only, with no spaces.',
            'ghl_api_token.regex' => 'The token must not contain spaces or line breaks.',
        ]);

        $oldLocation = (string) GhlClient::resolvedLocationId();

        if ($request->boolean('clear')) {
            Setting::put('ghl_api_token', null);
            Setting::put('ghl_location_id', null);
            $message = 'Saved GoHighLevel values removed. Any values in the server .env file are used instead.';
        } else {
            if (filled($data['ghl_api_token'] ?? null)) {
                Setting::put('ghl_api_token', Crypt::encryptString($data['ghl_api_token']));
            }
            Setting::put('ghl_location_id', filled($data['ghl_location_id'] ?? null) ? $data['ghl_location_id'] : null);
            $message = 'GoHighLevel settings saved. Use "Test connection" to check them.';
        }

        Cache::forget(GhlClient::fieldCacheKey($oldLocation));
        Cache::forget(GhlClient::fieldCacheKey((string) GhlClient::resolvedLocationId()));

        return back()->with('status', $message);
    }

    /** Read-only check: lists the contact custom fields. Creates nothing in GoHighLevel. */
    public function test()
    {
        if (! GhlClient::configured()) {
            return back()->withErrors(['ghl' => 'Save both the token and the Location ID first.']);
        }

        try {
            $fields = (new GhlClient)->fieldIds(fresh: true);
        } catch (GhlException $e) {
            return back()->withErrors(['ghl' => 'GoHighLevel rejected the connection: '.$e->getMessage()]);
        }

        $missing = count(array_diff(array_keys(GhlFields::all()), array_keys($fields)));
        $note = $missing
            ? " {$missing} expected custom fields are missing; run php artisan ghl:setup on the server to create them."
            : ' All expected custom fields exist.';

        return back()->with('status', 'Connected. Found '.count($fields).' custom fields.'.$note);
    }
}

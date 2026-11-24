<?php

namespace App\Support;

use App\Models\Enquiry;
use Illuminate\Support\Str;

/** Builds the Google Sheet tab name and the row (every stored field) for an enquiry. One tab per form. */
class SheetRow
{
    public const TABS = [
        'hero-quiz' => 'Hero Quiz',
        'header-modal' => 'Quote Popup',
        'package-quote' => 'Package Quote',
        'ask-question' => 'Ask a Question',
        'contact' => 'Contact',
    ];

    public static function tab(Enquiry $e): string
    {
        return self::TABS[$e->form_name] ?? Str::title(str_replace('-', ' ', (string) $e->form_name));
    }

    /** @return array<string, string> column name => value, in sheet column order */
    public static function row(Enquiry $e): array
    {
        $row = [
            'Enquiry ID' => (string) $e->id,
            'Submitted At' => $e->created_at?->format('Y-m-d H:i:s'),
            'Form' => self::tab($e),
            'First Name' => $e->first_name,
            'Last Name' => $e->last_name,
            'Email' => $e->email,
            'Phone' => $e->phone_e164,
            'Country Code' => $e->country_code,
            'Preferred Travel Date' => $e->travel_date,
            'Second Quote Preference' => $e->second_quote,
            'Special Request' => $e->special_request,
            'Package' => $e->package_name,
            'Page URL' => $e->page_url,
        ];

        foreach (($e->quiz_answers ?? []) as $key => $label) {
            $row[GhlFields::quiz((string) $key)] = (string) $label;
        }

        $row += [
            'Consent Given' => $e->consent_given ? 'Yes' : 'No',
            'Consent Timestamp' => $e->consented_at?->format('Y-m-d H:i:s'),
            'Consent Text Version' => $e->terms_version,
            'Consent Text' => $e->consent_text,
            'IP Address' => $e->ip_address,
            'User Agent' => $e->user_agent,
        ];

        $first = $e->tracking_first ?? [];
        $last = $e->tracking_last ?? $first;
        foreach (array_merge(Tracking::KEYS, ['referrer', 'landing_page']) as $key) {
            $row[GhlFields::first($key)] = $first[$key] ?? null;
            $row[GhlFields::last($key)] = $last[$key] ?? null;
        }

        return array_map(fn ($v) => $v === null ? '' : (string) $v, $row);
    }
}

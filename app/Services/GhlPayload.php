<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\Setting;
use App\Support\GhlFields;
use App\Support\Tracking;

/** Builds the GoHighLevel contact payload for an enquiry. Pure (no HTTP) so it is easy to test. */
class GhlPayload
{
    /**
     * @param  array<string,string>  $fieldIds     field name => GHL custom field id
     * @param  array<string,string>  $existing     field id => current value on an existing contact (first-touch is never overwritten)
     * @return array{contact: array, tags: array, missing: array}
     */
    public function build(Enquiry $e, array $fieldIds, array $existing = []): array
    {
        $values = [
            GhlFields::TRAVEL_DATE => $e->travel_date,
            GhlFields::SECOND_QUOTE => $e->second_quote,
            GhlFields::SPECIAL_REQUEST => $e->special_request,
            GhlFields::PACKAGE => $e->package_name,
            GhlFields::FORM_NAME => $e->form_name,
            GhlFields::CONSENT_GIVEN => $e->consent_given ? 'Yes' : 'No',
            GhlFields::CONSENT_AT => $e->consented_at?->toIso8601String(),
            GhlFields::CONSENT_VERSION => $e->terms_version,
        ];

        // Quiz answers: each as its own field, plus the full set as one text field.
        $answers = $e->quiz_answers ?? [];
        foreach ($answers as $key => $label) {
            $values[GhlFields::quiz($key)] = $label;
        }
        if ($answers) {
            $values[GhlFields::QUIZ_ALL] = collect($answers)->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n");
        }

        // Tracking: latest touch always updates; first touch is only written if GHL has no value yet.
        $first = $e->tracking_first ?? [];
        $last = $e->tracking_last ?? $first;
        foreach (array_keys(GhlFields::TRACKING) as $key) {
            $values[GhlFields::last($key)] = $last[$key] ?? null;

            $firstName = GhlFields::first($key);
            $firstId = $fieldIds[$firstName] ?? null;
            if (! ($firstId && filled($existing[$firstId] ?? null))) {
                $values[$firstName] = $first[$key] ?? null;
            }
        }

        $customFields = [];
        $missing = [];
        foreach ($values as $name => $value) {
            if (blank($value)) {
                continue;
            }
            if (! isset($fieldIds[$name])) {
                $missing[] = $name;
                continue;
            }
            $customFields[] = ['id' => $fieldIds[$name], 'field_value' => (string) $value];
        }

        $tags = array_values(array_filter([
            Setting::get('brand_name', config('app.name')),
            'source: '.(($last['utm_source'] ?? null) ?: 'direct'),
            $e->package_name ? 'package: '.$e->package_name : null,
            'form: '.$e->form_name,
        ]));

        return [
            'contact' => [
                'firstName' => $e->first_name, 'lastName' => $e->last_name, 'name' => $e->full_name,
                'email' => $e->email, 'phone' => $e->phone_e164, 'source' => 'Website: '.$e->form_name,
                'customFields' => $customFields,
            ],
            'tags' => $tags,
            'missing' => $missing,
        ];
    }
}

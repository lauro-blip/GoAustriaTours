<?php

namespace App\Support;

use App\Models\QuizStep;
use Illuminate\Support\Str;

/**
 * THE single source of truth for GoHighLevel custom field names.
 * Fields are matched by EXACT name. If you rename a field in GHL, change it here too (and vice versa).
 */
class GhlFields
{
    public const TEXT = 'TEXT';
    public const LARGE_TEXT = 'LARGE_TEXT';

    // Enquiry fields
    public const TRAVEL_DATE = 'Preferred Travel Date';
    public const SECOND_QUOTE = 'Second Quote Preference';
    public const SPECIAL_REQUEST = 'Special request';
    public const PACKAGE = 'Package';
    public const FORM_NAME = 'Form Name';
    public const QUIZ_ALL = 'Quiz - All Answers';
    public const CONSENT_GIVEN = 'Consent Given';
    public const CONSENT_AT = 'Consent Timestamp';
    public const CONSENT_VERSION = 'Consent Text Version';

    /**
     * Tracking attributes: enquiry tracking key => display label. In GoHighLevel the fields are named
     * "<key>_first" / "<key>_last" (e.g. utm_source_first), the names this account already uses.
     */
    public const TRACKING = [
        'utm_source' => 'UTM Source', 'utm_medium' => 'UTM Medium', 'utm_campaign' => 'UTM Campaign',
        'utm_term' => 'UTM Term', 'utm_content' => 'UTM Content', 'gclid' => 'GCLID', 'gbraid' => 'GBRAID',
        'wbraid' => 'WBRAID', 'referrer' => 'Referrer', 'landing_page' => 'Landing Page URL',
    ];

    /**
     * Latest-touch values that also go into the plain (unsuffixed) fields this GoHighLevel account already uses,
     * so existing workflows and reports keep working: tracking key => GHL field name.
     */
    public const LAST_TOUCH_PLAIN = [
        'utm_source' => 'utm_source', 'utm_medium' => 'utm_medium', 'utm_campaign' => 'utm_campaign',
        'utm_term' => 'utm_term', 'utm_content' => 'utm_content', 'gclid' => 'utm_gclid',
        'utm_device' => 'utm_device', 'utm_adposition' => 'utm_adposition', 'utm_adcreative' => 'utm_adcreative',
    ];

    public static function first(string $key): string
    {
        return $key.'_first';
    }

    public static function last(string $key): string
    {
        return $key.'_last';
    }

    public static function quiz(string $key): string
    {
        return 'Quiz - '.Str::title(str_replace('_', ' ', $key));
    }

    /** @return array<string, string> field name => GHL data type */
    public static function all(): array
    {
        $fields = [
            self::TRAVEL_DATE => self::TEXT, self::SECOND_QUOTE => self::TEXT, self::SPECIAL_REQUEST => self::LARGE_TEXT,
            self::PACKAGE => self::TEXT, self::FORM_NAME => self::TEXT, self::QUIZ_ALL => self::LARGE_TEXT,
            self::CONSENT_GIVEN => self::TEXT, self::CONSENT_AT => self::TEXT, self::CONSENT_VERSION => self::TEXT,
        ];

        foreach (array_keys(self::TRACKING) as $key) {
            $type = in_array($key, ['referrer', 'landing_page'], true) ? self::LARGE_TEXT : self::TEXT;
            $fields[self::first($key)] = $type;
            $fields[self::last($key)] = $type;
        }

        foreach (self::LAST_TOUCH_PLAIN as $name) {
            $fields[$name] = self::TEXT;
        }

        // One field per quiz step, so the list follows whatever is configured in the admin.
        try {
            foreach (QuizStep::orderBy('sort_order')->pluck('key') as $key) {
                $fields[self::quiz($key)] = self::TEXT;
            }
        } catch (\Throwable) {
            // Table may not exist yet (fresh install).
        }

        return $fields;
    }
}

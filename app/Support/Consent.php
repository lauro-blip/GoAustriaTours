<?php

namespace App\Support;

use App\Models\Setting;

/**
 * The consent wording is a legal/compliance requirement. Change it ONLY with sign-off,
 * and bump config('goaustria.terms_version') when you do.
 */
class Consent
{
    public static function companyName(): string
    {
        return (string) Setting::get('legal_company_name', Setting::get('brand_name', config('app.name')));
    }

    /** Plain text exactly as shown (and stored as evidence with every enquiry). */
    public static function text(?string $company = null): string
    {
        $company ??= self::companyName();

        return "I agree to the Terms and Conditions and Privacy Policy provided by {$company}. By providing my phone number, I consent to receive SMS messages from {$company} regarding my enquiry, booking updates, itinerary updates, reminders, and customer support. Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Consent is not a condition of purchase.";
    }

    /** HTML version for display: the two phrases become links that open in a new tab. */
    public static function html(?string $company = null): string
    {
        $company ??= self::companyName();
        $text = e(self::text($company));
        $terms = '<a href="'.e(route('terms')).'" target="_blank" rel="noopener" class="underline">Terms and Conditions</a>';
        $privacy = '<a href="'.e(route('privacy')).'" target="_blank" rel="noopener" class="underline">Privacy Policy</a>';

        return str_replace(['Terms and Conditions', 'Privacy Policy'], [$terms, $privacy], $text);
    }
}

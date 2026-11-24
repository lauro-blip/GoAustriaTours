<?php

namespace App\Support;

/**
 * Cheap checks for obviously fake phone numbers, on top of libphonenumber's validity check.
 * Keep in step with resources/js/phone.js (the same rules run in the browser).
 */
class PhoneQuality
{
    /** @param string $national national significant number, digits only (no country code, no trunk 0) */
    public static function isPlaceholder(string $national, int $countryCode): bool
    {
        $d = preg_replace('/\D/', '', $national);
        if (strlen($d) < 6) {
            return true;
        }
        // 5555555555, 0000000000
        if (preg_match('/^(\d)\1+$/', $d)) {
            return true;
        }
        // 1234567890, 0123456789, 9876543210
        if (str_contains('01234567890123456789', $d) || str_contains('98765432109876543210', $d)) {
            return true;
        }
        // 1212121212, 123123123123 (a short block repeated)
        if (preg_match('/^(\d{2,4})\1+\d{0,3}$/', $d) && strlen($d) >= 8 && strlen(count_chars($d, 3)) <= 4) {
            return true;
        }
        // North America: 555-01xx is reserved for fiction; 555-1212 is directory assistance
        if ($countryCode === 1 && strlen($d) === 10) {
            $exchange = substr($d, 3, 3);
            $line = substr($d, 6, 4);
            if ($exchange === '555' && (str_starts_with($line, '01') || $line === '1212')) {
                return true;
            }
        }

        return false;
    }
}

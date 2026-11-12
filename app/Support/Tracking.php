<?php

namespace App\Support;

use Illuminate\Http\Request;

/** First-touch / last-touch attribution captured from the URL and referrer. */
class Tracking
{
    public const FIRST_COOKIE = 'ga_first_touch';
    public const LAST_COOKIE = 'ga_last_touch';

    public const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'gbraid', 'wbraid'];

    public static function fromRequest(Request $request): array
    {
        $touch = [];
        foreach (self::KEYS as $key) {
            $touch[$key] = self::clean($request->query($key));
        }

        $referrer = (string) $request->headers->get('referer', '');
        $touch['referrer'] = parse_url($referrer, PHP_URL_HOST) === $request->getHost() ? null : self::clean($referrer, 500);
        $touch['landing_page'] = self::clean($request->fullUrl(), 500);

        return $touch;
    }

    public static function hasCampaignData(array $touch): bool
    {
        foreach (self::KEYS as $key) {
            if (! empty($touch[$key])) {
                return true;
            }
        }

        return false;
    }

    /** @return array{0: array, 1: array} [first, last]; last falls back to first. */
    public static function fromCookies(Request $request): array
    {
        $decode = fn ($name) => json_decode((string) $request->cookie($name), true) ?: [];
        $first = $decode(self::FIRST_COOKIE);
        $last = $decode(self::LAST_COOKIE) ?: $first;

        return [$first, $last];
    }

    private static function clean(?string $value, int $max = 255): ?string
    {
        $value = trim(strip_tags((string) $value));

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}

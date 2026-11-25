<?php

namespace App\Support;

/**
 * A stylised Austria outline (simplified border, lon/lat) for inline SVG maps.
 * Used by the package page (Places & map) and /destinations. Nothing loads from or links to another site.
 */
class AustriaMap
{
    public const W = 520;
    public const H = 270;

    private const BORDER = [[9.53, 47.53], [9.76, 47.59], [10.0, 47.48], [10.23, 47.38], [10.45, 47.56], [10.9, 47.53], [11.27, 47.43], [11.62, 47.59], [12.2, 47.61], [12.44, 47.68],
        [12.78, 47.67], [13.0, 47.47], [13.05, 47.7], [12.92, 47.83], [12.78, 48.0], [12.87, 48.2], [13.03, 48.26], [13.4, 48.37], [13.45, 48.57], [13.84, 48.77],
        [14.33, 48.56], [14.7, 48.6], [14.95, 48.79], [15.0, 49.0], [15.33, 48.99], [15.7, 48.86], [16.1, 48.75], [16.5, 48.79], [16.95, 48.62], [16.88, 48.47],
        [17.05, 48.2], [17.16, 48.0], [17.07, 47.71], [16.75, 47.68], [16.65, 47.45], [16.45, 47.25], [16.48, 47.0], [16.1, 46.86], [16.0, 46.68], [15.65, 46.7],
        [15.0, 46.63], [14.6, 46.43], [14.15, 46.44], [13.7, 46.52], [13.2, 46.57], [12.75, 46.65], [12.35, 46.73], [12.15, 46.94], [12.1, 47.07], [11.75, 46.97],
        [11.5, 47.01], [11.15, 46.95], [10.75, 46.79], [10.47, 46.86], [10.4, 47.0], [10.1, 46.85], [9.88, 46.92], [9.6, 47.05], [9.53, 47.27], [9.6, 47.4]];

    /** SVG x/y for a longitude/latitude. */
    public static function project(float $lon, float $lat): array
    {
        return [round(($lon - 9.45) * 0.676 * 100, 1), round((49.08 - $lat) * 100, 1)];
    }

    /** SVG path of the country outline. */
    public static function outline(): string
    {
        return 'M'.collect(self::BORDER)->map(fn ($p) => implode(' ', self::project($p[0], $p[1])))->implode('L').'Z';
    }

    /** viewBox with a 12px margin. */
    public static function viewBox(): string
    {
        return '-12 -12 '.(self::W + 24).' '.(self::H + 24);
    }
}

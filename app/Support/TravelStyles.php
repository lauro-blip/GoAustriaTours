<?php

namespace App\Support;

use App\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/** Travel styles (config/goaustria.php > travel_styles): which packages belong to which style, by simple word matching. */
class TravelStyles
{
    /** @return array<string, array<string, mixed>> keyed by style slug */
    public static function all(): array
    {
        return config('goaustria.travel_styles', []);
    }

    public static function find(?string $slug): ?array
    {
        $all = self::all();

        return $slug !== null && isset($all[$slug]) ? ['slug' => $slug] + $all[$slug] : null;
    }

    /** Everything searchable about a package, as one lowercase string (unicode kept, so "Schönbrunn" matches). */
    private static function haystack(Package $p): string
    {
        return Str::lower($p->title.' '.$p->summary.' '.json_encode($p->highlights ?? [], JSON_UNESCAPED_UNICODE).' '.json_encode($p->itinerary ?? [], JSON_UNESCAPED_UNICODE));
    }

    public static function matches(Package $p, string $slug): bool
    {
        $style = self::all()[$slug] ?? null;
        if (! $style) {
            return false;
        }
        $hay = self::haystack($p);
        foreach ($style['terms'] as $term) {
            if (str_contains($hay, Str::lower($term))) {
                return true;
            }
        }

        return false;
    }

    /**
     * For the menu: every style with how many of the given packages match and the first two of them.
     *
     * @param  Collection<int, Package>  $packages
     * @return array<int, array<string, mixed>>
     */
    public static function withPackages(Collection $packages): array
    {
        $out = [];
        foreach (self::all() as $slug => $style) {
            $matched = $packages->filter(fn (Package $p) => self::matches($p, $slug))->values();
            $out[] = ['slug' => $slug] + $style + ['count' => $matched->count(), 'packages' => $matched->take(2)];
        }

        return $out;
    }
}

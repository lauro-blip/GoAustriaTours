<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Support\Places;
use App\Support\TravelStyles;

/** /travel-styles: every travel style with the journeys that match it (App\Support\TravelStyles) and what they cover. */
class TravelStyleController extends Controller
{
    public function index()
    {
        $packages = Package::published()->with(['tourType'])->get();

        $styles = collect(TravelStyles::all())->map(function (array $st, string $slug) use ($packages) {
            $own = $packages->filter(fn (Package $p) => TravelStyles::matches($p, $slug))->values();

            return (object) [
                'slug' => $slug,
                'name' => $st['name'],
                'text' => $st['text'],
                'icon' => $st['icon'] ?? null,
                'packages' => $own,
                'places' => $own->flatMap(fn ($p) => Places::forPackage($p))->unique('key')->values(),
                'ways' => $own->pluck('tourType.name')->filter()->unique()->values(),
                'img' => self::image($st['image'] ?? 'winter/hero-wagrain'),
            ];
        })->values();

        return view('styles.index', ['styles' => $styles]);
    }

    /** src + srcset for an image under public/images: winter photos have -768 / -1280 sizes, gallery photos are one 1600px file. */
    private static function image(string $path): array
    {
        $url = fn (string $p) => asset('images/'.$p.'.webp');
        if (is_file(public_path('images/'.$path.'-1280.webp'))) {
            return ['src' => $url($path.'-1280'), 'small' => $url($path.'-768'), 'srcset' => $url($path.'-768').' 768w, '.$url($path.'-1280').' 1280w'];
        }
        $thumb = is_file(public_path('images/'.$path.'-thumb.webp')) ? $url($path.'-thumb') : $url($path);

        return ['src' => $url($path), 'small' => $thumb, 'srcset' => null];
    }
}

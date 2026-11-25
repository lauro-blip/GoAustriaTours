<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\TourType;
use App\Support\Places;

class TourTypeController extends Controller
{
    /** /austria-vacations: every active way to travel, compared side by side from real package data. */
    public function index()
    {
        $packages = Package::published()->with(['tourType', 'destination'])->get();

        $types = TourType::active()->get()->map(function (TourType $t) use ($packages) {
            $own = $packages->where('tour_type_id', $t->id)->values();
            $nights = $own->pluck('nights')->filter();
            $prices = $own->pluck('price_from')->filter(fn ($v) => $v > 0);

            return (object) [
                't' => $t,
                'packages' => $own,
                'nights' => $nights->isEmpty() ? null : ($nights->min() === $nights->max() ? (string) $nights->min() : $nights->min().'–'.$nights->max()),
                'price' => $prices->isEmpty() ? null : '$'.number_format($prices->min()),
                'regions' => $own->pluck('destination.name')->filter()->unique()->values(),
                'places' => $own->flatMap(fn ($p) => Places::forPackage($p))->unique('key')->values(),
                'image' => config('goaustria.tour_type_images.'.$t->slug, 'hero-wagrain'),
            ];
        });

        return view('tours.index', ['types' => $types]);
    }

    public function show(TourType $tourType)
    {
        abort_unless($tourType->is_active, 404);

        return view('tours.show', [
            'tourType' => $tourType,
            'packages' => Package::published()->where('tour_type_id', $tourType->id)->with('destination')->get(),
        ]);
    }
}

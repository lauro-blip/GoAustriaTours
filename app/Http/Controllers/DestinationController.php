<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Support\Places;

/** /destinations: every active region with the verified places its journeys visit and the journeys themselves. */
class DestinationController extends Controller
{
    public function index()
    {
        $packages = Package::published()->with(['tourType', 'destination'])->get();
        $seen = [];
        $n = 0;

        $regions = Destination::active()->get()->map(function (Destination $d) use ($packages, &$seen, &$n) {
            $own = $packages->where('destination_id', $d->id)->values();
            // Places the region's journeys really visit; each place belongs to the first region that reaches it.
            $places = $own->flatMap(fn ($p) => Places::forPackage($p))->unique('key')
                ->reject(fn ($pl) => isset($seen[$pl['key']]))->values()
                ->map(function ($pl) use (&$seen, &$n) {
                    $seen[$pl['key']] = true;

                    return $pl + ['n' => ++$n, 'xy' => \App\Support\AustriaMap::project($pl['lon'], $pl['lat'])];
                });

            return (object) [
                'd' => $d,
                'packages' => $own,
                'places' => $places,
                'image' => config('goaustria.destination_images.'.$d->slug, 'hero-wagrain'),
            ];
        });

        return view('destinations.index', ['regions' => $regions]);
    }
}

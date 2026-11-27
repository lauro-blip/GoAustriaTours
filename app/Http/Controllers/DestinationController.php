<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Support\AustriaMap;
use App\Support\Places;

/** /destinations: every place our published journeys visit (App\Support\Places), numbered on an inline map, with journey counts. No regions. */
class DestinationController extends Controller
{
    public function index()
    {
        $n = 0;
        $places = collect(Places::withJourneys(Package::published()->with('tourType')->get()))
            ->map(function ($pl) use (&$n) {
                return $pl + ['n' => ++$n, 'xy' => AustriaMap::project($pl['lon'], $pl['lat'])];
            });

        return view('destinations.index', ['places' => $places]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\TourType;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $tourType = TourType::active()->where('slug', $request->query('tour_type'))->first();
        $destination = Destination::active()->where('slug', $request->query('destination'))->first();

        // Free-text filter used by the Destinations mega menu (?q=Hallstatt). % _ and \ are stripped so it is a plain "contains" match.
        $q = mb_substr(trim((string) preg_replace('/[%_\\\\]/', '', (string) $request->query('q', ''))), 0, 60);
        $like = '%'.$q.'%';
        $style = \App\Support\TravelStyles::find((string) $request->query('style'));
        $collectionSlug = (string) $request->query('collection');
        $collection = config('goaustria.collections.'.$collectionSlug) ? ['slug' => $collectionSlug] + config('goaustria.collections.'.$collectionSlug) : null;

        $packages = Package::published()->with(['tourType', 'destination'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('summary', 'like', $like)
                ->orWhereRaw('CAST(highlights AS CHAR) LIKE ?', [$like])->orWhereRaw('CAST(itinerary AS CHAR) LIKE ?', [$like])))
            ->when($tourType, fn ($q) => $q->where('tour_type_id', $tourType->id))
            ->when($destination, fn ($q) => $q->where('destination_id', $destination->id))
            ->get()
            ->when($collection, fn ($all) => $all->whereIn('slug', $collection['packages'])->values())
            ->when($style, fn ($all) => $all->filter(fn ($p) => \App\Support\TravelStyles::matches($p, $style['slug']))->values());

        return view('packages.index', [
            'packages' => $packages,
            'tourTypes' => TourType::active()->get(),
            'destinations' => Destination::active()->get(),
            'activeTourType' => $tourType,
            'activeDestination' => $destination,
            'searchTerm' => $q,
            'activeStyle' => $style,
            'activeCollection' => $collection,
        ]);
    }

    public function show(Package $package)
    {
        abort_unless($package->is_published, 404);
        $package->load(['tourType', 'destination', 'images']);

        return view('packages.show', ['package' => $package]);
    }
}

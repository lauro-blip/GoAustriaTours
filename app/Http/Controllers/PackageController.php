<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Testimonial;
use App\Models\TourType;
use App\Support\Places;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $tourType = TourType::active()->where('slug', $request->query('tour_type'))->first();
        // ?place=hallstatt: journeys that really visit that place (App\Support\Places). Old ?destination= region links show all journeys.
        $place = Places::find((string) $request->query('place'));

        // Free-text search (?q=...). % _ and \ are stripped so it is a plain "contains" match.
        $q = mb_substr(trim((string) preg_replace('/[%_\\\\]/', '', (string) $request->query('q', ''))), 0, 60);
        $like = '%'.$q.'%';
        $style = \App\Support\TravelStyles::find((string) $request->query('style'));
        $collectionSlug = (string) $request->query('collection');
        $collection = config('goaustria.collections.'.$collectionSlug) ? ['slug' => $collectionSlug] + config('goaustria.collections.'.$collectionSlug) : null;

        $published = Package::published()->with(['tourType'])->get();
        $packages = Package::published()->with(['tourType'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('summary', 'like', $like)
                ->orWhereRaw('CAST(highlights AS CHAR) LIKE ?', [$like])->orWhereRaw('CAST(itinerary AS CHAR) LIKE ?', [$like])))
            ->when($tourType, fn ($q) => $q->where('tour_type_id', $tourType->id))
            ->get()
            ->when($place, fn ($all) => $all->filter(fn ($p) => in_array($place['key'], Places::keysFor($p), true))->values())
            ->when($collection, fn ($all) => $all->whereIn('slug', $collection['packages'])->values())
            ->when($style, fn ($all) => $all->filter(fn ($p) => \App\Support\TravelStyles::matches($p, $style['slug']))->values());

        return view('packages.index', [
            'packages' => $packages,
            'tourTypes' => TourType::active()->get(),
            'places' => Places::withJourneys($published),
            'activeTourType' => $tourType,
            'activePlace' => $place,
            'searchTerm' => $q,
            'activeStyle' => $style,
            'activeCollection' => $collection,
        ]);
    }

    public function show(Package $package)
    {
        abort_unless($package->is_published, 404);
        $package->load(['tourType', 'images']);

        // "You may also like": journeys sharing the most places first, then the same tour type.
        $mine = Places::keysFor($package);
        $related = Package::published()->with(['tourType'])->where('id', '!=', $package->id)->get()
            ->sortByDesc(fn ($p) => count(array_intersect($mine, Places::keysFor($p))) * 2 + ($p->tour_type_id === $package->tour_type_id ? 1 : 0))
            ->take(3)->values()->map(fn ($p) => Places::card($p));

        return view('packages.show', [
            'package' => $package,
            'related' => $related,
            'stories' => Testimonial::where('is_active', true)->orderBy('sort_order')->take(3)->get(),
        ]);
    }
}

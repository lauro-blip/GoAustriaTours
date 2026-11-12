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

        $packages = Package::published()->with(['tourType', 'destination'])
            ->when($tourType, fn ($q) => $q->where('tour_type_id', $tourType->id))
            ->when($destination, fn ($q) => $q->where('destination_id', $destination->id))
            ->get();

        return view('packages.index', [
            'packages' => $packages,
            'tourTypes' => TourType::active()->get(),
            'destinations' => Destination::active()->get(),
            'activeTourType' => $tourType,
            'activeDestination' => $destination,
        ]);
    }

    public function show(Package $package)
    {
        abort_unless($package->is_published, 404);
        $package->load(['tourType', 'destination', 'images']);

        return view('packages.show', ['package' => $package]);
    }
}

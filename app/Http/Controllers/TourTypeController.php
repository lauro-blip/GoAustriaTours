<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\TourType;

class TourTypeController extends Controller
{
    public function show(TourType $tourType)
    {
        abort_unless($tourType->is_active, 404);

        return view('tours.show', [
            'tourType' => $tourType,
            'packages' => Package::published()->where('tour_type_id', $tourType->id)->with('destination')->get(),
        ]);
    }
}

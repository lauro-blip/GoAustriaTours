<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'thisWeek' => Enquiry::where('created_at', '>=', now()->startOfWeek())->count(),
            'new' => Enquiry::where('status', 'new')->count(),
            'total' => Enquiry::count(),
            'recent' => Enquiry::latest('id')->take(8)->get(),
        ]);
    }
}

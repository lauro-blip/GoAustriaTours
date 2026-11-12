<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\TourType;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Enquiry forms: 5 per minute and 20 per hour per IP.
        RateLimiter::for('enquiries', fn (Request $r) => [
            Limit::perMinute(5)->by($r->ip()),
            Limit::perHour(20)->by($r->ip()),
        ]);

        // Share site settings and nav data with every public view.
        // Every public view gets $site (settings are cached) and $navTourTypes (one query per request).
        View::composer(['layouts.*', 'partials.*', 'pages.*', 'packages.*', 'tours.*', 'errors.*'], function ($view) {
            static $nav = null;
            $nav ??= TourType::active()->get(['name', 'slug']);
            $view->with(['site' => Setting::all_(), 'navTourTypes' => $nav]);
        });
    }
}

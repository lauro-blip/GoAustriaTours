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
        // Components read settings via Setting::get() directly; only layout + partials get $site.
        View::composer(['layouts.app', 'partials.*'], function ($view) {
            static $nav = null;
            $nav ??= TourType::active()->get(['name', 'slug']);
            $view->with(['site' => Setting::all_(), 'navTourTypes' => $nav]);
        });
    }
}

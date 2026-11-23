<?php

namespace App\Providers;

use App\Models\Destination;
use App\Models\Package;
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
            static $nav = null, $navDest = null, $navStyles = null, $navCollections = null, $navFeatured = null, $navPost = false, $navStory = false;
            $nav ??= TourType::active()->get(['id', 'name', 'slug', 'tagline', 'summary', 'image', 'image_alt'])->each(function ($t) {
                // up to 3 published packages per tour type for the mega menu (one small query per type, cached for the request)
                $t->setRelation('menuPackages', Package::published()->where('tour_type_id', $t->id)->limit(3)->get(['title', 'slug', 'nights']));
            });
            $navDest ??= Destination::active()->get(['name', 'slug', 'summary']);
            if ($navPost === false) {
                // About menu: the newest journal article and the first guest story
                $navPost = \App\Models\Post::published()->first();
                $navStory = \App\Models\Testimonial::where('is_active', true)->orderBy('sort_order')->first();
            }
            if ($navStyles === null) {
                // one query feeds the Travel Style, Signature Collections and Featured Packages menus
                $all = Package::published()->get(['title', 'slug', 'nights', 'summary', 'highlights', 'itinerary', 'is_featured', 'hero_image']);
                $navStyles = \App\Support\TravelStyles::withPackages($all);
                $navCollections = collect(config('goaustria.collections', []))->map(fn ($col, $slug) => [
                    'slug' => $slug, 'name' => $col['name'], 'text' => $col['text'],
                    'count' => $all->whereIn('slug', $col['packages'])->count(),
                ])->filter(fn ($col) => $col['count'] > 0)->values()->all();
                $navFeatured = $all->where('is_featured', true)->take(4)->values();
            }
            // Settings still holding a "[CONFIRM: ...]" placeholder are treated as empty on public pages, so they never show to visitors.
            $site = array_map(fn ($v) => is_string($v) && str_contains($v, '[CONFIRM') ? '' : $v, Setting::all_());
            $view->with(['site' => $site, 'navTourTypes' => $nav, 'navDestinations' => $navDest, 'navStyles' => $navStyles, 'navCollections' => $navCollections, 'navFeatured' => $navFeatured, 'navPost' => $navPost, 'navStory' => $navStory]);
        });
    }
}

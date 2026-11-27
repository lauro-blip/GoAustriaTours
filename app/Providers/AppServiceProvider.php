<?php

namespace App\Providers;

use App\Models\Package;
use App\Models\Post;
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

        // Every public view gets $site (settings are cached). Settings still holding a "[CONFIRM: ...]" placeholder are treated as empty, so they never show to visitors.
        View::composer(['layouts.*', 'partials.*', 'pages.*', 'packages.*', 'tours.*', 'destinations.*', 'styles.*', 'blocks.*', 'journal.*', 'errors.*'], function ($view) {
            $site = array_map(fn ($v) => is_string($v) && str_contains($v, '[CONFIRM') ? '' : $v, Setting::all_());
            $view->with([
                'site' => $site,
                'w' => fn (string $n, string $suffix = '') => asset('images/winter/'.$n.$suffix.'.webp'),
                'wayImg' => ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'],
            ]);
        });

        // The shared site layout (header, footer, quote popup) needs the same nav data on every page.
        View::composer('layouts.site', function ($view) {
            static $nav = null;
            $nav ??= (function () {
                $site = array_map(fn ($v) => is_string($v) && str_contains($v, '[CONFIRM') ? '' : $v, Setting::all_());
                $phone = trim((string) ($site['phone'] ?? ''));
                $all = Package::published()->with(['tourType'])->get();
                $feat = $all->sortByDesc('is_featured')->first();

                return [
                    'brand' => $site['brand_name'] ?? 'GoAustriaTours',
                    'img' => fn (string $p) => asset('images/'.$p),
                    'w' => fn (string $n, string $suffix = '') => asset('images/winter/'.$n.$suffix.'.webp'),
                    'wayImg' => ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'],
                    'winter' => ['hallstatt', 'zellamsee', 'kitzbuhel', 'steinernes-meer', 'tauplitz', 'untersberg', 'salzburg', 'schonbrunn', 'xmas-salzburg'],
                    'phone' => $phone,
                    'tel' => preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $phone)),
                    // Destinations = the places our published journeys visit (App\Support\Places), each with a journey count. No regions.
                    'navPlaces' => \App\Support\Places::withJourneys($all),
                    'navPost' => Post::published()->first(),
                    // Admin > Menus (header items, header button, footer columns and texts).
                    'menuHeader' => \App\Support\Menus::header(),
                    'menuFooter' => \App\Support\Menus::footer(),
                    // Pages from Admin > Pages placed in the About Us menu or the footer.
                    'navPages' => \App\Models\Page::live()->whereNotNull('nav_location')->orderBy('sort_order')->orderBy('title')->get(['slug', 'title', 'nav_location', 'intro'])->groupBy('nav_location'),
                    'navFeatured' => $feat ? \App\Support\Places::card($feat) : null,
                    'navStyles' => collect(\App\Support\TravelStyles::withPackages($all))->keyBy('slug')->all(),
                    'tourTypes' => TourType::active()->get(),
                    'pkgCounts' => [
                        'type' => $all->groupBy('tour_type_id')->map->count()->all(),
                    ],
                ];
            })();
            $view->with($nav);
        });
    }
}

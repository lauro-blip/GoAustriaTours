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

        // Every public view gets $site (settings are cached). Settings still holding a "[CONFIRM: ...]" placeholder are treated as empty, so they never show to visitors.
        View::composer(['layouts.*', 'partials.*', 'pages.*', 'packages.*', 'tours.*', 'journal.*', 'errors.*'], function ($view) {
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
                $published = Package::published()->with('destination:id,slug')->get(['id', 'destination_id', 'tour_type_id']);
                $phone = trim((string) ($site['phone'] ?? ''));

                return [
                    'brand' => $site['brand_name'] ?? 'GoAustriaTours',
                    'img' => fn (string $p) => asset('images/'.$p),
                    'w' => fn (string $n, string $suffix = '') => asset('images/winter/'.$n.$suffix.'.webp'),
                    'wayImg' => ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'],
                    'winter' => ['hallstatt', 'zellamsee', 'kitzbuhel', 'steinernes-meer', 'tauplitz', 'untersberg', 'salzburg', 'schonbrunn', 'xmas-salzburg'],
                    'phone' => $phone,
                    'tel' => preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $phone)),
                    'navPlaces' => config('goaustria.nav_places', []),
                    'destinations' => Destination::active()->get(),
                    'tourTypes' => TourType::active()->get(),
                    'pkgCounts' => [
                        'dest' => $published->groupBy(fn ($p) => $p->destination?->slug)->map->count()->all(),
                        'type' => $published->groupBy('tour_type_id')->map->count()->all(),
                    ],
                ];
            })();
            $view->with($nav);
        });
    }
}

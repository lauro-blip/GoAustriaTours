<?php

namespace App\Http\Middleware;

use App\Support\Tracking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Captures UTM / click-id parameters on GET page views.
 * First touch is written once (30 days); last touch is refreshed whenever new tracking params arrive.
 */
class CaptureTracking
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $request->ajax() || $request->is('admin*')) {
            return $response;
        }

        $touch = Tracking::fromRequest($request);
        $minutes = config('goaustria.tracking_cookie_days') * 24 * 60;

        if (! $request->cookie(Tracking::FIRST_COOKIE)) {
            Cookie::queue(Tracking::FIRST_COOKIE, json_encode($touch), $minutes);
        }
        if (Tracking::hasCampaignData($touch)) {
            Cookie::queue(Tracking::LAST_COOKIE, json_encode($touch), $minutes);
        }

        return $response;
    }
}

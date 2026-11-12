<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Page;
use App\Models\TourType;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = [[route('home'), now()]];

        foreach (Page::where('noindex', false)->whereNotIn('slug', ['home'])->get() as $page) {
            $route = match ($page->slug) {
                'terms' => route('terms'), 'privacy' => route('privacy'),
                default => url('/'.$page->slug),
            };
            $urls[] = [$route, $page->updated_at];
        }
        $urls[] = [route('packages.index'), now()];
        foreach (TourType::active()->get() as $t) {
            $urls[] = [route('tours.show', $t), $t->updated_at];
        }
        foreach (Package::published()->where('noindex', false)->get() as $p) {
            $urls[] = [route('packages.show', $p), $p->updated_at];
        }

        return response()->view('seo.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $body = "User-agent: *\nDisallow: /admin\nDisallow: /thank-you\nDisallow: /enquiry\n\nSitemap: ".route('sitemap')."\n";

        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}

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

        // Built-in pages + published pages from Admin > Pages (drafts and noindex pages are left out).
        foreach (Page::where('noindex', false)->whereNotIn('slug', ['home'])->where(fn ($q) => $q->where('is_system', true)->orWhere('is_published', true))->get() as $page) {
            $urls[] = [$page->url(), $page->updated_at];
        }
        $urls[] = [route('packages.index'), now()];
        $urls[] = [route('destinations.index'), now()];
        $urls[] = [route('tours.index'), now()];
        $urls[] = [route('styles.index'), now()];
        foreach (TourType::active()->get() as $t) {
            $urls[] = [route('tours.show', $t), $t->updated_at];
        }
        $urls[] = [route('journal.index'), now()];
        $urls[] = [route('testimonials'), now()];
        foreach (\App\Models\Post::published()->get() as $post) {
            $urls[] = [route('journal.show', $post), $post->updated_at];
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

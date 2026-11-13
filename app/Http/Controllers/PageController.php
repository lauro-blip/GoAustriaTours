<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Package;
use App\Models\Page;
use App\Models\QuizStep;
use App\Models\Testimonial;
use App\Models\TourType;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'page' => Page::slug('home'),
            'tourTypes' => TourType::active()->get(),
            'packages' => static::featuredPackages(),
            'carouselPackages' => Package::published()->with(['tourType', 'destination'])->get(),
            'posts' => \App\Models\Post::published()->take(3)->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get(),
            'quizSteps' => QuizStep::forQuiz(),
        ]);
    }

    /** Featured packages, 3 per page ('featured_page' query param). Used by the home page and the ajax endpoint. */
    public static function featuredPackages()
    {
        return Package::published()->where('is_featured', true)->with(['tourType', 'destination'])
            ->paginate(3, ['*'], 'featured_page')->withPath(route('home'));
    }

    public function featured()
    {
        return view('partials.featured-packages', ['packages' => static::featuredPackages()]);
    }

    public function about()
    {
        return view('pages.simple', ['page' => Page::slug('about') ?? abort(404)]);
    }

    public function contact()
    {
        return view('pages.contact', ['page' => Page::slug('contact') ?? abort(404)]);
    }

    public function legal(string $slug)
    {
        return view('pages.simple', ['page' => Page::slug($slug) ?? abort(404)]);
    }

    public function thankYou()
    {
        return view('pages.thank-you', ['page' => Page::slug('thank-you')]);
    }
}

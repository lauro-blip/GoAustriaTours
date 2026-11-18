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
        $tourTypes = TourType::active()->get();

        return view('pages.home', [
            'page' => Page::slug('home'),
            'tourTypes' => $tourTypes,
            // Google Ads message match: ?tour=self-drive (a real, active tour type slug) re-words the hero. Unknown values are ignored.
            'activeTour' => request()->filled('tour') ? $tourTypes->firstWhere('slug', (string) request('tour')) : null,
            'curatedPackages' => static::curatedPackages(),
            'featuredPackages' => Package::published()->where('is_featured', true)->with(['tourType', 'destination'])->get(),
            'posts' => \App\Models\Post::published()->take(3)->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
            // FAQs whose answer still holds a "[CONFIRM: ...]" placeholder stay hidden until the answer is written.
            'faqs' => Faq::where('is_active', true)->where('answer', 'not like', '%[CONFIRM%')->orderBy('sort_order')->take(6)->get(),   // the home FAQ shows the first 6
            'quizSteps' => QuizStep::forQuiz(),
        ]);
    }

    /** Curated packages (every published package), 6 per page ('curated_page' query param). Used by the home page and the ajax endpoint. */
    public static function curatedPackages()
    {
        return Package::published()->with(['tourType', 'destination'])
            ->paginate(6, ['*'], 'curated_page')->withPath(route('home'));
    }

    public function curated()
    {
        return view('partials.curated-packages', ['packages' => static::curatedPackages()]);
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

    /** Attribution for the Wikimedia Commons photos (CC BY / CC BY-SA require it). Keep in step with docs/reference/IMAGE_CREDITS.md. */
    public function photoCredits()
    {
        return view('pages.photo-credits');
    }

    public function thankYou()
    {
        return view('pages.thank-you', ['page' => Page::slug('thank-you')]);
    }
}

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
        return view('pages.home', $this->homeData(Page::slug('home')));
    }

    /** Everything the home page view needs; also used by the admin live preview with an unsaved page. */
    public function homeData(?Page $page): array
    {
        $tourTypes = TourType::active()->get();

        return [
            'page' => $page,
            'tourTypes' => $tourTypes,
            // Google Ads message match: ?tour=self-drive (a real, active tour type slug) re-words the hero. Unknown values are ignored.
            'activeTour' => request()->filled('tour') ? $tourTypes->firstWhere('slug', (string) request('tour')) : null,
            'packages' => Package::published()->with(['tourType', 'destination'])->get(),
            'quizSteps' => QuizStep::forQuiz(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->take(6)->get(),
            'destinations' => \App\Models\Destination::active()->get(),
            'posts' => \App\Models\Post::published()->take(3)->get(),
        ];
    }

    public function about()
    {
        return view('pages.simple', ['page' => Page::slug('about') ?? abort(404), 'tourTypes' => TourType::active()->get()]);
    }

    /** Every active guest story (the home page shows them as a pull-quote section). */
    public function testimonials()
    {
        return view('pages.testimonials', ['stories' => Testimonial::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function contact()
    {
        return view('pages.contact', ['page' => Page::slug('contact') ?? abort(404)]);
    }

    public function legal(string $slug)
    {
        return view('pages.simple', ['page' => Page::slug($slug) ?? abort(404)]);
    }

    /** A page built in Admin > Pages from section blocks. Drafts are visible only to a signed-in admin (as a preview). */
    public function show(Page $page)
    {
        abort_if($page->is_system, 404);
        abort_unless($page->is_published || auth('admin')->check(), 404);

        return view('pages.custom', ['page' => $page]);
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

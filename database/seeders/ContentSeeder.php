<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Faq;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\TourType;
use Illuminate\Database\Seeder;

/**
 * Starter content. EVERYTHING here is placeholder: prices, itineraries, testimonials and
 * legal text must be confirmed by the business (see docs/reference/CONTENT_TO_CONFIRM.md).
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->destinations();
        $this->tourTypes();
        $this->packages();
        $this->posts();
        $this->pages();
        $this->testimonials();
        $this->faqs();
    }

    private function destinations(): void
    {
        foreach ([
            ['Vienna & Salzburg', 'vienna-salzburg', 'Imperial palaces, concert halls, coffee houses and the Sound of Music city.', 'city'],
            ['Tyrol & the Alps', 'tyrol', 'Alpine valleys, lakes, cable cars and Innsbruck.', 'mountain'],
        ] as $i => [$name, $slug, $summary, $icon]) {
            Destination::firstOrCreate(['slug' => $slug], compact('name', 'summary', 'icon') + ['sort_order' => $i + 1]);
        }
    }

    private function tourTypes(): void
    {
        foreach ([
            ['Self Drive', 'self-drive', 'Your pace, your route', 'Hotels, car hire and a route booked for you. You set the pace and we handle the details.', 'Self drive tours of Austria | GoAustriaTours'],
            ['Escorted', 'escorted', 'A guide at the wheel', 'A private driver-guide and a flexible itinerary, so you can simply enjoy Austria.', 'Escorted tours of Austria | GoAustriaTours'],
            ['Coach', 'coach', 'Travel with others', 'Small-group coach touring with an expert guide and the best-known sights covered.', 'Coach tours of Austria | GoAustriaTours'],
        ] as $i => [$name, $slug, $tagline, $summary, $meta]) {
            TourType::firstOrCreate(['slug' => $slug], [
                'name' => $name, 'tagline' => $tagline, 'summary' => $summary, 'meta_title' => $meta,
                'meta_description' => $summary, 'sort_order' => $i + 1,
                'body' => '<p>[CONFIRM: tour type description for '.$name.' tours]</p>',
            ]);
        }
    }

    private function posts(): void
    {
        foreach (SamplePostData::all() as $p) {
            \App\Models\Post::firstOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'], 'category' => $p['category'], 'excerpt' => $p['excerpt'], 'body' => $p['body'],
                'image_alt' => $p['image_alt'], 'is_published' => true, 'published_at' => now()->subDays($p['days_ago']),
            ]);
        }
    }

    private function packages(): void
    {
        $this->syncSamplePackages(false);
    }

    /** Seeds the 6 sample packages. $overwrite = true refreshes existing rows (local dev only: it replaces admin edits). */
    public function syncSamplePackages(bool $overwrite): void
    {
        $samples = [
            ['self-drive', 'vienna-salzburg', 'imperial-austria-self-drive'],
            ['self-drive', 'tyrol', 'alpine-lakes-self-drive'],
            ['escorted', 'vienna-salzburg', 'vienna-salzburg-private-escorted'],
            ['escorted', 'tyrol', 'tyrol-private-escorted'],
            ['coach', 'vienna-salzburg', 'classic-austria-by-coach'],
            ['coach', 'tyrol', 'alpine-highlights-by-coach'],
        ];

        foreach ($samples as $i => [$type, $dest, $slug]) {
            $d = SamplePackageData::all()[$slug];
            $row = [
                'title' => $d['title'],
                'tour_type_id' => TourType::where('slug', $type)->value('id'),
                'destination_id' => Destination::where('slug', $dest)->value('id'),
                'nights' => $d['nights'],
                'price_from' => $d['price_from'], // SAMPLE price [CONFIRM]
                'currency' => 'USD',
                'summary' => $d['summary'],
                'description' => '<p>'.e($d['summary']).' SAMPLE CONTENT. [CONFIRM: full description.]</p>',
                'highlights' => $d['highlights'],
                'itinerary' => $d['itinerary'],
                'inclusions' => $d['inclusions'],
                'exclusions' => SamplePackageData::exclusions(),
                'faqs' => [],
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => $i + 1,
            ];
            $overwrite ? Package::updateOrCreate(['slug' => $slug], $row) : Package::firstOrCreate(['slug' => $slug], $row);
        }
    }

    private function pages(): void
    {
        $company = '[Company Name]';
        $pages = [
            'home' => ['Austria Tours | Self Drive, Escorted & Coach | GoAustriaTours', 'Home',
                'Self drive, escorted and coach tours of Austria planned by local experts. Get a free quote.', null, [
                    'hero_title' => 'Your Dream Austria, Planned Around You',
                    'hero_subtitle' => 'Self drive, escorted or coach. Get a free, personal quote.',
                    'why_title' => 'Why travellers choose us',
                    'cta_title' => 'Ready to see Austria?',
                    'cta_text' => 'Answer a few quick questions and a local expert will send you a tailored quote.',
                ]],
            'about' => ['About GoAustriaTours', 'About us', 'Meet the team behind GoAustriaTours.',
                '<p>[CONFIRM: about us copy. Who you are, where you are based, years in business.]</p>', null],
            'contact' => ['Contact GoAustriaTours', 'Contact us', 'Get in touch for a free Austria tour quote.',
                '<p>Tell us about your trip and we will reply with a personal quote.</p>', null],
            'terms' => ['Terms and Conditions | GoAustriaTours', 'Terms and Conditions', 'Terms and Conditions.',
                "<p><strong>STARTER TEMPLATE ONLY. Have this reviewed by a qualified professional before launch.</strong></p><h2>1. Introduction</h2><p>These terms apply to your use of this website and to enquiries made with {$company}.</p><h2>2. Quotes and bookings</h2><p>[CONFIRM: booking, payment, cancellation and refund terms.]</p><h2>3. SMS messaging</h2><p>By giving your phone number you consent to receive SMS messages from {$company} about your enquiry, booking updates, itinerary updates, reminders and customer support. Message and data rates may apply. Reply STOP to unsubscribe or HELP for assistance. Consent is not a condition of purchase.</p><h2>4. Liability</h2><p>[CONFIRM: liability terms.]</p>", null],
            'privacy' => ['Privacy Policy | GoAustriaTours', 'Privacy Policy', 'Privacy Policy.',
                "<p><strong>STARTER TEMPLATE ONLY. Have this reviewed by a qualified professional before launch.</strong></p><h2>1. What we collect</h2><p>{$company} collects the details you submit in our forms (name, email, phone, trip preferences), technical data (IP address, browser) and advertising attribution data (UTM parameters, click IDs).</p><h2>2. How we use it</h2><p>To respond to your enquiry, prepare quotes, contact you by email, phone and SMS, and measure our advertising.</p><h2>3. Sharing</h2><p>[CONFIRM: processors and partners, for example CRM and email providers.]</p><h2>4. Your rights</h2><p>[CONFIRM: access, deletion and contact details.]</p>", null],
            'thank-you' => ['Thank you | GoAustriaTours', 'Thank you', 'Thank you for your enquiry.', null, null],
        ];

        foreach ($pages as $slug => [$metaTitle, $title, $desc, $body, $sections]) {
            Page::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'meta_title' => $metaTitle, 'meta_description' => $desc,
                'body' => $body, 'sections' => $sections, 'noindex' => $slug === 'thank-you',
            ]);
        }
    }

    private function testimonials(): void
    {
        if (Testimonial::count()) {
            return;
        }
        // Supplied by the business owner.
        foreach ([
            ['Michael R.', 'New York', 'The Alpine Railway journey was the trip of a lifetime. Seamless from start to finish.'],
            ['Sarah & James', 'London', 'Every detail was perfect — from our Vienna opera night to the quiet morning in Hallstatt.'],
            ['The Nguyen Family', 'Sydney', "Our guide in Salzburg made history come alive. We're already planning our return."],
        ] as $i => [$name, $location, $quote]) {
            Testimonial::create([
                'name' => $name, 'location' => $location, 'quote' => $quote,
                'is_placeholder' => false, 'is_active' => true, 'sort_order' => $i + 1,
            ]);
        }
    }

    private function faqs(): void
    {
        if (Faq::count()) {
            return;
        }
        foreach ([
            ['How does it work?', 'Tell us a little about your trip. A local expert prepares a personal quote and itinerary, usually within [CONFIRM: response time].'],
            ['Is there any obligation to book?', 'No. Quotes are free and there is no obligation.'],
            ['When is the best time to visit Austria?', 'Summer suits hiking and the lakes, autumn the colours and wine harvest, winter the Christmas markets and skiing, spring the blossoms and fewer crowds.'],
            ['Can I tailor a tour?', '[CONFIRM: customisation options.]'],
            ['What is included in the price?', '[CONFIRM: standard inclusions.]'],
        ] as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'sort_order' => $i + 1]);
        }
    }
}

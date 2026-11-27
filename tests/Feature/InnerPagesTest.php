<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Support\Places;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Inner pages share the home page's header, footer and quote popup (layouts.site) and show verified place information. */
class InnerPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_every_public_page_uses_the_shared_header_footer_and_quote_popup(): void
    {
        foreach (['/packages', '/packages/imperial-austria-self-drive', '/tours/self-drive', '/destinations', '/austria-vacations', '/travel-styles', '/journal', '/contact', '/about', '/testimonials', '/photo-credits', '/terms-and-conditions'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('class="hdr"', $html, $url);
            $this->assertStringContainsString('class="foot"', $html, $url);
            $this->assertStringContainsString('data-qm', $html, $url);
            $this->assertStringContainsString('class="pbanner', $html, $url);
            $this->assertStringContainsString('id="mega-', str_replace('class="mega"', 'id="mega-x"', $html), $url);
        }
    }

    public function test_header_menus_list_destinations_tour_types_and_travel_styles(): void
    {
        $html = $this->get('/journal')->assertOk()->getContent();
        $this->assertStringContainsString('/packages?place=hallstatt', $html);   // destinations are places (no regions)
        $this->assertStringContainsString('/tours/escorted', $html);
        $this->assertStringContainsString('/packages?style=', $html);
        $this->assertStringContainsString('Plan your journey', $html);
    }

    public function test_package_page_has_details_itinerary_tabs_and_an_enquire_form(): void
    {
        $html = $this->get('/packages/imperial-austria-self-drive')->assertOk()
            ->assertSee('Your route')
            ->assertDontSee('Package details')
            ->assertSee('Places on this journey')
            ->assertSee('Historic Centre of the City of Salzburg is a UNESCO World Heritage Site', false)
            ->assertSee('Itinerary details')
            ->assertSee('Simple and personal')
            ->assertSee('You may also like')
            ->assertSee('Ready to plan Imperial Austria Self Drive?')
            ->assertSee('name="form_name" value="package-quote"', false)
            ->assertSee('name="group_size"', false)
            ->assertDontSee('name="form_name" value="ask-question"', false)
            ->getContent();

        $this->assertStringContainsString('data-ptabs', $html);
        $this->assertStringContainsString('role="tabpanel"', $html);
        $this->assertGreaterThanOrEqual(5, substr_count($html, 'data-package-id="'));
        $this->assertStringNotContainsString('openstreetmap.org', $html);
        $this->assertStringNotContainsString('View on map', $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_tour_type_page_has_no_sidebar_form_and_a_contextual_cta(): void
    {
        $html = $this->get('/tours/self-drive')->assertOk()
            ->assertDontSee('name="form_name" value="package-quote"', false)
            ->assertSee('Ready to plan your Self Drive tour?')
            ->getContent();
        $this->assertStringContainsString('data-interest="Self Drive tour"', $html);
    }

    public function test_package_filters_and_search_still_work_on_the_new_pages(): void
    {
        $this->get('/packages?destination=tyrol')->assertOk()->assertSee('journeys', false);   // old region links still load (all journeys)
        $this->get('/packages?place=innsbruck')->assertOk()->assertSee('Journeys to Innsbruck')
            ->assertSee('Tyrol Private Escorted')->assertDontSee('Vienna &amp; Salzburg Private Escorted', false);
        $this->get('/packages?q=Hallstatt')->assertOk()->assertSee('that include', false);
        $this->get('/packages?tour_type=coach')->assertOk()->assertSee('Coach tours');
    }

    public function test_contact_page_has_the_inline_form_and_shows_only_confirmed_details(): void
    {
        \App\Models\Setting::put('phone', '[CONFIRM: phone]');
        $this->get('/contact')->assertOk()->assertSee('name="form_name" value="contact"', false)->assertDontSee('CONFIRM');
    }

    public function test_place_facts_are_complete_and_photos_exist(): void
    {
        foreach (Places::all() as $pl) {
            $this->assertNotEmpty($pl['fact'], $pl['key']);
            $this->assertNotEmpty($pl['region'], $pl['key']);
            $this->assertEqualsWithDelta(47.5, $pl['lat'], 1.5, $pl['key'].' latitude is inside Austria');
            $this->assertEqualsWithDelta(13.0, $pl['lon'], 4.0, $pl['key'].' longitude is inside Austria');
            if ($pl['image']) {
                $this->assertFileExists(public_path('images/'.$pl['image'].'.webp'), $pl['key']);
            }
        }
    }

    public function test_every_known_place_is_matched_only_when_a_package_really_visits_it(): void
    {
        $p = Package::where('slug', 'imperial-austria-self-drive')->firstOrFail();
        $keys = array_column(Places::forPackage($p), 'key');
        $this->assertContains('vienna', $keys);
        $this->assertContains('salzburg', $keys);
        $this->assertNotContains('bregenz', $keys);
    }

    public function test_thank_you_and_404_use_the_new_design_and_stay_noindex(): void
    {
        $this->get('/thank-you')->assertOk()->assertSee('noindex', false)->assertSee('We plan your route');
        $this->get('/nope')->assertNotFound()->assertSee('class="hdr"', false)->assertSee('noindex', false);
    }

    public function test_photo_credits_list_the_winter_photos(): void
    {
        $this->get('/photo-credits')->assertOk()->assertSee('Wagrain in winter')->assertSee('AI-generated');
    }

    public function test_destinations_page_lists_each_place_with_its_journeys_and_no_regions(): void
    {
        $html = $this->get('/destinations')->assertOk()
            ->assertSee('Austria at a glance')
            ->assertSee('id="salzburg"', false)
            ->assertSee('/packages?place=innsbruck', false)
            ->assertSee('Historic Centre of the City of Salzburg', false)
            ->assertSee('class="plx-land"', false)
            ->assertDontSee('Tyrol &amp; the Alps', false)
            ->assertDontSee('Explore the region')
            ->assertDontSee('CONFIRM')
            ->getContent();

        // Each place is listed (and numbered) once.
        $this->assertSame(1, substr_count($html, '<h5>Salzburg</h5>'));
        $this->assertStringNotContainsString('openstreetmap.org', $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_destinations_menu_item_links_to_the_destinations_page(): void
    {
        $this->get('/')->assertOk()->assertSee('<a href="'.route('destinations.index').'" class="mtrig">Destinations', false);
        $this->get('/destinations')->assertOk()->assertSee('class="mtrig on">Destinations', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('destinations.index'), false);
    }

    public function test_austria_vacations_page_compares_every_tour_type_from_real_data(): void
    {
        $html = $this->get('/austria-vacations')->assertOk()
            ->assertSee('Compare at a glance')
            ->assertSee('Three ways', false)
            ->assertSee('Self Drive journeys')
            ->assertSee('href="'.route('tours.show', 'escorted').'"', false)
            ->assertSee('data-interest="Coach tour"', false)
            ->assertSee('per person')
            ->assertDontSee('CONFIRM')
            ->getContent();

        $cheapest = '$'.number_format(\App\Models\Package::published()->whereHas('tourType', fn ($q) => $q->where('slug', 'coach'))->min('price_from'));
        $this->assertStringContainsString($cheapest, $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_austria_vacations_menu_item_breadcrumbs_and_redirect(): void
    {
        $this->get('/')->assertOk()->assertSee('<a href="'.route('tours.index').'" class="mtrig">Austria Vacations', false);
        $this->get('/tours/self-drive')->assertOk()->assertSee('class="mtrig on">Austria Vacations', false)
            ->assertSee('<a href="'.route('tours.index').'">Austria Vacations</a>', false);
        $this->get('/tours')->assertRedirect('/austria-vacations');
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('tours.index'), false);
    }

    public function test_travel_styles_page_shows_every_style_with_its_matching_journeys(): void
    {
        $response = $this->get('/travel-styles')->assertOk()
            ->assertSee('Find your style')
            ->assertSee('Music &amp; <em>Opera</em>', false)
            ->assertSee('href="#alpine"', false)
            ->assertSee('id="wine"', false)
            ->assertSee('/packages?style=lakes', false)
            ->assertSee('data-interest="Private &amp; Personal"', false)
            ->assertDontSee('CONFIRM');

        // The counts on the page are the same matches the packages filter uses.
        $musical = Package::published()->get()->filter(fn ($p) => \App\Support\TravelStyles::matches($p, 'music'))->count();
        $response->assertSee('View '.$musical.' journeys', false);
        foreach (array_keys(config('goaustria.travel_styles')) as $slug) {
            $response->assertSee('id="'.$slug.'"', false);
        }
    }

    public function test_travel_style_menu_item_links_to_the_travel_styles_page(): void
    {
        $this->get('/')->assertOk()->assertSee('<a href="'.route('styles.index').'" class="mtrig">Travel Style', false);
        $this->get('/packages?style=music')->assertOk()->assertSee('class="mtrig on">Travel Style', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('styles.index'), false);
    }

    public function test_no_public_page_links_to_an_external_map(): void
    {
        // Visitors must stay on the site: no OpenStreetMap / Google Maps links or "View on map" buttons anywhere.
        \App\Models\Setting::put('address', "1 Example Street\nVienna");
        $slug = Package::published()->value('slug');
        foreach (['/', '/packages', "/packages/{$slug}", '/tours/self-drive', '/destinations', '/austria-vacations', '/travel-styles', '/contact', '/about', '/journal'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            foreach (['openstreetmap.org', 'google.com/maps', 'maps.google', 'View on map'] as $needle) {
                $this->assertStringNotContainsString($needle, $html, "{$url} contains {$needle}");
            }
        }
    }
}

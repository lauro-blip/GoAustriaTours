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
        foreach (['/packages', '/packages/imperial-austria-self-drive', '/tours/self-drive', '/journal', '/contact', '/about', '/testimonials', '/photo-credits', '/terms-and-conditions'] as $url) {
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
        $this->assertStringContainsString('/packages?destination=tyrol', $html);
        $this->assertStringContainsString('/tours/escorted', $html);
        $this->assertStringContainsString('/packages?style=', $html);
        $this->assertStringContainsString('Plan your journey', $html);
    }

    public function test_package_page_has_facts_verified_places_timeline_and_both_forms(): void
    {
        $html = $this->get('/packages/imperial-austria-self-drive')->assertOk()
            ->assertSee('Places on this journey')
            ->assertSee('Historic Centre of the City of Salzburg is a UNESCO World Heritage Site', false)
            ->assertSee('Day by day')
            ->assertSee('name="form_name" value="package-quote"', false)
            ->assertSee('name="form_name" value="ask-question"', false)
            ->getContent();

        $this->assertStringContainsString('openstreetmap.org', $html);
        $this->assertStringContainsString('data-package-id', $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_package_filters_and_search_still_work_on_the_new_pages(): void
    {
        $this->get('/packages?destination=tyrol')->assertOk()->assertSee('journeys', false);
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
}

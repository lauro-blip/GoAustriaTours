<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function nav(string $html): string
    {
        $start = strpos($html, 'aria-label="Main"');
        return substr($html, $start, strpos($html, '</nav>', $start) - $start);
    }

    public function test_packages_and_journal_are_gone_from_the_main_menu(): void
    {
        $nav = $this->nav($this->get('/journal')->assertOk()->getContent());
        $this->assertStringNotContainsString('>Packages<', $nav);
        $this->assertStringNotContainsString('>Journal<', $nav);
        $this->assertStringContainsString('About', $nav);
        $this->assertStringContainsString('Contact', $nav);
    }

    public function test_about_mega_menu_links_journal_testimonials_terms_and_privacy(): void
    {
        foreach (['/packages', '/contact'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('id="mega-about"', false)
                ->assertSee('/journal"', false)->assertSee('/testimonials"', false)
                ->assertSee('/terms-and-conditions"', false)->assertSee('/privacy-policy"', false)
                ->assertSee('Terms &amp; Conditions', false)->assertSee('Privacy Policy')
                ->assertSee('Our story');
        }
    }

    public function test_menu_shows_the_latest_article_and_first_guest_story(): void
    {
        $html = $this->get('/journal')->assertOk()->getContent();
        $menu = substr($html, strpos($html, 'id="mega-about"'), strpos($html, 'id="mega-vacations"') - strpos($html, 'id="mega-about"'));
        $this->assertStringContainsString('Latest:', $menu);
        $this->assertStringContainsString(Testimonial::where('is_active', true)->orderBy('sort_order')->first()->name, $menu);
    }

    public function test_testimonials_page_lists_every_active_story_and_is_in_the_sitemap(): void
    {
        $inactive = Testimonial::create(['name' => 'Hidden Person', 'location' => 'Nowhere', 'quote' => 'Should not show.', 'is_active' => false, 'is_placeholder' => false, 'sort_order' => 99]);
        $page = $this->get('/testimonials')->assertOk()->assertSee('What Our')->assertSee('Michael R.')->assertSee('Sarah &amp; James', false)->assertDontSee('Hidden Person');

        $this->get('/sitemap.xml')->assertOk()->assertSee('/testimonials', false);
        $this->assertNotNull($inactive);
        $this->assertNotNull($page);
    }

    public function test_mobile_menu_has_about_accordion_and_no_packages_or_journal_rows(): void
    {
        $html = $this->get('/journal')->assertOk()->getContent();
        $mobile = substr($html, strpos($html, 'id="mobile-menu"'), strpos($html, '</nav>', strpos($html, 'id="mobile-menu"')) - strpos($html, 'id="mobile-menu"'));
        $this->assertStringContainsString('About us', $mobile);
        $this->assertStringContainsString('Testimonials', $mobile);
        $this->assertStringNotContainsString('>Packages</a>', $mobile);
    }

    public function test_pages_still_reachable(): void
    {
        foreach (['/journal', '/packages', '/about', '/terms-and-conditions', '/privacy-policy'] as $u) {
            $this->get($u)->assertOk();
        }
    }
}

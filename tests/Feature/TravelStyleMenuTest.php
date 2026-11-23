<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Support\TravelStyles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelStyleMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function main(string $url): string
    {
        $html = $this->get($url)->assertOk()->getContent();

        return substr($html, (int) strpos($html, '<main'));
    }

    public function test_travel_style_mega_menu_is_on_every_page_with_all_six_styles(): void
    {
        foreach (['/packages', '/journal', '/contact'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('id="mega-style"', false)
                ->assertSee('Travel Style')
                ->assertSee('Culture &amp; Imperial Cities', false)->assertSee('Music &amp; Opera', false)->assertSee('Alpine Adventure')->assertSee('Lakes &amp; Villages', false)->assertSee('Wine &amp; Gastronomy', false)->assertSee('Private &amp; Personal', false)->assertSee('Discover')
                ->assertSee('/packages?style=wine', false);
        }
    }

    public function test_every_style_matches_at_least_one_package_so_no_menu_link_is_empty(): void
    {
        $packages = Package::published()->get();
        foreach (TravelStyles::withPackages($packages) as $st) {
            $this->assertGreaterThan(0, $st['count'], $st['slug']);
            $this->get('/packages?style='.$st['slug'])->assertOk()->assertDontSee('No packages match', false);
        }
    }

    public function test_style_filter_narrows_the_list_and_explains_itself(): void
    {
        $wine = $this->main('/packages?style=wine');

        $this->assertStringContainsString('Showing', $wine);
        $this->assertStringContainsString('Wine &amp; Gastronomy', $wine);
        $this->assertStringContainsString('Vienna and Wachau Wine Country Self Drive', $wine);
        $this->assertStringNotContainsString('Alpine Highlights by Coach', $wine);   // not a wine trip
    }

    public function test_unknown_style_is_ignored(): void
    {
        $this->assertStringNotContainsString('Showing', $this->main('/packages?style=bogus'));
        $this->get('/packages?style=<script>alert(1)</script>')->assertOk()->assertDontSee('<script>alert(1)', false);
    }

    public function test_style_combines_with_the_tour_type_filter(): void
    {
        $main = $this->main('/packages?style=lakes&tour_type=coach');
        $this->assertStringContainsString('Lakes and Mountains by Coach', $main);
        $this->assertStringNotContainsString('Alpine Lakes Self Drive', $main);
    }

    public function test_mobile_menu_and_filter_form_offer_travel_style(): void
    {
        $this->get('/journal')->assertOk()->assertSee('Travel Style');
        $this->get('/packages')->assertOk()->assertSee('All travel styles');
    }
}

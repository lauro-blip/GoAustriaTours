<?php

namespace Tests\Feature;

use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationsMenuTest extends TestCase
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

    public function test_mega_menu_lists_destinations_places_and_links_on_every_page(): void
    {
        foreach (['/packages', '/journal', '/contact'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('id="mega-destinations"', false)
                ->assertSee('Vienna &amp; Salzburg', false)
                ->assertSee('Tyrol', false)
                ->assertSee('Hallstatt and the Salzkammergut')
                ->assertSee('Bregenz and Lake Constance')
                ->assertSee('/packages?destination=tyrol', false)
                ->assertSee('/packages?q=Hallstatt', false);
        }
    }

    public function test_mobile_menu_has_a_destinations_section(): void
    {
        $this->get('/journal')->assertOk()->assertSee('Destinations');
    }

    public function test_menu_is_omitted_when_there_are_no_active_destinations(): void
    {
        Destination::query()->update(['is_active' => false]);
        $this->get('/journal')->assertOk()->assertDontSee('mega-destinations');
    }

    public function test_every_menu_place_link_shows_at_least_one_package(): void
    {
        foreach (config('goaustria.nav_places') as $slug => $places) {
            foreach ($places as [$label, $term]) {
                $this->get('/packages?q='.urlencode($term))->assertOk()->assertDontSee('No packages match', false);
            }
        }
    }

    public function test_search_filters_packages_and_explains_itself(): void
    {
        $main = $this->main('/packages?q=Bregenz');
        $this->assertStringContainsString('Tyrol and Vorarlberg by Coach', $main);
        $this->assertStringNotContainsString('Imperial Austria Self Drive', $main);   // (the mega menu outside <main> may list it)
        $this->assertStringContainsString('Showing trips that include', $main);
    }

    public function test_search_is_safe_for_odd_input(): void
    {
        $this->get('/packages?q='.urlencode('%_\\'))->assertOk();
        $this->get('/packages?q='.urlencode('<script>alert(1)</script>'))->assertOk()->assertDontSee('<script>alert(1)', false);
        $this->get('/packages?q=nothing-matches-this')->assertOk()->assertSee('No packages match');
    }

    public function test_search_combines_with_the_destination_filter(): void
    {
        $this->assertStringNotContainsString('Imperial Austria Self Drive', $this->main('/packages?destination=tyrol&q=Salzburg'));
    }
}

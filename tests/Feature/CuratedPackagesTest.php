<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CuratedPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        // The sample data has 6 packages (one page). Add 3 more so there is a page 2.
        foreach (['Zeta Danube Cruise Escape', 'Zeta Salzkammergut Lakes Retreat', 'Zeta Tyrol Family Adventure'] as $i => $title) {
            $copy = Package::published()->first()->replicate();
            $copy->title = $title;
            $copy->slug = str($title)->slug()->toString();
            $copy->sort_order = 100 + $i;
            $copy->is_featured = false;
            $copy->save();
        }
    }

    public function test_home_shows_six_curated_packages_with_pagination(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Next page')
            ->assertSee('$2,490')
            ->assertDontSee('Zeta Danube Cruise Escape');   // 7th package is on page 2
    }

    public function test_ajax_endpoint_returns_page_two_fragment(): void
    {
        $this->get('/packages/curated?curated_page=2', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Zeta Danube Cruise Escape')
            ->assertDontSee('Imperial Austria Self Drive')
            ->assertSee('Previous page')
            ->assertDontSee('<html', false);
    }

    public function test_no_js_fallback_link_renders_page_two_on_home(): void
    {
        $this->get('/?curated_page=2')->assertOk()->assertSee('Zeta Danube Cruise Escape');
    }

    public function test_featured_carousel_follows_the_intro_and_curated_grid_comes_later(): void
    {
        $html = $this->get('/')->assertOk()->assertSee('Featured packages')->assertSee('Curated packages')->getContent();

        $this->assertLessThan(strpos($html, 'id="featured"'), strpos($html, 'id="journey-title"'));
        $this->assertLessThan(strpos($html, 'id="curated"'), strpos($html, 'id="featured"'));
    }
}

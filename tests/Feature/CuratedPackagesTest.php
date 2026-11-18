<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The sample data has 15 published packages: page 1 = 6, page 2 = 6, page 3 = 3. */
class CuratedPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_there_are_fifteen_sample_packages_and_only_the_original_six_are_featured(): void
    {
        $this->assertSame(15, Package::where('is_published', true)->count());
        $this->assertSame(6, Package::where('is_featured', true)->count());
    }

    public function test_every_sample_package_has_a_card_image_file(): void
    {
        foreach (Package::pluck('slug') as $slug) {
            $this->assertFileExists(public_path("images/packages/{$slug}.webp"), $slug);
        }
    }

    public function test_home_shows_six_curated_packages_with_pagination(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Next page')
            ->assertSee('$2,490')
            ->assertDontSee('Grossglockner and High Tauern Self Drive');   // 7th package is on page 2
    }

    public function test_ajax_endpoint_returns_page_two_fragment(): void
    {
        $this->get('/packages/curated?curated_page=2', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Grossglockner and High Tauern Self Drive')
            ->assertSee('Musical Austria Private Escorted')
            ->assertDontSee('Imperial Austria Self Drive')
            ->assertSee('Previous page')
            ->assertSee('Next page')
            ->assertDontSee('<html', false);
    }

    public function test_page_three_has_the_last_three(): void
    {
        $this->get('/packages/curated?curated_page=3', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Tyrol and Vorarlberg by Coach')
            ->assertSee('Danube Valley by Coach')
            ->assertDontSee('Next page');
    }

    public function test_no_js_fallback_link_renders_page_two_on_home(): void
    {
        $this->get('/?curated_page=2')->assertOk()->assertSee('Grossglockner and High Tauern Self Drive');
    }

    public function test_featured_carousel_follows_the_intro_and_curated_grid_comes_later(): void
    {
        $html = $this->get('/')->assertOk()->assertSee('Featured packages')->assertSee('Curated packages')->getContent();

        $this->assertLessThan(strpos($html, 'id="curated"'), strpos($html, 'id="featured"'));
        $this->assertLessThan(strpos($html, 'id="featured"'), strpos($html, 'id="journey-title"'));
    }

    public function test_new_package_pages_render(): void
    {
        foreach (['musical-austria-private-escorted', 'danube-valley-by-coach', 'tyrol-and-vorarlberg-by-coach'] as $slug) {
            $this->get("/packages/{$slug}")->assertOk()->assertSee('Days 1-');
        }
    }
}

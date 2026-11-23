<?php

namespace Tests\Feature;

use App\Models\TourType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourTypesMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_tour_types_mega_menu_lists_each_type_with_summary_packages_and_links(): void
    {
        foreach (['/packages', '/journal'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('id="mega-tours"', false)
                ->assertSee('/tours/self-drive', false)
                ->assertSee('/tours/escorted', false)
                ->assertSee('/tours/coach', false)
                ->assertSee('/packages?tour_type=coach', false)
                ->assertSee('Imperial Austria Self Drive')          // first package of the Self Drive column
                ->assertSee('/packages/imperial-austria-self-drive', false);
        }
    }

    public function test_each_column_shows_at_most_three_packages(): void
    {
        $html = $this->get('/journal')->assertOk()->getContent();
        $mega = substr($html, strpos($html, 'id="mega-tours"'), strpos($html, 'id="mega-style"') - strpos($html, 'id="mega-tours"'));

        $this->assertSame(9, substr_count($mega, '/packages/'), 'three tour types x three packages');
    }

    public function test_menu_is_omitted_without_active_tour_types(): void
    {
        TourType::query()->update(['is_active' => false]);
        $this->get('/journal')->assertOk()->assertDontSee('mega-tours');
    }

    public function test_both_mega_menus_use_one_state_so_only_one_can_be_open(): void
    {
        $html = $this->get('/journal')->assertOk()->getContent();
        $this->assertStringContainsString("mega: ''", $html);
        $this->assertStringContainsString("x-show=\"mega === 'tours'\"", $html);
        $this->assertStringContainsString("x-show=\"mega === 'destinations'\"", $html);
        $this->assertStringNotContainsString('@mouseenter="o = true"', $html);
    }
}

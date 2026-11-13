<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_shows_first_three_packages_with_pagination(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Imperial Austria Self Drive')
            ->assertSee('Vienna &amp; Salzburg Private Escorted', false)
            ->assertSee('Next page')
            ->assertSee('$2,490');
    }

    public function test_ajax_endpoint_returns_page_two_fragment(): void
    {
        $this->get('/packages/featured?featured_page=2', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Classic Austria by Coach')
            ->assertDontSee('Imperial Austria Self Drive')
            ->assertSee('Previous page')
            ->assertDontSee('<html', false);
    }

    public function test_no_js_fallback_link_renders_page_two_on_home(): void
    {
        $this->get('/?featured_page=2')->assertOk()->assertSee('Alpine Highlights by Coach');
    }
}

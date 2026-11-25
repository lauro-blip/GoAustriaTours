<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_renders_the_hero_quiz_quote_popup_and_is_indexable(): void
    {
        $html = $this->get('/')->assertOk()
            ->assertSee('<span class="hl">Austria', false)->assertSee('Trip,', false)
            ->assertSee('name="form_name" value="hero-quiz"', false)
            ->assertSee('data-qm', false)
            ->assertSee('data-quote', false)
            ->assertSee('Guest', false)
            ->getContent();

        $this->assertStringNotContainsString('noindex', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.route('home').'">', $html);
    }

    public function test_home_has_no_leftover_design_study_references(): void
    {
        $this->get('/')->assertOk()->assertDontSee('test-page', false)->assertDontSee('Design study');
        $this->get('/test-page-3')->assertNotFound();
        $this->get('/test-page-2')->assertNotFound();
        $this->get('/test-page')->assertNotFound();
    }

    public function test_winter_images_referenced_by_home_exist(): void
    {
        foreach (['hero-wagrain', 'hero-salzburg', 'hero-montafon', 'hallstatt', 'zellamsee', 'way-self-drive', 'way-escorted', 'way-coach'] as $n) {
            $this->assertFileExists(public_path("images/winter/{$n}.webp"), $n);
            $this->assertFileExists(public_path("images/winter/{$n}-768.webp"), $n);
        }
    }
}

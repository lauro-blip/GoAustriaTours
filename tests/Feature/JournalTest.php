<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_journal_index_lists_published_posts(): void
    {
        $this->get('/journal')->assertOk()
            ->assertSee('Self Drive or Coach? How to Choose Your Austria Tour')
            ->assertSee('Driving the Grossglockner High Alpine Road');
    }

    public function test_post_page_renders_with_article_schema(): void
    {
        $this->get('/journal/when-to-visit-austria-season-by-season')->assertOk()
            ->assertSee('When to Visit Austria')
            ->assertSee('"@type":"Article"', false)
            ->assertSee('min read');
    }

    public function test_unpublished_and_future_posts_are_404_and_hidden(): void
    {
        Post::where('slug', 'when-to-visit-austria-season-by-season')->update(['is_published' => false]);
        Post::where('slug', 'three-days-in-vienna-first-timers-guide')->update(['published_at' => now()->addDay()]);

        $this->get('/journal/when-to-visit-austria-season-by-season')->assertNotFound();
        $this->get('/journal/three-days-in-vienna-first-timers-guide')->assertNotFound();
        $this->get('/journal')->assertOk()->assertDontSee('When to Visit Austria')->assertDontSee('Three Days in Vienna');
    }

    public function test_home_shows_journal_and_package_carousel_sections(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Planning Your Next Austrian Escape?')
            ->assertSee('Austria travel stories and tips')
            ->assertSee('/journal/self-drive-or-coach-how-to-choose', false)
            ->assertSee('Alpine Highlights by Coach');
    }

    public function test_sitemap_includes_journal(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();
        $this->assertStringContainsString('/journal', $xml);
        $this->assertStringContainsString('/journal/driving-the-grossglockner-high-alpine-road', $xml);
    }
}

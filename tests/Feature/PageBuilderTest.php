<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin > Pages: pages built from section blocks (App\Support\Blocks), drafts, menus and safety of what is saved. */
class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    private function blocks(): string
    {
        return json_encode([
            ['type' => 'text', 'data' => ['eyebrow' => 'Plan ahead', 'heading' => 'Winter in Austria', 'body' => '<p>Snowy <strong>villages</strong><script>alert(1)</script> <a href="javascript:alert(1)">bad</a> <a href="/destinations">good</a></p>', 'align' => 'center', 'tone' => 'light']],
            ['type' => 'image_text', 'data' => ['image' => ['ref' => 'site:winter/hallstatt', 'alt' => 'Hallstatt in winter'], 'side' => 'right', 'heading' => 'Lakeside calm', 'button_label' => 'Ask us', 'button_link' => 'javascript:alert(1)', 'tone' => 'tint']],
            ['type' => 'faq', 'data' => ['heading' => 'Questions', 'items' => [['question' => 'Is it cold?', 'answer' => 'Yes, bring a coat.'], ['question' => '', 'answer' => '']]]],
            ['type' => 'journeys', 'data' => ['heading' => 'Tyrol journeys', 'filter' => 'place:innsbruck', 'limit' => 3]],
            ['type' => 'quote_form', 'data' => ['heading' => 'Ask for a quote']],
            ['type' => 'evil', 'data' => ['x' => 1]],
        ]);
    }

    public function test_admin_can_create_a_draft_page_that_only_admins_can_see_then_publish_it(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/admin/pages/create')->assertOk()->assertSee('Add a section');

        $this->actingAs($this->admin, 'admin')->post('/admin/pages', [
            'title' => 'Winter in Austria', 'slug' => '', 'eyebrow' => 'Seasonal guide', 'intro' => 'Snow, markets and quiet lakes.',
            'header_image' => 'site:winter/hallstatt', 'blocks_json' => $this->blocks(), 'nav_location' => 'footer',
        ])->assertRedirect();

        $page = Page::where('slug', 'winter-in-austria')->firstOrFail();
        $this->assertFalse($page->is_published);
        $this->assertFalse($page->is_system);
        $this->assertCount(5, $page->blocks, 'unknown block types are dropped');

        // Draft: hidden from visitors, previewable by an admin, never in the sitemap or footer.
        auth('admin')->logout();
        $this->get('/winter-in-austria')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('winter-in-austria');
        $this->actingAs($this->admin, 'admin')->get('/winter-in-austria')->assertOk()->assertSee('Draft: only signed-in admins');

        $this->actingAs($this->admin, 'admin')->put("/admin/pages/{$page->id}", [
            'title' => 'Winter in Austria', 'slug' => 'winter-in-austria', 'eyebrow' => 'Seasonal guide', 'is_published' => '1', 'nav_location' => 'footer',
            'header_image' => 'site:winter/hallstatt', 'blocks_json' => $this->blocks(),
        ])->assertRedirect("/admin/pages/{$page->id}/edit");
        auth('admin')->logout();

        $html = $this->get('/winter-in-austria')->assertOk()
            ->assertSee('Winter in Austria')->assertSee('Seasonal guide')
            ->assertSee('<strong>villages</strong>', false)
            ->assertSee('<a href="/destinations">good</a>', false)
            ->assertDontSee('alert(1)', false)
            ->assertSee('Hallstatt in winter')
            ->assertSee('name="form_name" value="contact"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertDontSee('Draft: only')
            ->getContent();
        $this->assertSame(1, substr_count($html, '<summary>'), 'empty FAQ rows are dropped');
        $this->assertStringContainsString('/packages/', $html, 'journeys block lists Tyrol packages');

        $this->get('/sitemap.xml')->assertSee(route('pages.show', 'winter-in-austria'), false);
    }

    public function test_published_pages_appear_in_the_chosen_menu_and_drafts_do_not(): void
    {
        Page::create(['slug' => 'winter-guide', 'title' => 'Winter guide', 'is_system' => false, 'is_published' => true, 'nav_location' => 'footer', 'blocks' => []]);
        Page::create(['slug' => 'our-hotels', 'title' => 'Our hotels', 'intro' => 'Where you stay', 'is_system' => false, 'is_published' => true, 'nav_location' => 'about', 'blocks' => []]);
        Page::create(['slug' => 'secret-offer', 'title' => 'Secret offer', 'is_system' => false, 'is_published' => false, 'nav_location' => 'footer', 'blocks' => []]);

        $this->get('/')->assertOk()
            ->assertSee('href="'.route('pages.show', 'winter-guide').'"', false)
            ->assertSee('<b>Our hotels</b>', false)
            ->assertDontSee('Secret offer');
    }

    public function test_a_page_cannot_take_an_address_the_site_already_uses(): void
    {
        foreach (['packages', 'admin', 'destinations', 'about'] as $slug) {
            $this->actingAs($this->admin, 'admin')->post('/admin/pages', ['title' => 'X', 'slug' => $slug, 'blocks_json' => '[]'])->assertSessionHasErrors('slug');
        }
        $this->actingAs($this->admin, 'admin')->post('/admin/pages', ['title' => 'X', 'slug' => 'Bad Slug!', 'blocks_json' => '[]'])->assertSessionHasErrors('slug');
        $this->assertSame(0, Page::custom()->count());
    }

    public function test_photos_must_be_real_media_or_bundled_site_photos(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/pages', [
            'title' => 'Photos', 'is_published' => '1', 'header_image' => 'site:../../.env',
            'blocks_json' => json_encode([['type' => 'gallery', 'data' => ['items' => [
                ['image' => ['ref' => 'site:winter/hallstatt', 'alt' => 'ok'], 'caption' => 'Fine'],
                ['image' => ['ref' => 'media:999', 'alt' => 'missing'], 'caption' => ''],
                ['image' => ['ref' => 'http://evil.example/x.png', 'alt' => 'x'], 'caption' => ''],
            ]]]]),
        ]);
        $page = Page::where('slug', 'photos')->firstOrFail();
        $this->assertNull($page->header_image);
        $this->assertCount(1, $page->blocks[0]['data']['items']);
        $this->get('/photos')->assertOk()->assertSee('images/winter/hallstatt-1280.webp', false)->assertDontSee('evil.example');
    }

    public function test_built_in_pages_cannot_be_deleted_and_about_accepts_extra_sections(): void
    {
        $about = Page::slug('about');
        $this->actingAs($this->admin, 'admin')->delete("/admin/pages/{$about->id}")->assertForbidden();
        $this->actingAs($this->admin, 'admin')->get("/admin/pages/{$about->id}/edit")->assertOk()->assertSee('Extra sections shown below the main text.');

        $this->actingAs($this->admin, 'admin')->put("/admin/pages/{$about->id}", [
            'title' => $about->title, 'body' => '<p>Our story</p>',
            'blocks_json' => json_encode([['type' => 'cta', 'data' => ['heading' => 'Talk to a specialist', 'text' => 'Free quote.']]]),
        ])->assertRedirect();
        $this->get('/about')->assertOk()->assertSee('Our story')->assertSee('Talk to a specialist');
        $this->assertSame('about', $about->fresh()->slug);
        $this->get('/about')->assertOk();
        $this->get('/terms')->assertNotFound();
    }

    public function test_pages_can_be_duplicated_and_deleted(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/pages', ['title' => 'Offer', 'is_published' => '1', 'blocks_json' => '[]']);
        $page = Page::where('slug', 'offer')->firstOrFail();
        $this->actingAs($this->admin, 'admin')->post("/admin/pages/{$page->id}/duplicate")->assertRedirect();
        $copy = Page::where('slug', 'offer-copy')->firstOrFail();
        $this->assertFalse($copy->is_published);

        $this->actingAs($this->admin, 'admin')->delete("/admin/pages/{$page->id}")->assertRedirect('/admin/pages');
        $this->assertNull(Page::find($page->id));
        $this->get('/offer')->assertNotFound();
    }

    public function test_pages_admin_requires_login(): void
    {
        $this->get('/admin/pages/create')->assertRedirect('/admin/login');
        $this->post('/admin/pages', ['title' => 'X'])->assertRedirect('/admin/login');
    }
}

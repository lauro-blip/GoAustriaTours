<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Page;
use App\Support\HomeSections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Visual editor (round 1): live preview of unsaved changes, inline-edit markers only in the preview, version history. */
class VisualEditorTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = AdminUser::create(['name' => 'Ana', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    public function test_preview_renders_unsaved_changes_with_editing_markers_and_saves_nothing(): void
    {
        $page = Page::create(['slug' => 'guide', 'title' => 'Guide', 'is_system' => false, 'is_published' => true, 'blocks' => [['type' => 'text', 'data' => ['heading' => 'Saved heading'], 'hidden' => false]]]);
        $html = $this->actingAs($this->admin, 'admin')->post("/admin/pages/{$page->id}/preview", [
            'title' => 'Unsaved title',
            'blocks_json' => json_encode([['type' => 'text', 'data' => ['heading' => 'Typed but not saved', 'body' => '<p>x</p>']], ['type' => 'home_hero', 'data' => []]]),
        ])->assertOk()->getContent();

        $this->assertStringContainsString('Unsaved title', $html);
        $this->assertStringContainsString('Typed but not saved', $html);
        $this->assertStringContainsString('data-blk="0"', $html);
        $this->assertStringContainsString('data-edit="heading"', $html);
        $this->assertStringContainsString('id="pv-tag"', $html);
        $this->assertStringNotContainsString('class="hero"', $html, 'home sections are not allowed on other pages, even in preview');
        $this->assertSame('Saved heading', $page->fresh()->blocks[0]['data']['heading']);

        // The public page never carries editor markup.
        auth('admin')->logout();
        $public = $this->get('/guide')->assertOk()->getContent();
        foreach (['data-blk', 'data-edit', 'pv-tag'] as $needle) {
            $this->assertStringNotContainsString($needle, $public);
        }
    }

    public function test_home_preview_and_new_page_preview(): void
    {
        $home = Page::slug('home');
        $blocks = HomeSections::defaults();
        $blocks[0]['data']['h1_italic'] = 'Live preview line';
        $this->actingAs($this->admin, 'admin')->post("/admin/pages/{$home->id}/preview", ['title' => 'Home', 'blocks_json' => json_encode($blocks)])
            ->assertOk()->assertSee('Live preview line')->assertSee('data-edit="h1_italic"', false);
        $this->actingAs($this->admin, 'admin')->post('/admin/pages/preview', ['title' => 'Brand new', 'blocks_json' => '[]'])->assertOk()->assertSee('Brand new');
        $this->get('/')->assertDontSee('data-edit', false);
    }

    public function test_preview_requires_admin(): void
    {
        $this->post('/admin/pages/preview', ['title' => 'x'])->assertRedirect('/admin/login');
    }

    public function test_saving_keeps_the_previous_version_and_restore_brings_it_back(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/pages', ['title' => 'Offer', 'is_published' => '1', 'blocks_json' => json_encode([['type' => 'text', 'data' => ['heading' => 'Version one']]])]);
        $page = Page::where('slug', 'offer')->firstOrFail();
        $put = fn (string $h) => $this->actingAs($this->admin, 'admin')->put("/admin/pages/{$page->id}", ['title' => 'Offer', 'slug' => 'offer', 'is_published' => '1', 'blocks_json' => json_encode([['type' => 'text', 'data' => ['heading' => $h]]])]);

        $put('Version two');
        $put('Version two'); // no change: no new revision
        $this->assertSame(1, $page->revisions()->count());
        $rev = $page->revisions()->first();
        $this->assertSame('Version one', $rev->content['blocks'][0]['data']['heading']);
        $this->assertSame('Ana', $rev->author);

        $this->actingAs($this->admin, 'admin')->get("/admin/pages/{$page->id}/edit")->assertOk()->assertSee('History')->assertSee('Restore');
        $this->actingAs($this->admin, 'admin')->post("/admin/pages/{$page->id}/revisions/{$rev->id}/restore")->assertRedirect("/admin/pages/{$page->id}/edit");
        $this->assertSame('Version one', $page->fresh()->blocks[0]['data']['heading']);
        $this->assertSame(2, $page->revisions()->count(), 'the replaced version is kept too');
    }

    public function test_a_revision_of_another_page_cannot_be_restored(): void
    {
        $a = Page::create(['slug' => 'a-page', 'title' => 'A', 'is_system' => false, 'blocks' => []]);
        $b = Page::create(['slug' => 'b-page', 'title' => 'B', 'is_system' => false, 'blocks' => []]);
        $rev = $a->revisions()->create(['content' => ['title' => 'Hacked']]);
        $this->actingAs($this->admin, 'admin')->post("/admin/pages/{$b->id}/revisions/{$rev->id}/restore")->assertNotFound();
    }
}

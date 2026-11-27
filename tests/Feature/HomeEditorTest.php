<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Page;
use App\Support\Blocks;
use App\Support\HomeSections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin > Pages > Home: built-in sections are editable, movable and hideable; the hero stays first; blocks can be inserted. */
class HomeEditorTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    private function save(array $blocks): void
    {
        $home = Page::slug('home');
        $this->actingAs($this->admin, 'admin')->put("/admin/pages/{$home->id}", [
            'title' => $home->title, 'blocks_json' => json_encode($blocks),
        ])->assertRedirect("/admin/pages/{$home->id}/edit");
        auth('admin')->logout();
    }

    public function test_with_nothing_saved_the_home_page_shows_the_original_sections(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<span class="hl">Austria', false)
            ->assertSee('Planned for You')
            ->assertSee('Custom Itineraries')
            ->assertSee('Why travel with GoAustriaTours')
            ->assertSee('Our Top-Rated')
            ->assertDontSee('Moments Worth'); // hidden by default (HomeSections::HIDDEN)
        $this->assertCount(count(HomeSections::ORDER), HomeSections::defaults());
    }

    public function test_admin_edits_hides_moves_and_adds_sections_on_the_home_page(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/admin/pages/'.Page::slug('home')->id.'/edit')->assertOk()->assertSee('The home page, top to bottom.', false);
        auth('admin')->logout();

        $blocks = HomeSections::defaults();
        $blocks[0]['data']['h1_highlight'] = 'Tyrol';
        $blocks[0]['data']['h1_italic'] = 'Made for You';
        $blocks[0]['data']['sub'] = 'Tell us about {brand} trips.';
        $band = array_search('home_band', array_column($blocks, 'type'), true);
        $blocks[$band]['hidden'] = true;
        $why = array_search('home_why', array_column($blocks, 'type'), true);
        $blocks[$why]['data']['heading_em'] = 'crafted for you';
        // Insert a custom text block right after "why".
        array_splice($blocks, $why + 1, 0, [['type' => 'text', 'data' => ['heading' => 'A note from our planners', 'body' => '<p>Hello</p>'], 'hidden' => false]]);
        // Try to move the hero down and drop the journal section completely.
        $hero = array_shift($blocks);
        $blocks[] = $hero;
        $blocks = array_values(array_filter($blocks, fn ($b) => $b['type'] !== 'home_journal'));
        $this->save($blocks);

        $saved = Page::slug('home')->blocks;
        $this->assertSame('home_hero', $saved[0]['type'], 'the hero is always first');
        $this->assertContains('home_journal', array_column($saved, 'type'), 'built-in sections cannot be removed');

        $html = $this->get('/')->assertOk()
            ->assertSee('Tyrol<svg', false)->assertSee('Made for You')
            ->assertSee('Tell us about GoAustriaTours trips.')
            ->assertDontSee('Custom Itineraries')
            ->assertSee('crafted for you')
            ->assertSee('A note from our planners')
            ->getContent();
        $this->assertLessThan(strpos($html, 'Our Top-Rated'), strpos($html, 'A note from our planners'));
        $this->assertGreaterThan(strpos($html, 'crafted for you'), strpos($html, 'A note from our planners'));
    }

    public function test_home_sections_cannot_be_added_to_other_pages(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/pages', [
            'title' => 'Sneaky', 'is_published' => '1',
            'blocks_json' => json_encode([Blocks::blank('home_hero'), ['type' => 'text', 'data' => ['heading' => 'Fine']]]),
        ]);
        $this->assertSame(['text'], array_column(Page::where('slug', 'sneaky')->first()->blocks, 'type'));
    }

    public function test_hidden_blocks_are_not_shown_on_any_page(): void
    {
        Page::create(['slug' => 'hidden-test', 'title' => 'Hidden test', 'is_system' => false, 'is_published' => true, 'blocks' => [
            ['type' => 'text', 'data' => ['heading' => 'Visible block'], 'hidden' => false],
            ['type' => 'text', 'data' => ['heading' => 'Secret block'], 'hidden' => true],
        ]]);
        $this->get('/hidden-test')->assertOk()->assertSee('Visible block')->assertDontSee('Secret block');
    }
}

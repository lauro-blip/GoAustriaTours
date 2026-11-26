<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Page;
use App\Support\Blocks;
use App\Support\HomeSections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Visual editor round 2: section style options (fixed classes) and the Columns section with its elements. */
class SectionStyleColumnsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function page(array $blocks): string
    {
        Page::create(['slug' => 'styled', 'title' => 'Styled', 'is_system' => false, 'is_published' => true, 'blocks' => Blocks::sanitize($blocks)]);

        return $this->get('/styled')->assertOk()->getContent();
    }

    public function test_style_options_become_fixed_classes_and_a_photo_background(): void
    {
        $html = $this->page([
            ['type' => 'text', 'data' => ['heading' => 'Styled text', 'tone' => 'photo', 'bg_image' => ['ref' => 'site:winter/hallstatt', 'alt' => ''], 'spacing' => 'l', 'width' => 'wide', 'head_align' => 'left', 'show_on' => 'mobile']],
            ['type' => 'text', 'data' => ['heading' => 'Plain text']],
            ['type' => 'text', 'data' => ['heading' => 'Bad values', 'tone' => 'neon', 'spacing' => 'xxl', 'show_on' => 'tv']],
        ]);
        $this->assertStringContainsString('class="sec blk blk-text blk-dark blk-photo blk-sp-l blk-w-w blk-hl blk-hide-d" style="--blk-bg:url(&quot;', $html);
        $this->assertStringContainsString('images/winter/hallstatt-1280.webp', $html);
        $this->assertStringContainsString('<section class="sec blk blk-text ">', $html, 'defaults add no classes');
        $this->assertSame(2, substr_count($html, '<section class="sec blk blk-text ">'), 'invalid choices fall back to the defaults');
    }

    public function test_columns_render_their_elements_and_videos_load_only_on_click(): void
    {
        $html = $this->page([['type' => 'columns', 'data' => ['heading' => 'Two columns', 'layout' => '2l', 'valign' => 'center', 'cols' => [
            ['elements' => [
                ['type' => 'heading', 'data' => ['text' => 'Column heading', 'size' => 'l']],
                ['type' => 'text', 'data' => ['body' => '<p>Rich <strong>text</strong><script>x()</script></p>']],
                ['type' => 'list', 'data' => ['items' => "One\nTwo\n\nThree", 'icon' => 'number']],
                ['type' => 'button', 'data' => ['label' => 'Ask us', 'link' => '', 'style' => 'outline']],
                ['type' => 'evil', 'data' => []],
            ]],
            ['elements' => [
                ['type' => 'image', 'data' => ['image' => ['ref' => 'site:winter/kitzbuhel', 'alt' => 'Kitzbuhel'], 'ratio' => 'square', 'caption' => 'Old town']],
                ['type' => 'quote', 'data' => ['text' => 'Wonderful trip', 'author' => 'Guest']],
                ['type' => 'video', 'data' => ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Our trip']],
                ['type' => 'video', 'data' => ['url' => 'https://evil.example/video']],
                ['type' => 'spacer', 'data' => ['size' => 'l']],
            ]],
        ]]]]);

        $this->assertStringContainsString('class="blk-cols lay-2l va-c"', $html);
        $this->assertStringContainsString('<h3 class="el-h el-h-l">Column heading</h3>', $html, 'under the section heading (h2) element headings start at h3');
        $this->assertStringContainsString('<strong>text</strong>', $html);
        $this->assertStringNotContainsString('x()', $html);
        $this->assertStringContainsString('<ol class="el-list el-list-number"><li>One</li><li>Two</li><li>Three</li></ol>', $html);
        $this->assertStringContainsString('class="btn el-btn-o" href="'.route('contact').'" data-quote', $html);
        $this->assertStringContainsString('class="el-img r-square"', $html);
        $this->assertStringContainsString('data-video="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&amp;rel=0"', $html);
        $this->assertStringNotContainsString('<iframe', $html, 'no player is loaded until the visitor presses play');
        $this->assertSame(1, substr_count($html, 'data-video='), 'unknown video hosts are ignored');
        $this->assertStringContainsString('el-sp-l', $html);
    }

    public function test_video_links_are_recognised(): void
    {
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0', Blocks::videoEmbed('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertSame('https://player.vimeo.com/video/76979871?autoplay=1&dnt=1', Blocks::videoEmbed('https://vimeo.com/76979871'));
        $this->assertNull(Blocks::videoEmbed('https://example.com/x'));
    }

    public function test_home_loads_inner_css_only_when_it_has_inserted_sections(): void
    {
        $this->get('/')->assertOk()->assertDontSee('/build/assets/inner-', false);

        $blocks = HomeSections::defaults();
        array_splice($blocks, 2, 0, [Blocks::blank('cta')]);
        $blocks[2]['data']['heading'] = 'Inserted on the home page';
        Page::slug('home')->update(['blocks' => $blocks]);
        $this->get('/')->assertOk()->assertSee('Inserted on the home page')->assertSee('/build/assets/inner-', false);
    }

    public function test_the_editor_offers_the_columns_section_and_style_options(): void
    {
        $admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
        $this->actingAs($admin, 'admin')->get('/admin/pages/create')->assertOk()
            ->assertSee('Columns')->assertSee('Style')->assertSee('layoutCols', false);
    }
}

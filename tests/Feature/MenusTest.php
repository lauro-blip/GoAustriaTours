<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Setting;
use App\Support\Menus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin > Menus: header items + button, footer columns, texts and links; defaults match the original site. */
class MenusTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    private function save(array $header, array $footer): void
    {
        $this->actingAs($this->admin, 'admin')->put('/admin/menus', ['header_json' => json_encode($header), 'footer_json' => json_encode($footer)])
            ->assertRedirect('/admin/menus');
        auth('admin')->logout();
    }

    public function test_defaults_render_the_original_menus_and_footer(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/admin/menus')->assertOk()->assertSee('Header menu')->assertSee('Bottom bar links');
        auth('admin')->logout();
        $this->get('/')->assertOk()
            ->assertSee('class="mtrig">Destinations', false)->assertSee('>Contact us</a>', false)
            ->assertSee('Plan your journey')
            ->assertSee('Ready to Plan Your Perfect Austria Journey?')
            ->assertSee('Thoughtfully crafted Austria journeys')
            ->assertSee('© '.date('Y').' GoAustriaTours. All rights reserved.', false);
    }

    public function test_admin_renames_reorders_hides_and_adds_menu_items_and_footer_content(): void
    {
        $h = Menus::defaultHeader();
        $h['items'][1]['label'] = 'Where to go';                      // destinations
        $h['items'][3]['hidden'] = true;                              // travel style
        array_unshift($h['items'], ['key' => 'link', 'label' => 'Winter offers', 'url' => '/winter', 'hidden' => false]);
        $h['items'][] = ['key' => 'link', 'label' => 'Evil', 'url' => 'javascript:alert(1)', 'hidden' => false];
        $h['cta_label'] = 'Get a quote';
        $h['cta_url'] = '/contact';

        $f = Menus::defaultFooter();
        $f['cta_title'] = 'Talk to {brand} today';
        $f['columns'][] = ['key' => 'links', 'title' => 'Good to know', 'hidden' => false, 'links' => [['label' => 'Visas', 'url' => 'https://example.com/visa'], ['label' => '', 'url' => '/x']]];
        $f['columns'][2]['hidden'] = true;                            // travel styles column
        $f['bottom_links'] = [['label' => 'Privacy', 'url' => '/privacy-policy'], ['label' => 'Bad', 'url' => 'javascript:x']];
        $f['copyright'] = '{brand} {year}';
        $this->save($h, $f);

        $html = $this->get('/')->assertOk()
            ->assertSee('>Where to go <i class="caret"></i>', false)
            ->assertSee('>Winter offers</a>', false)
            ->assertDontSee('javascript:alert', false)
            ->assertSee('Get a quote')
            ->assertSee('Talk to GoAustriaTours today')
            ->assertSee('Good to know')->assertSee('https://example.com/visa', false)
            ->assertSee('GoAustriaTours '.date('Y'))
            ->assertDontSee('>Bad</a>', false)
            ->getContent();
        $this->assertStringNotContainsString('class="mtrig">Travel Style', $html);
        $this->assertLessThan(strpos($html, 'Where to go'), strpos($html, 'Winter offers'), 'custom link moved to the front');

        $saved = json_decode(Setting::get('menu_header'), true);
        $this->assertCount(7, $saved['items'], 'unsafe link dropped, built-ins kept');
    }

    public function test_page_specific_footer_cta_still_wins_and_reset_restores_defaults(): void
    {
        $f = Menus::defaultFooter();
        $f['cta_title'] = 'Custom default';
        $this->save(Menus::defaultHeader(), $f);
        $this->get('/tours/self-drive')->assertOk()->assertSee('Ready to plan your Self Drive tour?')->assertDontSee('Custom default');

        $this->actingAs($this->admin, 'admin')->post('/admin/menus/reset')->assertRedirect('/admin/menus');
        $this->assertNull(Setting::get('menu_footer'));
    }

    public function test_built_in_items_cannot_be_removed_only_hidden(): void
    {
        $clean = Menus::cleanHeader(['items' => [['key' => 'home', 'label' => 'Start', 'hidden' => false]]]);
        $this->assertSame(array_keys(Menus::HEADER_ITEMS), array_values(array_unique(array_column($clean['items'], 'key'))));
        $this->assertTrue(collect($clean['items'])->firstWhere('key', 'contact')['hidden']);
    }
}

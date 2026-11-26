<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Setting;
use App\Support\Design;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin > Design: colours, fonts, buttons; preview only for the admin; reset to the brand; nothing added while on brand defaults. */
class DesignTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    private function form(array $over = []): array
    {
        return $over + Design::DEFAULTS;
    }

    public function test_brand_defaults_add_no_css_and_keep_the_font_preloads(): void
    {
        $this->get('/')->assertOk()->assertDontSee('id="site-design"', false)
            ->assertSee('playfair-display-latin-wght-normal.woff2', false)->assertSee('playfair-display-latin-wght-italic.woff2', false);
        $this->actingAs($this->admin, 'admin')->get('/admin/design')->assertOk()->assertSee('Colour palettes')->assertSee('Live preview');
    }

    public function test_saving_a_design_changes_the_public_site(): void
    {
        $this->actingAs($this->admin, 'admin')->put('/admin/design', $this->form(Design::PRESETS['alpine'] + ['font_heading' => 'georgia', 'buttons' => 'pill', 'heading_case' => 'normal', 'body_weight' => '400']))
            ->assertRedirect('/admin/design');
        auth('admin')->logout();

        $this->get('/packages')->assertOk()
            ->assertSee('id="site-design"', false)
            ->assertSee('--navy: #1f3d33', false)->assertSee('--accent: #c8a25a', false)
            ->assertSee('--head: Georgia', false)
            ->assertSee('border-radius: 999px', false)
            ->assertSee('text-transform: none', false)
            ->assertSee('font-weight: 400', false)
            ->assertDontSee('playfair-display-latin-wght-normal.woff2', false);
        $this->assertNull(Setting::get('design_font_body'), 'values equal to the brand are not stored');
    }

    public function test_bad_values_are_rejected_or_ignored(): void
    {
        $this->actingAs($this->admin, 'admin')->put('/admin/design', $this->form(['navy' => 'red;}</style><script>x</script>']))->assertSessionHasErrors('navy');
        $this->actingAs($this->admin, 'admin')->put('/admin/design', $this->form(['font_heading' => 'comic-sans', 'buttons' => 'blob']))->assertRedirect();
        $this->assertTrue(Design::isDefault(Design::current()));
        $this->get('/')->assertDontSee('<script>x</script>', false);
    }

    public function test_preview_is_only_for_the_admin_until_saved_and_reset_restores_the_brand(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/design/preview', $this->form(Design::PRESETS['burgundy']))->assertRedirect('/');
        $this->actingAs($this->admin, 'admin')->get('/')->assertSee('Design preview (not saved')->assertSee('--navy: #5a1f2b', false);
        $this->assertTrue(Design::isDefault(Design::current()) === false);

        // Coming back to Design loads the preview into the form and ends the preview on the site.
        $this->actingAs($this->admin, 'admin')->get('/admin/design')->assertSee('Your previewed design is loaded');
        $this->actingAs($this->admin, 'admin')->get('/')->assertDontSee('Design preview (not saved')->assertDontSee('#5a1f2b', false);

        Setting::put('design_accent', '#ff0000');
        $this->actingAs($this->admin, 'admin')->post('/admin/design/reset')->assertRedirect('/admin/design');
        $this->assertNull(Setting::get('design_accent'));
    }

    public function test_every_preset_keeps_text_and_buttons_readable(): void
    {
        foreach (Design::PRESETS as $key => $p) {
            $this->assertGreaterThanOrEqual(4.5, Design::contrast($p['text'], $p['bg']), "$key text");
            $this->assertGreaterThanOrEqual(4.5, Design::contrast($p['navy'], $p['accent']), "$key button");
            $this->assertGreaterThanOrEqual(4.5, Design::contrast($p['accent_text'], $p['bg']), "$key small accent text");
            $this->assertGreaterThanOrEqual(4.5, Design::contrast('#ffffff', $p['dark']), "$key white on dark");
        }
    }
}

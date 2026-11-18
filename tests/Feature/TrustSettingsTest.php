<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): AdminUser
    {
        return AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    public function test_page_requires_login_and_lists_the_fields(): void
    {
        $this->get('/admin/trust')->assertRedirect(route('admin.login'));
        $this->actingAs($this->admin(), 'admin')->get('/admin/trust')->assertOk()
            ->assertSee('Trust &amp; tracking', false)->assertSee('Google Tag Manager ID')->assertSee('Mark 1: name')->assertSee('Established line');
    }

    public function test_trust_strip_is_hidden_until_something_is_filled_in(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Why you can trust us');
    }

    public function test_saving_shows_the_strip_contact_details_and_gtm(): void
    {
        $this->actingAs($this->admin(), 'admin')->put('/admin/trust', [
            'phone' => '+1 800 555 0199', 'phone_tel' => '+18005550199', 'email' => 'hello@example.com',
            'established_line' => 'Planning Austria journeys since 2012',
            'trust_mark_1_label' => 'ABTA Member', 'trust_mark_1_url' => 'https://example.com/abta',
            'review_summary' => 'Rated 4.9 from 120 Google reviews',
            'gtm_id' => 'GTM-ABC1234',
        ])->assertSessionHasNoErrors();

        $this->get('/')->assertOk()
            ->assertSee('Why you can trust us')->assertSee('Planning Austria journeys since 2012')->assertSee('ABTA Member')
            ->assertSee('Rated 4.9 from 120 Google reviews')->assertSee('tel:+18005550199', false)->assertSee('hello@example.com')
            ->assertSee('GTM-ABC1234');
    }

    public function test_confirm_placeholders_never_reach_the_public_strip(): void
    {
        Setting::put('established_line', '[CONFIRM: since when]');
        $this->get('/')->assertOk()->assertDontSee('CONFIRM')->assertDontSee('Why you can trust us');
    }

    public function test_bad_values_are_rejected(): void
    {
        $this->actingAs($this->admin(), 'admin')->put('/admin/trust', [
            'phone_tel' => '(800) 555', 'email' => 'nope', 'gtm_id' => 'abc', 'trust_mark_1_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors(['phone_tel', 'email', 'gtm_id', 'trust_mark_1_url']);
    }

    public function test_settings_page_no_longer_has_the_moved_fields(): void
    {
        $this->actingAs($this->admin(), 'admin')->get('/admin/settings')->assertOk()
            ->assertDontSee('Google Tag Manager ID')->assertDontSee('Planner name')->assertSee('reCAPTCHA');
    }
}

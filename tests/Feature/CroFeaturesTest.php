<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Enquiry;
use App\Models\Setting;
use App\Services\GhlPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Conversion features: sticky mobile bar, Google Ads message match, response promise, tracking IDs, GoHighLevel follow-up tags. */
class CroFeaturesTest extends TestCase
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

    // ---- sticky mobile bar
    public function test_sticky_bar_is_on_public_pages_but_not_where_a_form_or_own_bar_exists(): void
    {
        Setting::put('phone', '+1 800 555 0199');
        Setting::put('phone_tel', '+18005550199');

        $this->get('/')->assertOk()->assertSee('Plan My Journey')->assertSee('Call us on +1 800 555 0199', false);
        $this->get('/journal')->assertOk()->assertSee('Call us on +1 800 555 0199', false);
        $this->get('/contact')->assertOk()->assertDontSee('Call us on +1 800 555 0199', false);
        $this->get('/packages/imperial-austria-self-drive')->assertOk()->assertDontSee('Call us on +1 800 555 0199', false);
    }

    public function test_sticky_bar_without_a_phone_has_no_call_button(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Call us on', false);
    }

    // ---- Google Ads message match
    public function test_tour_parameter_rewords_the_hero_for_a_real_tour_type(): void
    {
        $this->get('/?tour=escorted')->assertOk()->assertSee('Escorted tours of Austria')->assertSee('Escorted Tours of Austria | ', false);
        $this->get('/')->assertOk()->assertSee('Bespoke journeys');
    }

    public function test_unknown_or_hostile_tour_values_are_ignored_and_never_echoed(): void
    {
        $this->get('/?tour=nonsense')->assertOk()->assertSee('Bespoke journeys');
        $this->get('/?tour=<script>alert(1)</script>')->assertOk()->assertDontSee('<script>alert(1)', false)->assertSee('Bespoke journeys');
    }

    public function test_canonical_ignores_the_query_string(): void
    {
        $html = $this->get('/?tour=coach')->assertOk()->getContent();
        preg_match('/<link rel="canonical" href="([^"]*)"/', $html, $m);
        $this->assertNotEmpty($m[1] ?? null);
        $this->assertStringNotContainsString('?', $m[1]);
        $this->assertStringNotContainsString('tour=', $m[1]);
    }

    // ---- response promise
    public function test_response_promise_shows_by_the_quiz_and_on_the_thank_you_page_only_when_set(): void
    {
        $this->get('/')->assertOk()->assertDontSee('within 1 business day');

        Setting::put('response_promise', 'A personal reply within 1 business day');
        $this->get('/')->assertOk()->assertSee('A personal reply within 1 business day');
        $this->get('/thank-you')->assertOk()->assertSee('A personal reply within 1 business day');

        Setting::put('response_promise', '[CONFIRM: promise]');
        $this->get('/')->assertOk()->assertDontSee('CONFIRM');
    }

    // ---- tracking IDs
    public function test_tracking_ids_are_validated_saved_and_injected_only_when_set(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('clarity.ms', $html);
        $this->assertStringNotContainsString('fbevents.js', $html);
        $this->assertStringNotContainsString('gtag/js', $html);

        $this->actingAs($this->admin(), 'admin')->put('/admin/trust', [
            'clarity_id' => 'abc123xyz9', 'google_ads_id' => 'AW-123456789', 'google_ads_conversion_label' => 'AbC-D_efG12', 'meta_pixel_id' => '1234567890123',
        ])->assertSessionHasNoErrors();

        $this->get('/')->assertOk()->assertSee('clarity.ms/tag', false)->assertSee('fbevents.js', false)->assertSee('gtag/js?id=AW-123456789', false);
        $this->get('/thank-you')->assertOk()->assertSee('AW-123456789\/AbC-D_efG12', false)->assertSee("fbq('track', 'Lead')", false);
    }

    public function test_bad_tracking_ids_are_rejected(): void
    {
        $this->actingAs($this->admin(), 'admin')->put('/admin/trust', [
            'clarity_id' => 'NOT valid!', 'google_ads_id' => '123', 'google_ads_conversion_label' => 'AW-1/x', 'meta_pixel_id' => 'abc',
        ])->assertSessionHasErrors(['clarity_id', 'google_ads_id', 'google_ads_conversion_label', 'meta_pixel_id']);
    }

    // ---- GoHighLevel follow-up tags
    public function test_ghl_contacts_get_the_nurture_and_interest_tags(): void
    {
        $e = Enquiry::create([
            'form_name' => 'hero-quiz', 'first_name' => 'Ada', 'last_name' => 'L', 'email' => 'ada@example.com', 'consent_given' => true, 'consent_text' => 'x',
            'terms_version' => '2026-01', 'consented_at' => now(), 'page_url' => 'https://goaustriatours.com/?tour=self-drive&gclid=X',
        ]);
        $tags = (new GhlPayload)->build($e, [])['tags'];
        $this->assertContains('nurture: new-lead', $tags);
        $this->assertContains('interest: self-drive', $tags);

        $e2 = Enquiry::create([
            'form_name' => 'contact', 'first_name' => 'B', 'last_name' => 'L', 'email' => 'b@example.com', 'consent_given' => true, 'consent_text' => 'x',
            'terms_version' => '2026-01', 'consented_at' => now(), 'page_url' => 'https://goaustriatours.com/?tour=bogus',
        ]);
        $tags2 = (new GhlPayload)->build($e2, [])['tags'];
        $this->assertContains('nurture: new-lead', $tags2);
        $this->assertCount(0, array_filter($tags2, fn ($t) => str_starts_with($t, 'interest:')));
    }
}

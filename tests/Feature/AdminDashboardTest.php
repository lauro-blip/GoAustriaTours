<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\AdminUser;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function enquiry(array $extra = []): Enquiry
    {
        return Enquiry::create($extra + [
            'form_name' => 'contact', 'first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.com',
            'consent_given' => true, 'consent_text' => 'x', 'terms_version' => '2026-01', 'consented_at' => now(),
        ]);
    }

    private function admin(): AdminUser
    {
        return AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    public function test_dashboard_needs_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_empty_dashboard_renders_with_the_setup_checklist(): void
    {
        $this->actingAs($this->admin(), 'admin')->get('/admin')->assertOk()
            ->assertSee('Enquiries per day', false)->assertSee('No enquiries in this period yet.')->assertSee('Site health')
            ->assertSee('GoHighLevel connected')->assertSee('Real reCAPTCHA keys');
    }

    public function test_dashboard_summarises_enquiries_sources_forms_and_problems(): void
    {
        $ads = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'austria-search', 'gclid' => 'G1'];
        $this->enquiry(['form_name' => 'hero-quiz', 'tracking_first' => $ads, 'tracking_last' => $ads, 'package_name' => 'Imperial Austria']);
        $this->enquiry(['form_name' => 'hero-quiz', 'email' => 'b@example.com', 'tracking_last' => ['gclid' => 'G2'], 'status' => 'won']);
        $this->enquiry(['form_name' => 'contact', 'email' => 'c@example.com', 'sheet_status' => 'failed']);
        $old = $this->enquiry(['email' => 'd@example.com']);
        $old->forceFill(['created_at' => now()->subDays(3)])->save();

        $admin = $this->admin();
        $html = $this->actingAs($admin, 'admin')->get('/admin?range=7')->assertOk()
            ->assertSee('Needs your attention')->assertSee('waited more than 24 hours')->assertSee('failed to sync')
            ->assertSee('google / cpc')->assertSee('direct')->assertSee('austria-search')->assertSee('Hero Quiz')->assertSee('Imperial Austria')
            ->getContent();

        $this->assertStringContainsString('Enquiries, last 7 days', $html);
        $this->actingAs($admin, 'admin')->get('/admin?range=999')->assertOk()->assertSee('Enquiries, last 30 days');
    }

    public function test_source_label_uses_latest_touch_then_click_ids_then_referrer(): void
    {
        $mk = fn ($first, $last = null) => new Enquiry(['tracking_first' => $first, 'tracking_last' => $last ?? $first]);

        $this->assertSame('facebook / social', DashboardController::sourceLabel($mk(['utm_source' => 'Facebook', 'utm_medium' => 'Social'])));
        $this->assertSame('google / cpc', DashboardController::sourceLabel($mk(['gclid' => 'X'])));
        $this->assertSame('bing.com', DashboardController::sourceLabel($mk(['referrer' => 'https://www.bing.com/search?q=x'])));
        $this->assertSame('direct', DashboardController::sourceLabel($mk([])));
        $this->assertSame('google / cpc', DashboardController::sourceLabel($mk(['utm_source' => 'organic'], ['utm_source' => 'google', 'utm_medium' => 'cpc'])));
    }

    public function test_inbox_can_be_filtered_to_failed_syncs_and_stale_enquiries(): void
    {
        $this->enquiry(['email' => 'ok@example.com']);
        $bad = $this->enquiry(['email' => 'bad@example.com', 'ghl_status' => 'failed']);
        $old = $this->enquiry(['email' => 'old@example.com']);
        $old->forceFill(['created_at' => now()->subDays(2)])->save();

        $admin = $this->admin();
        $this->actingAs($admin, 'admin')->get('/admin/enquiries?sync=failed')->assertOk()->assertSee('bad@example.com')->assertDontSee('ok@example.com');
        $this->actingAs($admin, 'admin')->get('/admin/enquiries?stale=1')->assertOk()->assertSee('old@example.com')->assertDontSee('ok@example.com');
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\SyncEnquiryToSheet;
use App\Models\Enquiry;
use App\Models\Setting;
use App\Support\SheetRow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SheetsTest extends TestCase
{
    use RefreshDatabase;

    private function enable(): void
    {
        Setting::put('sheets_webhook_url', 'https://script.google.com/macros/s/TEST/exec');
        Setting::put('sheets_secret', Crypt::encryptString('a-long-test-secret'));
    }

    private function enquiry(string $form = 'contact'): Enquiry
    {
        $touch = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'c1', 'utm_term' => 't', 'utm_content' => 'ad', 'gclid' => 'GCL',
            'utm_device' => 'm', 'referrer' => 'https://google.com/', 'landing_page' => 'https://x.test/?gclid=GCL'];

        return Enquiry::create([
            'form_name' => $form, 'first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.com', 'country_code' => '+1',
            'phone_e164' => '+12025550143', 'travel_date' => 'June', 'special_request' => '=HYPERLINK("x")', 'package_name' => 'Imperial Austria',
            'consent_given' => true, 'consent_text' => 'I agree...', 'terms_version' => '2026-01', 'consented_at' => now(),
            'ip_address' => '203.0.113.5', 'user_agent' => 'UA', 'page_url' => 'https://x.test/contact', 'tracking_first' => $touch, 'tracking_last' => $touch,
            'quiz_answers' => ['season' => 'Summer'], 'sheet_status' => 'pending',
        ]);
    }

    private function verifyFake(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
            'script.google.com/*' => Http::response(['ok' => true]),
        ]);
    }

    public function test_submitting_a_form_redirects_to_thank_you_and_adds_the_row_to_the_sheet(): void
    {
        $this->seed();
        $this->enable();
        $this->verifyFake();

        $this->post('/enquiry', [
            'form_name' => 'hero-quiz', 'page_url' => 'http://localhost/', 'first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.com',
            'country' => 'US', 'phone' => '2025550143', 'consent' => '1', 'g-recaptcha-response' => 'token',
        ])->assertRedirect(route('thank-you'));

        $e = Enquiry::firstOrFail();
        $this->assertSame('synced', $e->sheet_status);
        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), 'script.google.com') && $r['tab'] === 'Hero Quiz');
    }

    public function test_a_failing_sheet_never_breaks_the_thank_you_redirect(): void
    {
        $this->seed();
        $this->enable();
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
            'script.google.com/*' => Http::response('boom', 500),
        ]);

        $this->post('/enquiry', [
            'form_name' => 'contact', 'page_url' => 'http://localhost/contact', 'first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.com',
            'country' => 'US', 'consent' => '1', 'g-recaptcha-response' => 'token',
        ])->assertRedirect(route('thank-you'));

        $this->assertSame('failed', Enquiry::firstOrFail()->sheet_status);
    }

    public function test_both_sync_jobs_can_be_queued_after_commit(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        SyncEnquiryToSheet::dispatch(1)->afterCommit();
        \App\Jobs\SyncEnquiryToGhl::dispatch(1)->afterCommit();
        \Illuminate\Support\Facades\Queue::assertPushed(SyncEnquiryToSheet::class);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SyncEnquiryToGhl::class);
    }

    public function test_every_form_has_its_own_tab(): void
    {
        $this->assertSame(\App\Http\Controllers\EnquiryController::FORMS, array_keys(SheetRow::TABS));
        $this->assertCount(5, array_unique(SheetRow::TABS));
    }

    public function test_row_holds_every_field_including_utm_and_consent(): void
    {
        $row = SheetRow::row($this->enquiry());

        foreach (['Enquiry ID', 'Submitted At', 'Form', 'First Name', 'Last Name', 'Email', 'Phone', 'Country Code', 'Preferred Travel Date', 'Second Quote Preference',
            'Special Request', 'Package', 'Page URL', 'Quiz - Season', 'Consent Given', 'Consent Timestamp', 'Consent Text Version', 'Consent Text', 'IP Address', 'User Agent',
            'utm_source_first', 'utm_source_last', 'utm_medium_first', 'utm_campaign_last', 'utm_term_first', 'utm_content_last', 'gclid_first', 'gclid_last',
            'gbraid_first', 'wbraid_last', 'utm_device_first', 'utm_adposition_last', 'utm_adcreative_first', 'referrer_first', 'landing_page_last'] as $col) {
            $this->assertArrayHasKey($col, $row, $col);
        }
        $this->assertSame('google', $row['utm_source_first']);
        $this->assertSame('GCL', $row['gclid_last']);
        $this->assertSame('m', $row['utm_device_last']);
        $this->assertSame('Summer', $row['Quiz - Season']);
        $this->assertSame('Contact', $row['Form']);
    }

    public function test_job_posts_the_row_to_the_web_app_and_marks_it_synced(): void
    {
        $this->enable();
        Http::fake(['script.google.com/*' => Http::response(['ok' => true, 'row' => 2])]);
        $e = $this->enquiry('hero-quiz');

        SyncEnquiryToSheet::dispatchSync($e->id);

        Http::assertSent(function (HttpRequest $r) {
            return str_contains($r->url(), 'script.google.com')
                && $r['secret'] === 'a-long-test-secret'
                && $r['tab'] === 'Hero Quiz'
                && $r['row']['utm_source_last'] === 'google'
                && $r['row']['Email'] === 'ada@example.com';
        });
        $e->refresh();
        $this->assertSame('synced', $e->sheet_status);
        $this->assertNotNull($e->sheet_synced_at);
    }

    public function test_a_refused_row_is_recorded_as_failed_and_does_not_throw(): void
    {
        $this->enable();
        Http::fake(['script.google.com/*' => Http::response(['ok' => false, 'error' => 'wrong secret'])]);
        $e = $this->enquiry();

        SyncEnquiryToSheet::dispatchSync($e->id);

        $e->refresh();
        $this->assertSame('failed', $e->sheet_status);
        $this->assertStringContainsString('wrong secret', $e->sheet_error);
    }

    public function test_it_is_off_without_settings(): void
    {
        Http::fake();
        $e = $this->enquiry();

        SyncEnquiryToSheet::dispatchSync($e->id);

        Http::assertNothingSent();
        $this->assertSame('disabled', $e->refresh()->sheet_status);
    }

    public function test_admin_page_saves_the_secret_encrypted_and_test_row_works(): void
    {
        $this->get('/admin/sheets')->assertRedirect(route('admin.login'));

        $admin = \App\Models\AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
        $this->actingAs($admin, 'admin')->get('/admin/sheets')->assertOk()->assertSee('Hero Quiz');
        $this->actingAs($admin, 'admin')->put('/admin/sheets', ['sheets_webhook_url' => 'https://example.com/x', 'sheets_secret' => 'a-long-test-secret'])->assertSessionHasErrors('sheets_webhook_url');
        $this->actingAs($admin, 'admin')->put('/admin/sheets', ['sheets_webhook_url' => 'https://script.google.com/macros/s/ABC/exec', 'sheets_secret' => 'a-long-test-secret'])->assertSessionHasNoErrors();

        $this->assertNotSame('a-long-test-secret', Setting::get('sheets_secret'));
        $this->assertTrue(\App\Services\SheetsClient::configured());

        Http::fake(['script.google.com/*' => Http::response(['ok' => true])]);
        $this->actingAs($admin, 'admin')->post('/admin/sheets/test')->assertSessionHas('status');
        Http::assertSent(fn (HttpRequest $r) => $r['tab'] === 'Connection test');
    }
}

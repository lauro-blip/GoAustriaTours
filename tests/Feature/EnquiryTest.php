<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Support\Consent;
use App\Support\Tracking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY_URL = 'www.google.com/recaptcha/api/siteverify';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'form_name' => 'contact', 'page_url' => 'http://localhost/contact',
            'first_name' => 'Ada', 'last_name' => 'Lovelace', 'email' => 'ada@example.com',
            'country' => 'US', 'phone' => '2025550143', 'travel_date' => 'June 2027',
            'second_quote' => 'No', 'special_request' => 'Window seat please',
            'consent' => '1', 'g-recaptcha-response' => 'token',
        ], $overrides);
    }

    public function test_enquiry_fails_without_consent(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $payload = $this->payload();
        unset($payload['consent']);

        $this->post('/enquiry', $payload)->assertSessionHasErrors('consent');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_when_recaptcha_is_rejected(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

        $this->post('/enquiry', $this->payload())->assertSessionHasErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_without_a_recaptcha_token(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $this->post('/enquiry', $this->payload(['g-recaptcha-response' => '']))->assertSessionHasErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_enquiry_fails_closed_when_google_is_unreachable(): void
    {
        Http::fake([self::VERIFY_URL => fn () => throw new ConnectionException('timeout')]);

        $this->post('/enquiry', $this->payload())->assertSessionHasErrors('g-recaptcha-response');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_valid_enquiry_is_saved_with_consent_evidence(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $this->withHeader('User-Agent', 'PHPUnit-Test-Agent')
            ->post('/enquiry', $this->payload())
            ->assertRedirect(route('thank-you'));

        $e = Enquiry::firstOrFail();
        $this->assertTrue($e->consent_given);
        $this->assertSame(Consent::text(), $e->consent_text);
        $this->assertStringContainsString('Consent is not a condition of purchase.', $e->consent_text);
        $this->assertSame(config('goaustria.terms_version'), $e->terms_version);
        $this->assertNotNull($e->consented_at);
        $this->assertNotNull($e->ip_address);
        $this->assertSame('PHPUnit-Test-Agent', $e->user_agent);
        $this->assertSame('http://localhost/contact', $e->page_url);
        $this->assertSame('+12025550143', $e->phone_e164);
        $this->assertSame('disabled', $e->ghl_status); // GHL is off in tests
    }

    public function test_tracking_cookies_are_stored_with_the_enquiry(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);
        $first = ['utm_source' => 'google', 'gclid' => 'G1', 'landing_page' => 'http://localhost/?gclid=G1'];

        // Cookies are encrypted by Laravel, so build the value the way the framework would have.
        $enc = app('encrypter');
        $value = $enc->encrypt(\Illuminate\Cookie\CookieValuePrefix::create(Tracking::FIRST_COOKIE, $enc->getKey()).json_encode($first), false);

        $this->withUnencryptedCookie(Tracking::FIRST_COOKIE, $value)
            ->post('/enquiry', $this->payload());

        $e = Enquiry::firstOrFail();
        $this->assertSame('G1', $e->tracking_first['gclid']);
        $this->assertSame('G1', $e->tracking_last['gclid']); // falls back to first touch
    }

    public function test_honeypot_submissions_are_discarded(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $this->post('/enquiry', $this->payload(['website' => 'http://spam.example']))->assertRedirect(route('thank-you'));
        $this->assertSame(0, Enquiry::count());
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        Http::fake([self::VERIFY_URL => Http::response(['success' => true])]);

        $this->post('/enquiry', $this->payload(['phone' => '123']))->assertSessionHasErrors('phone');
        $this->assertSame(0, Enquiry::count());
    }

    public function test_every_page_with_a_form_renders_the_consent_text_unticked(): void
    {
        foreach (['/', '/contact', '/packages/imperial-austria-self-drive', '/tours/coach'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('Consent is not a condition of purchase.', $html, $url);
            $this->assertDoesNotMatchRegularExpression('/name="consent"[^>]*checked/', $html, $url);
        }
    }
}

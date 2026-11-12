<?php

namespace Tests\Feature;

use App\Jobs\SyncEnquiryToGhl;
use App\Models\Enquiry;
use App\Services\GhlPayload;
use App\Support\GhlFields;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GhlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function fieldIds(): array
    {
        return collect(array_keys(GhlFields::all()))->mapWithKeys(fn ($n) => [$n => 'id_'.md5($n)])->all();
    }

    private function enquiry(string $form, array $extra = []): Enquiry
    {
        $touch = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'austria', 'utm_term' => 'austria tours',
            'utm_content' => 'ad1', 'gclid' => 'GCL', 'gbraid' => 'GBR', 'wbraid' => 'WBR', 'referrer' => 'https://google.com/', 'landing_page' => 'https://x.test/?gclid=GCL'];

        return Enquiry::create($extra + [
            'form_name' => $form, 'first_name' => 'Ada', 'last_name' => 'L', 'email' => 'ada@example.com',
            'country_code' => '+1', 'phone_e164' => '+12025550143', 'consent_given' => true, 'consent_text' => 'x',
            'terms_version' => '2026-01', 'consented_at' => now(), 'tracking_first' => $touch, 'tracking_last' => $touch,
            'quiz_answers' => ['season' => 'Summer', 'budget' => '$2K per person'],
        ]);
    }

    /** @return array<string, array{0:string}> */
    public static function formTypes(): array
    {
        return array_combine(['hero-quiz', 'header-modal', 'package-quote', 'ask-question', 'contact'], array_map(fn ($f) => [$f], ['hero-quiz', 'header-modal', 'package-quote', 'ask-question', 'contact']));
    }

    /** @dataProvider formTypes */
    #[\PHPUnit\Framework\Attributes\DataProvider('formTypes')]
    public function test_payload_includes_utm_and_tracking_fields_for_every_form_type(string $form): void
    {
        $ids = $this->fieldIds();
        $built = (new GhlPayload)->build($this->enquiry($form), $ids);
        $sent = collect($built['contact']['customFields'])->pluck('field_value', 'id');

        $expect = ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'austria', 'utm_term' => 'austria tours', 'utm_content' => 'ad1',
            'gclid' => 'GCL', 'gbraid' => 'GBR', 'wbraid' => 'WBR', 'referrer' => 'https://google.com/', 'landing_page' => 'https://x.test/?gclid=GCL'];
        foreach ($expect as $key => $value) {
            $this->assertSame($value, $sent[$ids[GhlFields::last($key)]] ?? null, "last $key");
            $this->assertSame($value, $sent[$ids[GhlFields::first($key)]] ?? null, "first $key");
        }
        $this->assertSame($form, $sent[$ids[GhlFields::FORM_NAME]]);
        $this->assertSame('Yes', $sent[$ids[GhlFields::CONSENT_GIVEN]]);
        $this->assertSame('2026-01', $sent[$ids[GhlFields::CONSENT_VERSION]]);
        $this->assertSame('Summer', $sent[$ids[GhlFields::quiz('season')]]);
        $this->assertArrayHasKey($ids[GhlFields::QUIZ_ALL], $sent->all());
        $this->assertSame('+12025550143', $built['contact']['phone']);
        $this->assertContains('form: '.$form, $built['tags']);
        $this->assertSame([], $built['missing']);
    }

    public function test_returning_contact_keeps_first_touch_and_updates_last_touch(): void
    {
        $ids = $this->fieldIds();
        $e = $this->enquiry('contact', ['tracking_last' => ['utm_source' => 'facebook', 'gclid' => 'NEW']]);
        $existing = [$ids[GhlFields::first('utm_source')] => 'google', $ids[GhlFields::first('gclid')] => 'OLD'];

        $sent = collect((new GhlPayload)->build($e, $ids, $existing)['contact']['customFields'])->pluck('field_value', 'id');

        $this->assertArrayNotHasKey($ids[GhlFields::first('utm_source')], $sent->all(), 'first touch must not be overwritten');
        $this->assertArrayNotHasKey($ids[GhlFields::first('gclid')], $sent->all());
        $this->assertSame('facebook', $sent[$ids[GhlFields::last('utm_source')]]);
        $this->assertSame('NEW', $sent[$ids[GhlFields::last('gclid')]]);
    }

    public function test_sync_is_off_without_credentials_and_sends_no_requests(): void
    {
        Http::fake();
        $e = $this->enquiry('contact');

        $this->assertFalse(SyncEnquiryToGhl::enabled());
        (new SyncEnquiryToGhl($e->id))->handle(new GhlPayload);

        Http::assertNothingSent();
        $this->assertSame('disabled', $e->fresh()->ghl_status);
    }

    public function test_job_upserts_contact_and_records_sync_status(): void
    {
        config(['goaustria.ghl.token' => 'secret-pit', 'goaustria.ghl.location_id' => 'LOC1']);
        Cache::flush();
        Http::fake([
            '*/locations/LOC1/customFields*' => Http::response(['customFields' => collect($this->fieldIds())->map(fn ($id, $n) => ['id' => $id, 'name' => $n])->values()->all()]),
            '*/contacts/search/duplicate*' => Http::response([], 404),
            '*/contacts/upsert' => Http::response(['contact' => ['id' => 'C123'], 'new' => true]),
            '*/contacts/C123/tags' => Http::response(['tags' => []]),
        ]);
        $e = $this->enquiry('hero-quiz');

        (new SyncEnquiryToGhl($e->id))->handle(new GhlPayload);

        $e->refresh();
        $this->assertSame('synced', $e->ghl_status);
        $this->assertSame('C123', $e->ghl_contact_id);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/contacts/upsert')
            && $r->hasHeader('Authorization', 'Bearer secret-pit') && $r['locationId'] === 'LOC1' && $r['email'] === 'ada@example.com');
    }

    public function test_failure_is_recorded_without_leaking_the_token_and_does_not_throw(): void
    {
        config(['goaustria.ghl.token' => 'secret-pit', 'goaustria.ghl.location_id' => 'LOC1']);
        Cache::flush();
        Http::fake(['*' => Http::response(['message' => 'bad token secret-pit'], 401)]);
        $e = $this->enquiry('contact');

        (new SyncEnquiryToGhl($e->id))->handle(new GhlPayload);

        $e->refresh();
        $this->assertSame('failed', $e->ghl_status);
        $this->assertStringNotContainsString('secret-pit', (string) $e->ghl_error);
    }
}

<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Setting;
use App\Services\GhlClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminGhlSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): AdminUser
    {
        return AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
    }

    public function test_page_requires_login(): void
    {
        $this->get('/admin/ghl')->assertRedirect(route('admin.login'));
        $this->put('/admin/ghl', [])->assertRedirect(route('admin.login'));
        $this->post('/admin/ghl/test')->assertRedirect(route('admin.login'));
    }

    public function test_saving_stores_the_token_encrypted_and_turns_sync_on(): void
    {
        $this->assertFalse(GhlClient::configured());

        $this->actingAs($this->admin(), 'admin')
            ->put('/admin/ghl', ['ghl_location_id' => 'LOC12345', 'ghl_api_token' => 'pit-secret-token-9876'])
            ->assertSessionHasNoErrors();

        $stored = Setting::get('ghl_api_token');
        $this->assertNotSame('pit-secret-token-9876', $stored);
        $this->assertSame('pit-secret-token-9876', Crypt::decryptString($stored));
        $this->assertSame('LOC12345', Setting::get('ghl_location_id'));
        $this->assertTrue(GhlClient::configured());
    }

    public function test_page_never_shows_the_full_token(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')->put('/admin/ghl', ['ghl_location_id' => 'LOC12345', 'ghl_api_token' => 'pit-secret-token-9876']);

        $this->get('/admin/ghl')->assertOk()->assertSee('9876')->assertDontSee('pit-secret-token-9876');
    }

    public function test_blank_token_keeps_the_saved_one(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')->put('/admin/ghl', ['ghl_location_id' => 'LOC12345', 'ghl_api_token' => 'pit-secret-token-9876']);
        $this->put('/admin/ghl', ['ghl_location_id' => 'NEWLOC99', 'ghl_api_token' => '']);

        $this->assertSame('pit-secret-token-9876', GhlClient::savedToken());
        $this->assertSame('NEWLOC99', Setting::get('ghl_location_id'));
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->put('/admin/ghl', ['ghl_location_id' => 'bad id!', 'ghl_api_token' => 'has space'])
            ->assertSessionHasErrors(['ghl_location_id', 'ghl_api_token']);
    }

    public function test_remove_clears_saved_values(): void
    {
        $this->actingAs($this->admin(), 'admin')->put('/admin/ghl', ['ghl_location_id' => 'LOC12345', 'ghl_api_token' => 'pit-secret-token-9876']);
        $this->put('/admin/ghl', ['clear' => '1']);

        $this->assertNull(Setting::get('ghl_api_token'));
        $this->assertFalse(GhlClient::configured());
    }

    private function savedKeys(): void
    {
        $this->actingAs($this->admin(), 'admin')->put('/admin/ghl', ['ghl_location_id' => 'LOC12345', 'ghl_api_token' => 'pit-secret-token-9876']);
    }

    public function test_connection_test_reports_success(): void
    {
        $this->savedKeys();
        Http::fake(['*' => Http::response(['customFields' => [['id' => 'a', 'name' => 'Some field']]], 200)]);

        $this->post('/admin/ghl/test')->assertSessionHas('status');
    }

    public function test_connection_test_reports_failure_without_leaking_the_token(): void
    {
        $this->savedKeys();
        Http::fake(['*' => Http::response(['message' => 'Unauthorized pit-secret-token-9876'], 401)]);

        $this->post('/admin/ghl/test')->assertSessionHasErrors('ghl');
        $this->assertStringNotContainsString('pit-secret-token-9876', session('errors')->first('ghl'));
    }

    public function test_test_without_keys_asks_to_save_first(): void
    {
        $this->actingAs($this->admin(), 'admin')->post('/admin/ghl/test')->assertSessionHasErrors('ghl');
    }
}

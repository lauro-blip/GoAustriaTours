<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_require_login(): void
    {
        foreach (['/admin', '/admin/enquiries', '/admin/packages', '/admin/settings', '/admin/users', '/admin/media', '/admin/enquiries/export'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
    }

    public function test_admin_can_log_in_and_see_dashboard(): void
    {
        $admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'a-long-test-password'])->assertRedirect('/admin');
        $this->actingAs($admin, 'admin')->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    public function test_wrong_password_is_rejected_and_throttled(): void
    {
        AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'nope'])->assertSessionHasErrors('email');
        }
        // Sixth attempt is locked out even with the right password.
        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'a-long-test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_passwords_are_hashed(): void
    {
        $admin = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
        $this->assertNotSame('a-long-test-password', $admin->password);
        $this->assertTrue(password_verify('a-long-test-password', $admin->password));
    }

    public function test_admin_cannot_delete_self_or_last_admin(): void
    {
        $a = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
        $b = AdminUser::create(['name' => 'B', 'email' => 'b@example.com', 'password' => 'a-long-test-password']);

        $this->actingAs($a, 'admin')->delete("/admin/users/{$a->id}")->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('admin_users', ['id' => $a->id]);

        $this->actingAs($a, 'admin')->delete("/admin/users/{$b->id}")->assertRedirect();
        $this->assertDatabaseMissing('admin_users', ['id' => $b->id]);

        // Only one admin left: still protected.
        $this->actingAs($a, 'admin')->delete("/admin/users/{$a->id}")->assertSessionHasErrors('delete');
    }

    public function test_enquiries_csv_export_neutralises_formulas(): void
    {
        $c = new \App\Http\Controllers\Admin\EnquiryController;
        $this->assertSame("'=HYPERLINK(\"x\")", $c->safe('=HYPERLINK("x")'));
        $this->assertSame('plain', $c->safe('plain'));
    }
}

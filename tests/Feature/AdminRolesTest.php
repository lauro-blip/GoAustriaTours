<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin user roles (App\Support\Roles): Admin = everything, Editor = website content, Sales = dashboard + enquiries. */
class AdminRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function user(string $role, string $email): AdminUser
    {
        return AdminUser::create(['name' => ucfirst($role).' User', 'email' => $email, 'password' => 'a-long-test-password', 'role' => $role]);
    }

    public function test_new_and_existing_users_default_to_admin(): void
    {
        $u = AdminUser::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'a-long-test-password']);
        $this->assertSame('admin', $u->fresh()->role);
        $this->actingAs($u, 'admin')->get('/admin/users')->assertOk()->assertSee('Role')->assertSee('Admin');
    }

    public function test_editor_can_edit_content_but_not_enquiries_users_or_settings(): void
    {
        $ed = $this->user('editor', 'ed@example.com');
        foreach (['/admin/pages', '/admin/packages', '/admin/posts', '/admin/media', '/admin/menus', '/admin/design', '/admin/quiz'] as $url) {
            $this->actingAs($ed, 'admin')->get($url)->assertOk();
        }
        foreach (['/admin', '/admin/enquiries', '/admin/users', '/admin/settings', '/admin/trust', '/admin/ghl', '/admin/sheets'] as $url) {
            $this->actingAs($ed, 'admin')->get($url)->assertRedirect('/admin/pages');
        }
        $this->actingAs($ed, 'admin')->put('/admin/settings', ['brand_name' => 'Hacked'])->assertForbidden();
        $this->actingAs($ed, 'admin')->post('/admin/users', ['name' => 'X', 'email' => 'x@example.com', 'role' => 'admin', 'password' => 'a-long-test-password'])->assertForbidden();

        // The sidebar only lists what an Editor may open.
        $html = $this->actingAs($ed, 'admin')->get('/admin/pages')->getContent();
        $this->assertStringContainsString('>Pages</a>', $html);
        $this->assertStringNotContainsString('>Enquiries</a>', $html);
        $this->assertStringNotContainsString('>Admin users</a>', $html);
        $this->assertStringContainsString('Editor</span>', $html);
    }

    public function test_sales_sees_enquiries_but_cannot_delete_export_or_edit_the_site(): void
    {
        $sales = $this->user('sales', 'sales@example.com');
        $this->actingAs($sales, 'admin')->get('/admin')->assertOk();
        $this->actingAs($sales, 'admin')->get('/admin/enquiries')->assertOk()->assertDontSee('Export CSV');
        $this->actingAs($sales, 'admin')->get('/admin/enquiries/export')->assertRedirect('/admin');
        $this->actingAs($sales, 'admin')->get('/admin/pages')->assertRedirect('/admin');
        $this->actingAs($sales, 'admin')->get('/admin/design')->assertRedirect('/admin');

        $enquiry = Enquiry::create(['form_name' => 'contact', 'first_name' => 'Jo', 'last_name' => 'Doe', 'email' => 'jo@example.com', 'consent_given' => true, 'consent_text' => 'x', 'terms_version' => '2026-01', 'consented_at' => now()]);
        $this->actingAs($sales, 'admin')->get("/admin/enquiries/{$enquiry->id}")->assertOk()->assertDontSee('Delete enquiry');
        $this->actingAs($sales, 'admin')->delete("/admin/enquiries/{$enquiry->id}")->assertForbidden();
        $this->assertNotNull($enquiry->fresh());
    }

    public function test_login_sends_each_role_to_its_first_screen(): void
    {
        $this->user('editor', 'ed@example.com');
        $this->post('/admin/login', ['email' => 'ed@example.com', 'password' => 'a-long-test-password'])->assertRedirect('/admin/pages');
    }

    public function test_there_is_always_an_admin(): void
    {
        $admin = AdminUser::create(['name' => 'Boss', 'email' => 'boss@example.com', 'password' => 'a-long-test-password']);
        AdminUser::whereKeyNot($admin->id)->update(['role' => 'editor']); // Boss is now the only Admin
        $this->actingAs($admin, 'admin')->put("/admin/users/{$admin->id}", ['name' => 'Boss', 'email' => 'boss@example.com', 'role' => 'editor'])
            ->assertSessionHasErrors('role');
        $this->assertSame('admin', $admin->fresh()->role);

        $second = $this->user('admin', 'two@example.com');
        $this->actingAs($admin, 'admin')->put("/admin/users/{$second->id}", ['name' => 'Two', 'email' => 'two@example.com', 'role' => 'editor'])->assertRedirect();
        $this->assertSame('editor', $second->fresh()->role);
        $this->actingAs($admin, 'admin')->put("/admin/users/{$admin->id}", ['name' => 'Boss', 'email' => 'boss@example.com', 'role' => 'nonsense'])->assertSessionHasErrors('role');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/** Admin > Admin users (Admin role only): name, email, password and role (App\Support\Roles). There is always at least one Admin. */
class UserController extends CrudController
{
    protected function model(): string { return AdminUser::class; }
    protected function routeName(): string { return 'admin.users'; }
    protected function title(): string { return 'Admin users'; }
    protected function columns(): array { return ['Name' => 'name', 'Email' => 'email', 'Role' => 'role_label']; }

    protected function fields(): array
    {
        $help = collect(Roles::ROLES)->map(fn ($r) => '<b>'.e($r[0]).'</b>: '.e($r[1]))->implode('<br>');

        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => Roles::options(), 'help' => $help],
            ['name' => 'password', 'label' => 'Password (leave blank to keep the current one; minimum 12 characters)', 'type' => 'password'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('admin_users', 'email')->ignore($item?->id)],
            'role' => ['required', Rule::in(array_keys(Roles::ROLES))],
            'password' => [$item ? 'nullable' : 'required', 'string', 'min:12'],
        ];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // keep existing; the model hashes new values
        }
        // Never leave the site without an Admin (also stops you removing your own Admin role by accident).
        if ($item->exists && $item->isAdmin() && ($data['role'] ?? 'admin') !== 'admin' && AdminUser::where('role', 'admin')->count() <= 1) {
            back()->withInput()->withErrors(['role' => 'This is the only Admin. Make someone else an Admin first.'])->throwResponse();
        }

        return $data;
    }

    public function destroy(int $id)
    {
        if ($id === Auth::guard('admin')->id()) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }
        $user = AdminUser::findOrFail($id);
        if ($user->isAdmin() && AdminUser::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['delete' => 'You cannot delete the last Admin.']);
        }

        return parent::destroy($id);
    }
}

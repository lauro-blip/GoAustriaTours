<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends CrudController
{
    protected function model(): string { return AdminUser::class; }
    protected function routeName(): string { return 'admin.users'; }
    protected function title(): string { return 'Admin users'; }
    protected function columns(): array { return ['Name' => 'name', 'Email' => 'email']; }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['name' => 'password', 'label' => 'Password (leave blank to keep the current one; minimum 12 characters)', 'type' => 'password'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('admin_users', 'email')->ignore($item?->id)],
            'password' => [$item ? 'nullable' : 'required', 'string', 'min:12'],
        ];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // keep existing; the model hashes new values
        }

        return $data;
    }

    public function destroy(int $id)
    {
        if ($id === Auth::guard('admin')->id()) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }
        if (AdminUser::count() <= 1) {
            return back()->withErrors(['delete' => 'You cannot delete the last admin.']);
        }

        return parent::destroy($id);
    }
}

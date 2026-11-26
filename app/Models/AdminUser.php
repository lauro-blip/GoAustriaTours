<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_users';
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $attributes = ['role' => 'admin'];

    /** "Admin" / "Editor" / "Sales" for lists. */
    public function getRoleLabelAttribute(): string
    {
        return \App\Support\Roles::label($this->role);
    }

    public function isAdmin(): bool
    {
        return ($this->role ?: 'admin') === 'admin';
    }
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}

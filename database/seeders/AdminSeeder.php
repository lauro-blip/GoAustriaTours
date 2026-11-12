<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

/** Creates the first admin from ADMIN_SEED_EMAIL / ADMIN_SEED_PASSWORD. Never hard-code credentials. */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_SEED_EMAIL');
        $password = env('ADMIN_SEED_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_SEED_EMAIL / ADMIN_SEED_PASSWORD not set; no admin created.');
            return;
        }

        // Only create when missing so re-seeding never resets a password someone has changed.
        AdminUser::firstOrCreate(['email' => $email], ['name' => 'Administrator', 'password' => $password]);
    }
}

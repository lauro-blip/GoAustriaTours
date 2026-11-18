<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The hero quiz had 8 questions plus the contact step, which is too long for a hero card.
 * Keep destination, season, group size and nights; switch the other four OFF (not deleted:
 * they can be switched back on in Admin > Quiz). Fresh installs get the short quiz from QuizSeeder.
 */
return new class extends Migration
{
    private array $keys = ['travel_style', 'flights_booked', 'budget', 'likelihood'];

    public function up(): void
    {
        DB::table('quiz_steps')->whereIn('key', $this->keys)->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('quiz_steps')->whereIn('key', $this->keys)->update(['is_active' => true]);
    }
};

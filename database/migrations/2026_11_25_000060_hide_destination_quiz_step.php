<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Drop the "Vienna & Salzburg or Tyrol?" opener from the hero quiz so it starts with the season.
 * Switched OFF, not deleted: it can be turned back on in Admin > Quiz.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('quiz_steps')->where('key', 'destination')->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('quiz_steps')->where('key', 'destination')->update(['is_active' => true]);
    }
};

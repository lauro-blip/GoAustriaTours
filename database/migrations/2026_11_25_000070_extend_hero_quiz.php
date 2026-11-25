<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hero quiz v3: season, group size (1-20, 21+), nights (1-20, 21+), flights, budget, likelihood, age range.
 * Re-uses the switched-off flights_booked / budget / likelihood steps (new wording + options) and adds age_range.
 * Options are replaced: enquiries store the answer label, not the option id, so old enquiries are unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        foreach (\Database\Seeders\QuizSeeder::steps() as $i => $s) {
            $row = DB::table('quiz_steps')->where('key', $s['key'])->first();
            $values = ['question' => $s['question'], 'type' => $s['type'], 'sort_order' => $i + 1, 'is_active' => $s['active'], 'updated_at' => $now];
            if ($row) {
                DB::table('quiz_steps')->where('id', $row->id)->update($values);
                $id = $row->id;
            } else {
                $id = DB::table('quiz_steps')->insertGetId($values + ['key' => $s['key'], 'created_at' => $now]);
            }
            // Season and destination keep the options an admin may have edited.
            if (in_array($s['key'], ['season', 'destination'], true) && $row) {
                continue;
            }
            DB::table('quiz_options')->where('quiz_step_id', $id)->delete();
            foreach ($s['options'] as $j => [$label, $value, $icon]) {
                DB::table('quiz_options')->insert([
                    'quiz_step_id' => $id, 'label' => $label, 'value' => $value, 'icon' => $icon,
                    'sort_order' => $j + 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
        // Anything not in the new list (e.g. travel_style) stays off, after the others.
        DB::table('quiz_steps')->whereNotIn('key', array_column(\Database\Seeders\QuizSeeder::steps(), 'key'))->update(['is_active' => false, 'sort_order' => 99]);
    }

    public function down(): void
    {
        DB::table('quiz_steps')->whereIn('key', ['flights_booked', 'budget', 'likelihood', 'age_range'])->update(['is_active' => false]);
    }
};

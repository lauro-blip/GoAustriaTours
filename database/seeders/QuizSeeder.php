<?php

namespace Database\Seeders;

use App\Models\QuizStep;
use Illuminate\Database\Seeder;

/**
 * Default hero quiz, adapted from the GoIrishTours quiz.
 * The final contact step is built into the component and is not stored here. Kept short (4 questions) on purpose: the longer version lost leads.
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        if (QuizStep::count() > 0) {
            return;
        }

        $range = fn (int $from, int $to, string $unit) => collect(range($from, $to))->map(fn ($n) => [$n.' '.($n === 1 ? rtrim($unit, 's') : $unit), (string) $n])->all();

        $steps = [
            ['destination', 'Would you like to visit Vienna & Salzburg or Tyrol?', 'tiles', [
                ['Vienna & Salzburg', 'vienna-salzburg', 'city'], ['Tyrol & the Alps', 'tyrol', 'mountain'],
            ]],
            ['season', 'What time of year do you want to travel?', 'tiles', [
                ['Spring', 'spring', 'flower'], ['Summer', 'summer', 'sun'], ['Autumn', 'autumn', 'leaf'], ['Winter', 'winter', 'snow'],
            ]],
            ['group_size', 'How many people are in your group?', 'dropdown', array_map(fn ($o) => [$o[0], $o[1], null], [...$range(1, 9, 'people'), ['10 or more', '10+']])],
            ['nights', 'How many nights?', 'dropdown', array_map(fn ($o) => [$o[0], $o[1], null], [...$range(3, 14, 'nights'), ['15 or more', '15+']])],
        ];

        foreach ($steps as $i => [$key, $question, $type, $options]) {
            $step = QuizStep::create(['key' => $key, 'question' => $question, 'type' => $type, 'sort_order' => $i + 1]);
            foreach ($options as $j => [$label, $value, $icon]) {
                $step->options()->create(['label' => $label, 'value' => $value, 'icon' => $icon, 'sort_order' => $j + 1]);
            }
        }
    }
}

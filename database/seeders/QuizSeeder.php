<?php

namespace Database\Seeders;

use App\Models\QuizStep;
use Illuminate\Database\Seeder;

/**
 * Default hero quiz. The final contact step is built into the component and is not stored here.
 * steps() is also used by the 2026_11_25_000070 migration, so a fresh install and an upgraded one get the same quiz.
 * Icons are keys in resources/views/components/icon.blade.php; options without an icon render as text tiles.
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        if (QuizStep::count() > 0) {
            return;
        }

        foreach (self::steps() as $i => $s) {
            $step = QuizStep::create(['key' => $s['key'], 'question' => $s['question'], 'type' => $s['type'], 'sort_order' => $i + 1, 'is_active' => $s['active']]);
            foreach ($s['options'] as $j => [$label, $value, $icon]) {
                $step->options()->create(['label' => $label, 'value' => $value, 'icon' => $icon, 'sort_order' => $j + 1]);
            }
        }
    }

    /** @return array<int, array{key: string, question: string, type: string, active: bool, options: array<int, array{0: string, 1: string, 2: ?string}>}> */
    public static function steps(): array
    {
        $count = fn (string $one, string $many) => [
            ...array_map(fn ($n) => [$n.' '.($n === 1 ? $one : $many), (string) $n, null], range(1, 20)),
            ['21 or more', '21+', null],
        ];

        return [
            // Switched off: the quiz starts with the season. Can be enabled in Admin > Quiz.
            ['key' => 'destination', 'question' => 'Would you like to visit Vienna & Salzburg or Tyrol?', 'type' => 'tiles', 'active' => false, 'options' => [
                ['Vienna & Salzburg', 'vienna-salzburg', 'city'], ['Tyrol & the Alps', 'tyrol', 'mountain'],
            ]],
            ['key' => 'season', 'question' => 'What time of year do you want to travel?', 'type' => 'tiles', 'active' => true, 'options' => [
                ['Spring', 'spring', 'flower'], ['Summer', 'summer', 'sun'], ['Autumn', 'autumn', 'leaf'], ['Winter', 'winter', 'snow'],
            ]],
            ['key' => 'group_size', 'question' => 'How many people are in your group?', 'type' => 'dropdown', 'active' => true, 'options' => $count('person', 'people')],
            ['key' => 'nights', 'question' => 'How many nights do you want to stay for?', 'type' => 'dropdown', 'active' => true, 'options' => $count('night', 'nights')],
            ['key' => 'flights_booked', 'question' => 'Are your flights all set?', 'type' => 'tiles', 'active' => true, 'options' => [
                ['Yes', 'yes', 'check-circle'], ['No', 'no', 'cross-circle'],
            ]],
            ['key' => 'budget', 'question' => 'Do you have a budget in mind for your trip?', 'type' => 'tiles', 'active' => true, 'options' => [
                ['$1K per person', '1000', 'coins-1'], ['$2K per person', '2000', 'coins-2'], ['$3K+ per person', '3000+', 'coins-3'],
            ]],
            ['key' => 'likelihood', 'question' => 'What are the chances of you booking with us?', 'type' => 'tiles', 'active' => true, 'options' => [
                ["I'm 80% sure I'm going", '80', 'suitcase'], ["I'm 50/50", '50', 'scale'], ['Unsure, just researching', 'researching', 'book'],
            ]],
            ['key' => 'age_range', 'question' => 'To make sure we suggest the best experiences for your group, which age range applies to most of your travellers?', 'type' => 'tiles', 'active' => true, 'options' => [
                ['Under 30', 'under-30', null], ['30–40', '30-40', null], ['40–50', '40-50', null], ['50–60+', '50-60+', null],
            ]],
            // Kept switched off from the original quiz (travel style is covered by the tour type pages).
            ['key' => 'travel_style', 'question' => 'What is your favourite way to see a new country?', 'type' => 'tiles', 'active' => false, 'options' => [
                ['By coach with other travellers', 'coach', 'bus'], ['Driving yourself', 'self-drive', 'car'], ['With a tour guide driving', 'escorted', 'guide'], ["Don't know", 'unsure', 'question'],
            ]],
        ];
    }
}

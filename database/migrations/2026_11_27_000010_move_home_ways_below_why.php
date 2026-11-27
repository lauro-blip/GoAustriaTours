<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/** Home page: move "Your Journey. Your Way." (home_ways) directly below "Why travel with us" (home_why). The old order is kept in the page's version history. */
return new class extends Migration
{
    public function up(): void
    {
        $page = Page::slug('home');
        $blocks = $page?->blocks;
        if (! $page || empty($blocks)) {
            return; // nothing saved yet: HomeSections::ORDER already has the new default
        }
        $ways = collect($blocks)->search(fn ($b) => ($b['type'] ?? '') === 'home_ways');
        if ($ways === false) {
            return;
        }
        $block = $blocks[$ways];
        unset($blocks[$ways]);
        $blocks = array_values($blocks);
        $why = collect($blocks)->search(fn ($b) => ($b['type'] ?? '') === 'home_why');
        array_splice($blocks, $why === false ? 1 : $why + 1, 0, [$block]);
        $page->blocks = $blocks;
        $page->snapshot('Reorder: Your Way below Why');
        $page->save();
    }

    public function down(): void
    {
        // Restore the previous order from Admin > Pages > Home > History if needed.
    }
};

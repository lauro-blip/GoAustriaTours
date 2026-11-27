<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Destinations are places now (no regions). Saved page blocks still mentioning regions are updated:
 * the home stats label "Regions of Austria" becomes "Places to discover" (the number is now the count of places),
 * and "Journeys" blocks listing a region ("destination:...") list all journeys. Previous versions stay in each page's History.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Page::whereNotNull('blocks')->get() as $page) {
            $changed = false;
            $blocks = array_map(function ($b) use (&$changed) {
                if (($b['type'] ?? '') === 'home_difference' && ($b['data']['stat2_label'] ?? null) === 'Regions of Austria') {
                    $b['data']['stat2_label'] = 'Places to discover';
                    $changed = true;
                }
                if (($b['type'] ?? '') === 'journeys' && str_starts_with((string) ($b['data']['filter'] ?? ''), 'destination:')) {
                    $b['data']['filter'] = 'all';
                    $changed = true;
                }

                return $b;
            }, (array) $page->blocks);
            if ($changed) {
                $page->blocks = $blocks;
                $page->snapshot('Destinations are places (no regions)');
                $page->save();
            }
        }
    }

    public function down(): void
    {
        // Restore from each page's History if needed.
    }
};

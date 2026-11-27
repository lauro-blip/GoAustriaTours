<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/** Home page: hide "Moments Worth Travelling For" (home_moments). It can be switched back on in Admin > Pages > Home. */
return new class extends Migration
{
    public function up(): void
    {
        $page = Page::slug('home');
        if (! $page || empty($page->blocks)) {
            return; // nothing saved yet: HomeSections::HIDDEN already hides it by default
        }
        $page->blocks = array_map(fn ($b) => ($b['type'] ?? '') === 'home_moments' ? ['hidden' => true] + $b : $b, $page->blocks);
        $page->snapshot('Hide Moments Worth Travelling For');
        $page->save();
    }

    public function down(): void
    {
        // Show it again in Admin > Pages > Home (or restore from its History).
    }
};

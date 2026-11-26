<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Page builder: pages made of section blocks (App\Support\Blocks). Existing pages become "system" pages (cannot be deleted). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $t) {
            $t->json('blocks')->nullable()->after('sections');
            $t->boolean('is_system')->default(false)->after('noindex');
            $t->boolean('is_published')->default(true)->after('is_system');
            $t->string('eyebrow')->nullable()->after('title');
            $t->string('intro', 400)->nullable()->after('eyebrow');
            $t->string('header_image')->nullable()->after('intro');
            $t->string('nav_location', 20)->nullable()->after('is_published');
            $t->unsignedInteger('sort_order')->default(0)->after('nav_location');
        });
        DB::table('pages')->update(['is_system' => true]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $t) {
            $t->dropColumn(['blocks', 'is_system', 'is_published', 'eyebrow', 'intro', 'header_image', 'nav_location', 'sort_order']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Version history for Admin > Pages: a copy of the page is kept every time it is saved (last 30 per page). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_revisions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('page_id')->constrained()->cascadeOnDelete();
            $t->json('content');
            $t->string('author')->nullable();
            $t->timestamps();
            $t->index(['page_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_revisions');
    }
};

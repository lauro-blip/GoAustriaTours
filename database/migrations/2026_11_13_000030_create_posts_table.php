<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('category')->nullable();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->string('image_alt')->nullable();
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 320)->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

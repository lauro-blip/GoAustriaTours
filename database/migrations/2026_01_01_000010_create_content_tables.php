<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 320)->nullable();
            $t->longText('body')->nullable();
            $t->json('sections')->nullable();
            $t->boolean('noindex')->default(false);
            $t->timestamps();
        });

        Schema::create('tour_types', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('tagline')->nullable();
            $t->text('summary')->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->string('image_alt')->nullable();
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 320)->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('destinations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('summary')->nullable();
            $t->string('icon')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->foreignId('tour_type_id')->constrained()->cascadeOnDelete();
            $t->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedSmallInteger('nights')->default(7);
            $t->decimal('price_from', 10, 2)->nullable();
            $t->string('currency', 3)->default('USD');
            $t->text('summary')->nullable();
            $t->longText('description')->nullable();
            $t->json('highlights')->nullable();
            $t->json('itinerary')->nullable();   // [{title, body}]
            $t->json('inclusions')->nullable();
            $t->json('exclusions')->nullable();
            $t->json('faqs')->nullable();        // [{question, answer}]
            $t->string('hero_image')->nullable();
            $t->string('hero_image_alt')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('meta_description', 320)->nullable();
            $t->string('social_image')->nullable();
            $t->boolean('is_published')->default(false);
            $t->boolean('is_featured')->default(false);
            $t->boolean('noindex')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('package_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->string('alt');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('quiz_steps', function (Blueprint $t) {
            $t->id();
            $t->string('key');                 // answer key, e.g. "season"
            $t->string('question');
            $t->enum('type', ['tiles', 'dropdown'])->default('tiles');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quiz_step_id')->constrained()->cascadeOnDelete();
            $t->string('label');
            $t->string('value');
            $t->string('icon')->nullable();    // icon key from resources/views/components/icon
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('location')->nullable();
            $t->text('quote');
            $t->boolean('is_placeholder')->default(true);
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->string('question');
            $t->text('answer');
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('path');            // original WebP, relative to public disk
            $t->string('alt');
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->json('variants')->nullable(); // {480: path, 960: path, 1600: path}
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['media', 'faqs', 'testimonials', 'quiz_options', 'quiz_steps', 'package_images', 'packages', 'destinations', 'tour_types', 'pages', 'settings', 'admin_users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

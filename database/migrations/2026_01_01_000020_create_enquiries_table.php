<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $t) {
            $t->id();
            $t->string('form_name');                       // hero-quiz, header-modal, package-quote, ask-question, contact
            $t->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $t->string('package_name')->nullable();

            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');
            $t->string('country_code', 8)->nullable();     // e.g. +1
            $t->string('phone_e164', 20)->nullable();
            $t->string('travel_date')->nullable();
            $t->string('second_quote')->nullable();
            $t->text('special_request')->nullable();
            $t->json('quiz_answers')->nullable();

            // Consent / chargeback evidence
            $t->boolean('consent_given')->default(false);
            $t->text('consent_text');
            $t->string('terms_version', 20);
            $t->timestamp('consented_at');
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->text('page_url')->nullable();

            // Tracking (first touch + latest touch)
            $t->json('tracking_first')->nullable();
            $t->json('tracking_last')->nullable();

            // Admin workflow
            $t->enum('status', ['new', 'contacted', 'quoted', 'won', 'lost'])->default('new');
            $t->text('notes')->nullable();

            // GoHighLevel sync
            $t->string('ghl_status')->default('disabled'); // disabled|pending|synced|failed
            $t->string('ghl_contact_id')->nullable();
            $t->text('ghl_error')->nullable();
            $t->timestamp('ghl_synced_at')->nullable();

            $t->timestamps();
            $t->index(['status', 'created_at']);
            $t->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};

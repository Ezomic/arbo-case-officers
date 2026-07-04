<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Append-only log of every significant event on a case — used to render
    // the case timeline. Events are never updated or deleted.
    public function up(): void
    {
        Schema::create('case_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('case_id')->index();
            $table->string('event'); // case_opened, case_closed, return_date_set, outcome_shared, note_added
            $table->json('payload')->nullable(); // e.g. {"note_type": "Consult", "return_date": "2026-08-01"}
            $table->uuid('actor_user_id')->nullable();
            $table->string('actor_name')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            // No updated_at — append-only
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_events');
    }
};

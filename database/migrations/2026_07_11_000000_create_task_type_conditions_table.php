<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Shadow of admin's task_type_conditions, synced on demand via
     * TaskTypeSyncService — mirrors the task_types shadowing pattern.
     * Local auto-increment id; never keyed by a remote id.
     */
    public function up(): void
    {
        Schema::create('task_type_conditions', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_type_id')->index();
            $table->string('type');
            $table->string('case_type')->nullable();
            $table->string('milestone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_type_conditions');
    }
};

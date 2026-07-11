<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DB-level dedup guard for auto-generated tasks: a case can only have
     * one task of a given task type. Postgres treats each NULL as distinct
     * in a unique index, so manually created tasks (task_type_id null) are
     * unaffected.
     */
    public function up(): void
    {
        Schema::table('case_tasks', function (Blueprint $table) {
            $table->unique(['case_id', 'task_type_id']);
        });
    }

    public function down(): void
    {
        Schema::table('case_tasks', function (Blueprint $table) {
            $table->dropUnique(['case_id', 'task_type_id']);
        });
    }
};

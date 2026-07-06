<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('case_id')->index();
            $table->uuid('task_type_id')->nullable();
            $table->uuid('assigned_user_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_tasks');
    }
};

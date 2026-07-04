<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_type_case_types', function (Blueprint $table) {
            $table->id();
            $table->string('contract_type_id');
            $table->string('case_type');
            $table->timestamps();

            $table->unique(['contract_type_id', 'case_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_type_case_types');
    }
};

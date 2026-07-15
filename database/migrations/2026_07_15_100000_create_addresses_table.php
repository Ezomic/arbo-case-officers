<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuidMorphs('addressable');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('postal_code', 10);
            $table->string('city');
            $table->string('country', 2)->default('NL');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

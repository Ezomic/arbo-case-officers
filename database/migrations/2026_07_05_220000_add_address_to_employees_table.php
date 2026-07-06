<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->string('address_line_1')->nullable()->after('nationality');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('postal_code', 10)->nullable()->after('address_line_2');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('country', 2)->nullable()->default('NL')->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->dropColumn(['address_line_1', 'address_line_2', 'postal_code', 'city', 'country']);
        });
    }
};

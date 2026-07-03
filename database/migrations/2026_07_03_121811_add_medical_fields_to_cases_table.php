<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * These are the only medical-adjacent fields that live outside the
     * Doctors app — structured, non-medical outcomes (not diagnosis or
     * clinical notes, which stay fully isolated in Doctors' own database
     * per Dutch medical-confidentiality law). Doctors pushes these back
     * via its internal API after recording the actual medical detail.
     */
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->text('advice')->nullable()->after('case_type');
            $table->text('restrictions')->nullable()->after('advice');
            $table->date('expected_return_date')->nullable()->after('restrictions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['advice', 'restrictions', 'expected_return_date']);
        });
    }
};

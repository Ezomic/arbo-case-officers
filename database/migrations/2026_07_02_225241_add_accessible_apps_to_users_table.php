<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Other apps this person can also log into ({slug, name, base_url}),
     * synced from the SSO token's `apps` claim on every login — powers the
     * portal switcher without a live call back to Identity.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('accessible_apps')->nullable()->after('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accessible_apps');
        });
    }
};

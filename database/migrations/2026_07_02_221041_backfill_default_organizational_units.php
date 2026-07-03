<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Every existing Employer predates organizational units, so give each
     * one a default root unit (treated as the employer's own legal entity)
     * and attach its existing employees to it.
     */
    public function up(): void
    {
        DB::table('employers')->orderBy('id')->each(function (object $employer) {
            $unitId = (string) Str::uuid();

            DB::table('organizational_units')->insert([
                'id' => $unitId,
                'tenant_id' => $employer->tenant_id,
                'employer_id' => $employer->id,
                'parent_id' => null,
                'name' => $employer->name,
                'is_legal_entity' => true,
                'kvk_number' => $employer->kvk_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('employees')
                ->where('employer_id', $employer->id)
                ->whereNull('organizational_unit_id')
                ->update(['organizational_unit_id' => $unitId]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill only; nothing to reverse.
    }
};

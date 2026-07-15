<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('employees')
            ->whereNotNull('address_line_1')
            ->orderBy('id')
            ->chunkById(500, function ($employees): void {
                $now = now();

                $rows = $employees->map(fn ($employee) => [
                    'id' => (string) Str::uuid(),
                    'addressable_type' => 'App\\Models\\Employee',
                    'addressable_id' => $employee->id,
                    'address_line_1' => $employee->address_line_1,
                    'address_line_2' => $employee->address_line_2,
                    'postal_code' => $employee->postal_code,
                    'city' => $employee->city,
                    'country' => $employee->country ?? 'NL',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('addresses')->insert($rows);
            });
    }

    public function down(): void
    {
        DB::table('addresses')->where('addressable_type', 'App\\Models\\Employee')->delete();
    }
};

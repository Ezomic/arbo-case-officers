<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Local dev data (tenant/employer/contract/employees/case) is created
     * via the SSO login flow and manual testing, not seeded here — this
     * seeder only provisions the machine-to-machine token the Employers
     * app needs to call this app's internal API.
     */
    public function run(): void
    {
        $client = ApiClient::query()->firstOrCreate(['name' => 'employers-service']);

        $token = $client->createToken('employers-service', ['employers:read', 'employees:write', 'cases:create'])->plainTextToken;

        $this->command?->info("employers-service token (put in employers/.env as CASE_OFFICERS_SERVICE_TOKEN):\n{$token}");

        $doctorsClient = ApiClient::query()->firstOrCreate(['name' => 'doctors-service']);

        $doctorsToken = $doctorsClient->createToken('doctors-service', ['cases:read', 'cases:write'])->plainTextToken;

        $this->command?->info("doctors-service token (put in doctors/.env as CASE_OFFICERS_SERVICE_TOKEN):\n{$doctorsToken}");
    }
}

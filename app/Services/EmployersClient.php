<?php

namespace App\Services;

use App\Models\CaseFile;
use RobbinThijssen\IdentitySsoKit\Api\InternalApiClient;

class EmployersClient extends InternalApiClient
{
    protected function baseUrl(): string
    {
        return config('services.employers.base_url');
    }

    protected function token(): string
    {
        return config('services.employers.token');
    }

    public function syncCase(CaseFile $case): void
    {
        $this->put("cases/{$case->id}", [
            'tenant_id' => $case->tenant_id,
            'employee_id' => $case->employee_id,
            'status' => $case->status,
            'opened_at' => $case->opened_at->toDateString(),
            'expected_return_date' => $case->expected_return_date?->toDateString(),
            'closed_at' => $case->closed_at?->toDateString(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\CaseFile;
use App\Models\Employer;
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

    public function syncContactPersons(Employer $employer): void
    {
        $persons = $employer->contactPersons()
            ->get(['id', 'name', 'email', 'phone', 'job_title'])
            ->toArray();

        $this->put("employers/{$employer->id}/contact-persons", [
            'tenant_id' => $employer->tenant_id,
            'contact_persons' => $persons,
        ]);
    }

    public function syncCase(CaseFile $case): void
    {
        $this->put("cases/{$case->id}", [
            'tenant_id' => $case->tenant_id,
            'employee_id' => $case->employee_id,
            'case_type' => $case->case_type?->value,
            'status' => $case->status,
            'opened_at' => $case->opened_at->toDateString(),
            'expected_return_date' => $case->expected_return_date?->toDateString(),
            'closed_at' => $case->closed_at?->toDateString(),
        ]);
    }
}

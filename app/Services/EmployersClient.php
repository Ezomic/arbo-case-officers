<?php

namespace App\Services;

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
}

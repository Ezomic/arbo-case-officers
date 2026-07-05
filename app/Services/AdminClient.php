<?php

namespace App\Services;

use RobbinThijssen\IdentitySsoKit\Api\InternalApiClient;

/**
 * Admin owns contract_types (tenant-wide config) — this app only ever
 * reads them through Admin's internal API, then caches the result locally.
 */
class AdminClient extends InternalApiClient
{
    protected function baseUrl(): string
    {
        return config('services.admin.base_url');
    }

    protected function token(): string
    {
        return config('services.admin.token');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getContractTypes(string $tenantId): array
    {
        return $this->get('contract-types', ['tenant_id' => $tenantId]);
    }
}

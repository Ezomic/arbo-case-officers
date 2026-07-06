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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getNoteTypes(string $tenantId): array
    {
        return $this->get('note-types', ['tenant_id' => $tenantId, 'app_slug' => 'case-officers']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTaskTypes(string $tenantId): array
    {
        return $this->get('task-types', ['tenant_id' => $tenantId]);
    }

    /** @return list<string> */
    public function getRolePermissions(string $tenantId, string $roleName): array
    {
        return array_values($this->get('role-permissions', [
            'tenant_id' => $tenantId,
            'role_name' => $roleName,
            'app_slug' => 'case-officers',
        ]));
    }
}

<?php

namespace App\Services;

use RobbinThijssen\IdentitySsoKit\Api\InternalApiClient;

class IdentityClient extends InternalApiClient
{
    protected function baseUrl(): string
    {
        return config('services.identity.base_url');
    }

    protected function token(): string
    {
        return config('services.identity.token');
    }

    /**
     * @param  array<int, string>  $userTypes
     * @return array<int, array<string, mixed>>
     */
    public function getUsers(string $tenantId, array $userTypes): array
    {
        return $this->get('users', ['tenant_id' => $tenantId, 'user_types' => $userTypes]);
    }

    /**
     * @return array<string, mixed>
     */
    public function createUser(string $tenantId, string $name, string $email, string $userTypeId, ?string $scopeId = null): array
    {
        return $this->post('users', [
            'tenant_id' => $tenantId,
            'name' => $name,
            'email' => $email,
            'user_type_id' => $userTypeId,
            'scope_id' => $scopeId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateUser(string $tenantId, string $uuid, array $data): array
    {
        return $this->put("users/{$uuid}", [...$data, 'tenant_id' => $tenantId]);
    }

    public function deleteUser(string $tenantId, string $uuid): void
    {
        $this->delete("users/{$uuid}", ['tenant_id' => $tenantId]);
    }
}

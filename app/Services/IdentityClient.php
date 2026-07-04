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

    public function getUsers(string $tenantId, array $userTypes): array
    {
        return $this->get('users', ['tenant_id' => $tenantId, 'user_types' => $userTypes]);
    }

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

    public function updateUser(string $uuid, array $data): array
    {
        return $this->put("users/{$uuid}", $data);
    }

    public function deleteUser(string $uuid): void
    {
        $this->delete("users/{$uuid}");
    }
}

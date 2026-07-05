<?php

namespace App\Services;

use App\Models\ContractType;
use Illuminate\Database\Eloquent\Collection;

class ContractTypeSyncService
{
    public function __construct(private readonly AdminClient $client) {}

    /**
     * @return Collection<int, ContractType>
     */
    public function sync(string $tenantId): Collection
    {
        rescue(function () use ($tenantId) {
            foreach ($this->client->getContractTypes($tenantId) as $contractType) {
                ContractType::query()->updateOrCreate(
                    ['id' => $contractType['id']],
                    ['tenant_id' => $contractType['tenant_id'], 'name' => $contractType['name']],
                );
            }
        });

        return ContractType::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->oldest()
            ->get();
    }
}

<?php

namespace App\Services;

use App\Models\ContractType;
use App\Models\ContractTypeCaseType;
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

                ContractTypeCaseType::query()
                    ->where('contract_type_id', $contractType['id'])
                    ->delete();

                foreach ($contractType['case_types'] ?? [] as $caseType) {
                    ContractTypeCaseType::query()->create([
                        'contract_type_id' => $contractType['id'],
                        'case_type' => $caseType,
                    ]);
                }
            }
        });

        return ContractType::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->with('caseTypes')
            ->oldest()
            ->get();
    }
}

<?php

namespace App\Services;

use App\Models\TaskType;
use Illuminate\Database\Eloquent\Collection;

class TaskTypeSyncService
{
    public function __construct(private readonly AdminClient $client) {}

    /** @return Collection<int, TaskType> */
    public function sync(string $tenantId): Collection
    {
        $remote = rescue(fn () => $this->client->getTaskTypes($tenantId), null);

        if ($remote !== null) {
            $remoteIds = array_column($remote, 'id');

            foreach ($remote as $tt) {
                TaskType::withoutGlobalScope('tenant')->updateOrCreate(
                    ['id' => $tt['id']],
                    ['tenant_id' => $tt['tenant_id'], 'name' => $tt['name'], 'description' => $tt['description']],
                );
            }

            TaskType::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->whereNotIn('id', $remoteIds)
                ->delete();
        }

        return TaskType::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->oldest()
            ->get();
    }
}

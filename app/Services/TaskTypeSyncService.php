<?php

namespace App\Services;

use App\Models\TaskType;
use App\Models\TaskTypeCondition;
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

                $this->syncConditions($tt['id'], $tt['conditions'] ?? []);
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

    /** @param list<array{type: string, case_type: string|null, milestone: string|null}> $conditions */
    private function syncConditions(string $taskTypeId, array $conditions): void
    {
        TaskTypeCondition::query()->where('task_type_id', $taskTypeId)->delete();

        foreach ($conditions as $condition) {
            TaskTypeCondition::query()->create([
                'task_type_id' => $taskTypeId,
                'type' => $condition['type'],
                'case_type' => $condition['case_type'],
                'milestone' => $condition['milestone'],
            ]);
        }
    }
}

<?php

use App\Models\TaskType;
use App\Models\TaskTypeCondition;
use App\Services\AdminClient;
use App\Services\TaskTypeSyncService;
use RobbinThijssen\IdentitySsoKit\Api\InternalApiException;

it('updates an existing local task type from the remote response', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $taskType = new TaskType;
    $taskType->forceFill([
        'id' => $taskTypeId,
        'tenant_id' => $tenantId,
        'name' => 'Follow-up call',
        'description' => 'Old description.',
    ])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Follow-up call (updated)',
                    'description' => 'New description.',
                ],
            ]);
    });

    $service = app(TaskTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);

    $updated = $result->first();
    expect($updated->id)->toBe($taskTypeId)
        ->and($updated->name)->toBe('Follow-up call (updated)')
        ->and($updated->description)->toBe('New description.');

    $this->assertDatabaseHas('task_types', [
        'id' => $taskTypeId,
        'name' => 'Follow-up call (updated)',
        'description' => 'New description.',
    ]);
});

it('deletes local task types that no longer exist in the remote response', function () {
    $tenantId = (string) Str::uuid();

    $stale = new TaskType;
    $stale->forceFill(['tenant_id' => $tenantId, 'name' => 'Stale task type'])->save();

    $keptId = (string) Str::uuid();

    $kept = new TaskType;
    $kept->forceFill(['id' => $keptId, 'tenant_id' => $tenantId, 'name' => 'Kept task type'])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $keptId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $keptId,
                    'tenant_id' => $tenantId,
                    'name' => 'Kept task type',
                    'description' => null,
                ],
            ]);
    });

    $service = app(TaskTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($keptId);

    $this->assertDatabaseMissing('task_types', ['id' => $stale->id]);
    $this->assertDatabaseHas('task_types', ['id' => $keptId]);
});

it('falls back to the local cache and does not throw when the remote call fails', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $taskType = new TaskType;
    $taskType->forceFill([
        'id' => $taskTypeId,
        'tenant_id' => $tenantId,
        'name' => 'Existing task type',
        'description' => 'Already cached locally.',
    ])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andThrow(InternalApiException::fromResponse('GET', 'task-types', 500, 'admin unavailable'));
    });

    $service = app(TaskTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($taskTypeId);
    expect($result->first()->name)->toBe('Existing task type');

    $this->assertDatabaseHas('task_types', ['id' => $taskTypeId]);
});

it('does not throw when the remote call throws a generic exception', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $taskType = new TaskType;
    $taskType->forceFill([
        'id' => $taskTypeId,
        'tenant_id' => $tenantId,
        'name' => 'Existing task type',
    ])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andThrow(new RuntimeException('connection refused'));
    });

    $service = app(TaskTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($taskTypeId);
});

it('persists a newly synced task type under the id supplied by the remote response', function () {
    // Regression test for a real bug: TaskType::$fillable (via #[Fillable])
    // used to omit "id", so updateOrCreate(['id' => $tt['id']], ...) silently
    // dropped the incoming id on insert. HasUuidPrimaryKey then assigned a
    // random UUID, and the immediately following whereNotIn('id', $remoteIds)
    // cleanup deleted that row again because its real id never matched
    // $remoteIds — sync() could never persist a task type it hadn't already
    // seen before, for a tenant with no prior local cache.
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Follow-up call',
                    'description' => null,
                ],
            ]);
    });

    $service = app(TaskTypeSyncService::class);
    $result = $service->sync($tenantId);

    $this->assertDatabaseHas('task_types', ['id' => $taskTypeId, 'name' => 'Follow-up call']);
    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($taskTypeId);
});

it('survives a second sync without deleting and recreating the task type under a new id', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->twice()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Follow-up call',
                    'description' => null,
                ],
            ]);
    });

    $service = app(TaskTypeSyncService::class);
    $service->sync($tenantId);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($taskTypeId);
    $this->assertDatabaseCount('task_types', 1);
});

it('creates local condition rows from the remote payload', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Plan van aanpak opstellen',
                    'description' => null,
                    'conditions' => [
                        ['type' => 'case_type', 'case_type' => 'verzuim', 'milestone' => null],
                        ['type' => 'milestone_due', 'case_type' => null, 'milestone' => 'plan_van_aanpak'],
                        ['type' => 'return_date_overdue', 'case_type' => null, 'milestone' => null],
                    ],
                ],
            ]);
    });

    app(TaskTypeSyncService::class)->sync($tenantId);

    $conditions = TaskTypeCondition::query()->where('task_type_id', $taskTypeId)->get();
    expect($conditions)->toHaveCount(3)
        ->and($conditions->firstWhere('type', 'case_type')?->case_type?->value)->toBe('verzuim')
        ->and($conditions->firstWhere('type', 'milestone_due')?->milestone?->value)->toBe('plan_van_aanpak')
        ->and($conditions->contains('type', 'return_date_overdue'))->toBeTrue();
});

it('replaces local condition rows on re-sync when the remote conditions change', function () {
    $tenantId = (string) Str::uuid();
    $taskTypeId = (string) Str::uuid();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Follow-up call',
                    'description' => null,
                    'conditions' => [['type' => 'return_date_overdue', 'case_type' => null, 'milestone' => null]],
                ],
            ]);
    });
    app(TaskTypeSyncService::class)->sync($tenantId);

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $taskTypeId) {
        $mock->shouldReceive('getTaskTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $taskTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Follow-up call',
                    'description' => null,
                    'conditions' => [],
                ],
            ]);
    });
    app(TaskTypeSyncService::class)->sync($tenantId);

    expect(TaskTypeCondition::query()->where('task_type_id', $taskTypeId)->count())->toBe(0);
});

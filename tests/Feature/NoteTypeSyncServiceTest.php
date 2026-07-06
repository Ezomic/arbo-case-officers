<?php

use App\Models\NoteType;
use App\Services\AdminClient;
use App\Services\NoteTypeSyncService;
use RobbinThijssen\IdentitySsoKit\Api\InternalApiException;

it('deletes local note types that no longer exist in the remote response', function () {
    $tenantId = (string) Str::uuid();

    $stale = new NoteType;
    $stale->forceFill(['tenant_id' => $tenantId, 'name' => 'Stale note type'])->save();

    $keptId = (string) Str::uuid();

    $kept = new NoteType;
    $kept->forceFill(['id' => $keptId, 'tenant_id' => $tenantId, 'name' => 'Kept note type'])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $keptId) {
        $mock->shouldReceive('getNoteTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $keptId,
                    'tenant_id' => $tenantId,
                    'name' => 'Kept note type (renamed)',
                    'permissions' => [],
                ],
            ]);
    });

    $service = app(NoteTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($keptId);
    expect($result->first()->name)->toBe('Kept note type (renamed)');

    $this->assertDatabaseMissing('note_types', ['id' => $stale->id]);
    $this->assertDatabaseHas('note_types', ['id' => $keptId, 'name' => 'Kept note type (renamed)']);
});

it('removes permissions for roles no longer present in the remote response', function () {
    $tenantId = (string) Str::uuid();
    $noteTypeId = (string) Str::uuid();

    $noteType = new NoteType;
    $noteType->forceFill(['id' => $noteTypeId, 'tenant_id' => $tenantId, 'name' => 'Medical note'])->save();

    $noteType->permissions()->create([
        'role' => 'employer',
        'can_read' => true,
        'can_write' => false,
        'can_update' => false,
        'can_delete' => false,
    ]);

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $noteTypeId) {
        $mock->shouldReceive('getNoteTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $noteTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Medical note',
                    'permissions' => [
                        [
                            'role' => 'arbo',
                            'can_read' => true,
                            'can_write' => true,
                            'can_update' => true,
                            'can_delete' => true,
                        ],
                    ],
                ],
            ]);
    });

    $service = app(NoteTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);

    $permissions = $result->first()->permissions;
    expect($permissions)->toHaveCount(1);
    expect($permissions->first()->role)->toBe('arbo');
});

it('falls back to the local cache and does not throw when the remote call fails', function () {
    $tenantId = (string) Str::uuid();
    $noteTypeId = (string) Str::uuid();

    $noteType = new NoteType;
    $noteType->forceFill(['id' => $noteTypeId, 'tenant_id' => $tenantId, 'name' => 'Existing note type'])->save();

    $noteType->permissions()->create([
        'role' => 'arbo',
        'can_read' => true,
        'can_write' => true,
        'can_update' => false,
        'can_delete' => false,
    ]);

    $this->mock(AdminClient::class, function ($mock) use ($tenantId) {
        $mock->shouldReceive('getNoteTypes')
            ->once()
            ->with($tenantId)
            ->andThrow(InternalApiException::fromResponse('GET', 'note-types', 500, 'admin unavailable'));
    });

    $service = app(NoteTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($noteTypeId);
    expect($result->first()->name)->toBe('Existing note type');
    expect($result->first()->permissions)->toHaveCount(1);

    $this->assertDatabaseHas('note_types', ['id' => $noteTypeId]);
});

it('does not throw when the remote call throws a generic exception', function () {
    $tenantId = (string) Str::uuid();
    $noteTypeId = (string) Str::uuid();

    $noteType = new NoteType;
    $noteType->forceFill(['id' => $noteTypeId, 'tenant_id' => $tenantId, 'name' => 'Existing note type'])->save();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId) {
        $mock->shouldReceive('getNoteTypes')
            ->once()
            ->with($tenantId)
            ->andThrow(new RuntimeException('connection refused'));
    });

    $service = app(NoteTypeSyncService::class);
    $result = $service->sync($tenantId);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($noteTypeId);
});

it('does not persist a newly synced note type under the id supplied by the remote response', function () {
    // Documents a real bug: NoteType::$fillable (via #[Fillable]) does not
    // include "id", so updateOrCreate(['id' => $nt['id']], ...) silently
    // drops the incoming id on insert. HasUuidPrimaryKey then assigns a
    // random UUID, and the immediately following whereNotIn('id', $remoteIds)
    // cleanup deletes that row again because its real id never matches
    // $remoteIds. Net effect: sync() can never persist a note type it has
    // not already seen before, for a tenant with no prior local cache.
    $tenantId = (string) Str::uuid();
    $noteTypeId = (string) Str::uuid();

    $this->mock(AdminClient::class, function ($mock) use ($tenantId, $noteTypeId) {
        $mock->shouldReceive('getNoteTypes')
            ->once()
            ->with($tenantId)
            ->andReturn([
                [
                    'id' => $noteTypeId,
                    'tenant_id' => $tenantId,
                    'name' => 'Medical note',
                    'permissions' => [],
                ],
            ]);
    });

    $service = app(NoteTypeSyncService::class);
    $result = $service->sync($tenantId);

    $this->assertDatabaseMissing('note_types', ['id' => $noteTypeId]);
    expect($result)->toHaveCount(0);
});

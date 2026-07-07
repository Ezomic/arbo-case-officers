<?php

use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;

test('a user without view-employers cannot list employers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/employers');

    $response->assertForbidden();
});

test('a user with view-employers can list employers', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'View employers',
    ]);
    $this->actingAs($user);

    $response = $this->get('/employers');

    $response->assertOk();
});

test('a user without view-employers cannot view an employer', function () {
    $user = User::factory()->create();
    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $this->actingAs($user);

    $response = $this->get("/employers/{$employer->id}");

    $response->assertForbidden();
});

test('a user with view-employers can view an employer', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'View employers',
    ]);
    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $this->actingAs($user);

    $response = $this->get("/employers/{$employer->id}");

    $response->assertOk();
});

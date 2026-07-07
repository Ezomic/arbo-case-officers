<?php

use App\Models\Employee;
use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;

test('a user without view-employees cannot list employees', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/employees');

    $response->assertForbidden();
});

test('a user with view-employees can list employees', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'View employees',
    ]);
    $this->actingAs($user);

    $response = $this->get('/employees');

    $response->assertOk();
});

test('a user without view-employees cannot search employees', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $response = $this->get('/employees/search?q=Jane');

    $response->assertForbidden();
});

test('a user with view-employees can search employees', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'View employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $response = $this->get('/employees/search?q=Jane');

    $response->assertOk();
});

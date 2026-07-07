<?php

use App\Models\Employer;
use App\Models\OrganizationalUnit;
use App\Models\RolePermission;
use App\Models\User;

test('a user without manage-contracts cannot create an organizational unit', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $response = $this->post("/employers/{$employer->id}/organizational-units", [
        'name' => 'Sales',
    ]);

    $response->assertForbidden();
});

test('a user without manage-contracts cannot update an organizational unit', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $unit = $employer->organizationalUnits()->create(['tenant_id' => $user->tenant_id, 'name' => 'Sales']);

    $response = $this->put("/employers/{$employer->id}/organizational-units/{$unit->id}", [
        'name' => 'Sales Updated',
    ]);

    $response->assertForbidden();
});

test('a user with manage-contracts can create an organizational unit', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage contracts',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $response = $this->post("/employers/{$employer->id}/organizational-units", [
        'name' => 'Sales',
    ]);

    $response->assertRedirect(route('employers.show', $employer));
    expect(OrganizationalUnit::query()->where('employer_id', $employer->id)->where('name', 'Sales')->exists())->toBeTrue();
});

test('a user with manage-contracts can update an organizational unit', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage contracts',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $unit = $employer->organizationalUnits()->create(['tenant_id' => $user->tenant_id, 'name' => 'Sales']);

    $response = $this->put("/employers/{$employer->id}/organizational-units/{$unit->id}", [
        'name' => 'Sales Updated',
    ]);

    $response->assertRedirect(route('employers.show', $employer));
    expect($unit->fresh()->name)->toBe('Sales Updated');
});

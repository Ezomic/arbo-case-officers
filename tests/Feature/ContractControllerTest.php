<?php

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Str;

test('a user without manage-contracts cannot add a contract to an employer', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $contractType = ContractType::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Basis']);

    $response = $this->post("/employers/{$employer->id}/contracts", [
        'contract_type_id' => $contractType->id,
        'start_date' => '2026-01-01',
    ]);

    $response->assertForbidden();
});

test('a user with manage-contracts can add a contract to an employer', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage contracts',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $contractType = ContractType::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Basis']);

    $response = $this->post("/employers/{$employer->id}/contracts", [
        'contract_type_id' => $contractType->id,
        'start_date' => '2026-01-01',
    ]);

    $response->assertRedirect(route('employers.show', $employer));
    expect(Contract::query()->where('employer_id', $employer->id)->exists())->toBeTrue();
});

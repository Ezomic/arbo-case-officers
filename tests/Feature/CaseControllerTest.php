<?php

use App\Models\Contract;
use App\Models\ContractType;
use App\Models\ContractTypeCaseType;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

function caseOfficerWithPermission(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        RolePermission::query()->create([
            'tenant_id' => $user->tenant_id,
            'role_name' => $user->current_role,
            'permission' => $permission,
        ]);
    }

    return $user;
}

test('a user without manage-cases cannot open a case', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $response = $this->post('/cases', [
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'start_date' => '2026-07-01',
    ]);

    $response->assertForbidden();
});

test('a user with manage-cases can open a case', function () {
    Http::fake(['*/cases/*' => Http::response([])]);

    $user = caseOfficerWithPermission('Manage cases');
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $response = $this->post('/cases', [
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'start_date' => '2026-07-01',
    ]);

    $response->assertRedirect(route('cases.index'));
});

test('a case type not allowed by the employer active contract is rejected', function () {
    Http::fake(['*/cases/*' => Http::response([])]);

    $user = caseOfficerWithPermission('Manage cases');
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $contractType = ContractType::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Basis']);
    ContractTypeCaseType::query()->create(['contract_type_id' => $contractType->id, 'case_type' => 'pmo']);

    Contract::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'contract_type_id' => $contractType->id,
        'status' => 'active',
        'start_date' => '2026-01-01',
    ]);

    $response = $this->post('/cases', [
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'start_date' => '2026-07-01',
    ]);

    $response->assertSessionHasErrors('case_type');
});

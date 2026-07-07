<?php

use App\Models\ApiClient;
use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

function apiClientWithAbility(string ...$abilities): ApiClient
{
    $client = ApiClient::query()->create(['name' => 'Employers']);

    Sanctum::actingAs($client, $abilities, 'api-client');

    return $client;
}

test('show rejects a request scoped to the wrong tenant and returns the case owned by its actual tenant', function () {
    apiClientWithAbility('cases:read');

    $ownTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $ownTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $ownTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $ownTenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response = $this->getJson("/api/cases/{$case->id}?tenant_id=".Str::uuid());

    $response->assertOk();
    expect($response->json('id'))->toBe($case->id);
});

test('mutate on an existing case ignores a mismatched tenant_id and still succeeds by uuid', function () {
    apiClientWithAbility('cases:write');

    $ownTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $ownTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $ownTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $ownTenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response = $this->postJson("/api/cases/{$case->id}/mutate", [
        'tenant_id' => (string) Str::uuid(),
        'expected_return_date' => '2026-08-01',
    ]);

    $response->assertOk();
    expect($case->fresh()->expected_return_date->toDateString())->toBe('2026-08-01');
});

test('close on an existing case ignores a mismatched tenant_id and still succeeds by uuid', function () {
    apiClientWithAbility('cases:write');

    $ownTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $ownTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $ownTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $ownTenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response = $this->postJson("/api/cases/{$case->id}/close", [
        'tenant_id' => (string) Str::uuid(),
        'recovery_date' => '2026-08-01',
    ]);

    $response->assertOk();
    expect($case->fresh()->status)->toBe('closed');
});

test('update on an existing case ignores a mismatched tenant_id and still succeeds by uuid', function () {
    apiClientWithAbility('cases:write');

    $ownTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $ownTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $ownTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $ownTenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response = $this->patchJson("/api/cases/{$case->id}", [
        'tenant_id' => (string) Str::uuid(),
        'advice' => 'Rest for two weeks.',
    ]);

    $response->assertOk();
    expect($case->fresh()->advice)->toBe('Rest for two weeks.');
});

test('sync rejects an employee_id that does not belong to the claimed tenant_id', function () {
    apiClientWithAbility('cases:write');

    $realTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $realTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $realTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $caseId = (string) Str::uuid();
    $attackerTenantId = (string) Str::uuid();

    $response = $this->putJson("/api/cases/{$caseId}", [
        'tenant_id' => $attackerTenantId,
        'employee_id' => $employee->id,
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response->assertNotFound();
    expect(CaseFile::withoutGlobalScope('tenant')->find($caseId))->toBeNull();
});

test('sync creates the case when the employee_id genuinely belongs to the claimed tenant_id', function () {
    apiClientWithAbility('cases:write');

    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    $caseId = (string) Str::uuid();

    $response = $this->putJson("/api/cases/{$caseId}", [
        'tenant_id' => $tenantId,
        'employee_id' => $employee->id,
        'status' => 'open',
        'opened_at' => '2026-07-01',
        'expected_return_date' => null,
        'closed_at' => null,
    ]);

    $response->assertNoContent();
    expect(CaseFile::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->where('employee_id', $employee->id)->exists())->toBeTrue();
});

test('sync updating an existing case still requires the employee_id to belong to the claimed tenant_id', function () {
    apiClientWithAbility('cases:write');

    $ownTenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $ownTenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $ownTenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $ownTenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $otherTenantId = (string) Str::uuid();

    $response = $this->putJson("/api/cases/{$case->id}", [
        'tenant_id' => $otherTenantId,
        'employee_id' => $employee->id,
        'status' => 'closed',
        'opened_at' => '2026-07-01',
    ]);

    $response->assertNotFound();
    expect($case->fresh()->status)->toBe('open');
});

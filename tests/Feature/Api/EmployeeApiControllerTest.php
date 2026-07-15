<?php

use App\Models\ApiClient;
use App\Models\Employee;
use App\Models\Employer;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

function employeeApiClientWithAbility(string ...$abilities): ApiClient
{
    $client = ApiClient::query()->create(['name' => 'Employers']);

    Sanctum::actingAs($client, $abilities, 'api-client');

    return $client;
}

test('index never returns bsn and includes a nested address when present', function () {
    employeeApiClientWithAbility('employers:read');

    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'bsn' => '123456789',
    ]);
    $employee->address()->create([
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
    ]);

    $response = $this->getJson("/api/employers/{$employer->id}/employees?tenant_id={$tenantId}");

    $response->assertOk();
    expect($response->json())->each->not->toHaveKey('bsn');
    expect($response->json('0.address.city'))->toBe('Amsterdam');
});

test('store accepts personal data and address fields and masks bsn on the returned employee', function () {
    employeeApiClientWithAbility('employers:read', 'employees:write');

    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);

    $response = $this->postJson("/api/employers/{$employer->id}/employees", [
        'tenant_id' => $tenantId,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'gender' => 'female',
        'bsn' => '123456789',
        'nationality' => 'NLD',
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertCreated();

    $employee = Employee::query()->where('first_name', 'Jane')->firstOrFail();
    expect($employee->bsn)->toBe('123456789')
        ->and($employee->address)->not->toBeNull()
        ->and($employee->address->city)->toBe('Amsterdam');
});

test('update replaces personal data and address and masks bsn on the returned employee', function () {
    employeeApiClientWithAbility('employees:write');

    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'bsn' => '123456789',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response = $this->putJson("/api/employers/{$employer->id}/employees/{$employee->id}", [
        'tenant_id' => $tenantId,
        'first_name' => 'Jane',
        'last_name' => 'Roe',
        'nationality' => 'NLD',
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertOk();
    expect($response->json())->not->toHaveKey('bsn');

    $employee->refresh();
    expect($employee->last_name)->toBe('Roe')
        ->and($employee->nationality)->toBe('NLD')
        ->and($employee->bsn)->toBe('123456789')
        ->and($employee->address)->not->toBeNull()
        ->and($employee->address->city)->toBe('Amsterdam');
});

<?php

use App\Models\Employer;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

test('createFromRow creates an employee with personal data and an address', function () {
    $employer = Employer::query()->create(['tenant_id' => (string) Str::uuid(), 'name' => 'Acme']);

    $employee = app(EmployeeImportService::class)->createFromRow($employer, [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-01-01',
        'gender' => 'female',
        'bsn' => '123456789',
        'nationality' => 'NLD',
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
    ], 'test');

    expect($employee->gender)->toBe('female')
        ->and($employee->bsn)->toBe('123456789')
        ->and($employee->nationality)->toBe('NLD')
        ->and($employee->address)->not->toBeNull()
        ->and($employee->address->address_line_1)->toBe('Kerkstraat 1')
        ->and($employee->address->city)->toBe('Amsterdam');
});

test('createFromRow does not create an address when no address fields are present', function () {
    $employer = Employer::query()->create(['tenant_id' => (string) Str::uuid(), 'name' => 'Acme']);

    $employee = app(EmployeeImportService::class)->createFromRow($employer, [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ], 'test');

    expect($employee->address)->toBeNull();
});

test('createFromRow rejects a postal code without an address line 1', function () {
    $employer = Employer::query()->create(['tenant_id' => (string) Str::uuid(), 'name' => 'Acme']);

    app(EmployeeImportService::class)->createFromRow($employer, [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'postal_code' => '1234AB',
    ], 'test');
})->throws(ValidationException::class);

test('createFromRow rejects an invalid bsn', function () {
    $employer = Employer::query()->create(['tenant_id' => (string) Str::uuid(), 'name' => 'Acme']);

    app(EmployeeImportService::class)->createFromRow($employer, [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'bsn' => '123',
    ], 'test');
})->throws(ValidationException::class);

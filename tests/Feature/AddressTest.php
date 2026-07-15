<?php

use App\Models\Address;
use App\Models\Employee;
use App\Models\Employer;
use Illuminate\Support\Str;

test('an employee has one address via the polymorphic relation', function () {
    $employer = Employer::query()->create(['tenant_id' => (string) Str::uuid(), 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $employer->tenant_id,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ]);

    $employee->address()->create([
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
    ]);

    $fresh = $employee->fresh();

    expect($fresh->address)->not->toBeNull()
        ->and($fresh->address->city)->toBe('Amsterdam')
        ->and($fresh->address->addressable)->toBeInstanceOf(Employee::class)
        ->and($fresh->address->addressable->id)->toBe($employee->id);

    expect(Address::query()->first()->addressable_type)->toBe(Employee::class);
});

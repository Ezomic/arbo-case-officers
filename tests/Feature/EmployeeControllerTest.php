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

test('a user without manage-employees cannot add an employee', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $response = $this->post("/employers/{$employer->id}/employees", [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertForbidden();
});

test('a user with manage-employees can add an employee with personal data and an address', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $response = $this->post("/employers/{$employer->id}/employees", [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.test',
        'date_of_birth' => '1990-01-01',
        'gender' => 'female',
        'bsn' => '123456789',
        'nationality' => 'NLD',
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertRedirect("/employers/{$employer->id}");

    $employee = Employee::query()->where('first_name', 'Jane')->firstOrFail();
    expect($employee->gender)->toBe('female')
        ->and($employee->bsn)->toBe('123456789')
        ->and($employee->nationality)->toBe('NLD')
        ->and($employee->date_of_birth->toDateString())->toBe('1990-01-01')
        ->and($employee->address)->not->toBeNull()
        ->and($employee->address->address_line_1)->toBe('Kerkstraat 1')
        ->and($employee->address->city)->toBe('Amsterdam');
});

test('creating an employee without an address does not create an address row', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $this->post("/employers/{$employer->id}/employees", [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $employee = Employee::query()->where('first_name', 'Jane')->firstOrFail();
    expect($employee->address)->toBeNull();
});

test('bsn must be 9 digits', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);

    $response = $this->post("/employers/{$employer->id}/employees", [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'bsn' => '123',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertSessionHasErrors('bsn');
});

test('a user without manage-employees cannot update an employee', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response = $this->put("/employers/{$employer->id}/employees/{$employee->id}", [
        'first_name' => 'Jane',
        'last_name' => 'Roe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertForbidden();
});

test('a user with manage-employees can update personal data and add an address', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response = $this->put("/employers/{$employer->id}/employees/{$employee->id}", [
        'first_name' => 'Jane',
        'last_name' => 'Roe',
        'gender' => 'female',
        'nationality' => 'NLD',
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    $response->assertRedirect("/employers/{$employer->id}");

    $employee->refresh();
    expect($employee->last_name)->toBe('Roe')
        ->and($employee->gender)->toBe('female')
        ->and($employee->address)->not->toBeNull()
        ->and($employee->address->city)->toBe('Amsterdam');
});

test('removing address fields on update deletes the existing address', function () {
    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage employees',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);
    $employee->address()->create([
        'address_line_1' => 'Kerkstraat 1',
        'postal_code' => '1234AB',
        'city' => 'Amsterdam',
        'country' => 'NL',
    ]);

    $this->put("/employers/{$employer->id}/employees/{$employee->id}", [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'organizational_unit_id' => $employer->defaultOrganizationalUnit()->id,
    ]);

    expect($employee->fresh()->address)->toBeNull();
});

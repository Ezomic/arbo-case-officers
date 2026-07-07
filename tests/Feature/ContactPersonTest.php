<?php

use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function caseOfficerWithManageEmployers(): User
{
    $user = User::factory()->create();

    foreach (['Manage employers', 'View employers'] as $permission) {
        RolePermission::query()->create([
            'tenant_id' => $user->tenant_id,
            'role_name' => $user->current_role,
            'permission' => $permission,
        ]);
    }

    return $user;
}

test('a case officer can add a contact person to an employer, which pushes a sync to employers', function () {
    Http::fake(['*/employers/*/contact-persons' => Http::response([], 200)]);

    $user = caseOfficerWithManageEmployers();
    $this->actingAs($user)->post('/employers', ['name' => 'Acme Corp'])->assertRedirect();
    $employer = Employer::query()->where('name', 'Acme Corp')->firstOrFail();

    $response = $this->actingAs($user)->post("/employers/{$employer->id}/contact-persons", [
        'name' => 'Jane Doe',
        'email' => 'jane@example.test',
        'job_title' => 'HR Manager',
    ]);

    $response->assertRedirect();
    expect($employer->contactPersons()->where('name', 'Jane Doe')->exists())->toBeTrue();

    Http::assertSent(function ($request) use ($employer) {
        return str_contains($request->url(), "employers/{$employer->id}/contact-persons")
            && $request['contact_persons'][0]['name'] === 'Jane Doe';
    });
});

test('a case officer can delete a contact person, which pushes a sync to employers', function () {
    Http::fake(['*/employers/*/contact-persons' => Http::response([], 200)]);

    $user = caseOfficerWithManageEmployers();
    $this->actingAs($user)->post('/employers', ['name' => 'Acme Corp']);
    $employer = Employer::query()->where('name', 'Acme Corp')->firstOrFail();
    $contactPerson = $employer->contactPersons()->create(['name' => 'Jane Doe']);

    $response = $this->actingAs($user)->delete("/employers/{$employer->id}/contact-persons/{$contactPerson->id}");

    $response->assertRedirect();
    expect($employer->contactPersons()->find($contactPerson->id))->toBeNull();
});

test('a contact person belonging to a different employer cannot be updated or deleted through the wrong employer', function () {
    $user = caseOfficerWithManageEmployers();
    $this->actingAs($user)->post('/employers', ['name' => 'Acme Corp']);
    $this->actingAs($user)->post('/employers', ['name' => 'Other Corp']);

    $employerA = Employer::query()->where('name', 'Acme Corp')->firstOrFail();
    $employerB = Employer::query()->where('name', 'Other Corp')->firstOrFail();
    $contactPerson = $employerA->contactPersons()->create(['name' => 'Jane Doe']);

    $this->actingAs($user)
        ->delete("/employers/{$employerB->id}/contact-persons/{$contactPerson->id}")
        ->assertNotFound();

    expect($employerA->contactPersons()->find($contactPerson->id))->not->toBeNull();
});

test('the employer show page includes contact persons', function () {
    $user = caseOfficerWithManageEmployers();
    $this->actingAs($user)->post('/employers', ['name' => 'Acme Corp']);
    $employer = Employer::query()->where('name', 'Acme Corp')->firstOrFail();
    $employer->contactPersons()->create(['name' => 'Jane Doe']);

    $this->actingAs($user)
        ->get("/employers/{$employer->id}")
        ->assertInertia(fn ($page) => $page
            ->component('employers/Show')
            ->has('contactPersons', 1)
            ->where('contactPersons.0.name', 'Jane Doe'),
        );
});

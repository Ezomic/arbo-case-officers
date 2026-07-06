<?php

use App\Models\CaseEvent;
use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

test('assigning an unassigned case records a case_assigned event', function () {
    Http::fake(['*/cases/*' => Http::response([])]);

    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage cases',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $newOfficer = User::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Officer Two', 'email' => 'officer2@acme.test', 'current_role' => 'case_officer']);

    $response = $this->put("/cases/{$case->id}/assignment", ['case_officer_user_id' => $newOfficer->id]);

    $response->assertRedirect(route('cases.show', $case));
    expect($case->fresh()->case_officer_user_id)->toBe($newOfficer->id);

    $event = CaseEvent::query()->where('case_id', $case->id)->where('event', 'case_assigned')->firstOrFail();
    expect($event->actor_name)->toBe($user->name);
});

test('reassigning a case records a case_handed_over event', function () {
    Http::fake(['*/cases/*' => Http::response([])]);

    $user = User::factory()->create();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage cases',
    ]);
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $firstOfficer = User::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Officer One', 'email' => 'officer1@acme.test', 'current_role' => 'case_officer']);
    $case = CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
        'case_officer_user_id' => $firstOfficer->id,
    ]);

    $secondOfficer = User::query()->create(['id' => (string) Str::uuid(), 'tenant_id' => $user->tenant_id, 'name' => 'Officer Two', 'email' => 'officer2@acme.test', 'current_role' => 'case_officer']);

    $this->put("/cases/{$case->id}/assignment", ['case_officer_user_id' => $secondOfficer->id])
        ->assertRedirect(route('cases.show', $case));

    expect(CaseEvent::query()->where('case_id', $case->id)->where('event', 'case_handed_over')->exists())->toBeTrue();
});

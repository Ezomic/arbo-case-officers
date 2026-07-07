<?php

use App\Models\CaseFile;
use App\Models\CaseTask;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\RolePermission;
use App\Models\User;

function caseWithoutPermissionSetup(): CaseFile
{
    $user = User::factory()->create();
    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    return CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);
}

test('a user without manage-cases cannot create a task', function () {
    $case = caseWithoutPermissionSetup();
    $user = User::factory()->create(['tenant_id' => $case->tenant_id]);
    $this->actingAs($user);

    $response = $this->post("/cases/{$case->id}/tasks", ['title' => 'Follow up']);

    $response->assertForbidden();
});

test('a user without manage-cases cannot update a task', function () {
    $case = caseWithoutPermissionSetup();
    $task = CaseTask::query()->create(['case_id' => $case->id, 'title' => 'Follow up']);
    $user = User::factory()->create(['tenant_id' => $case->tenant_id]);
    $this->actingAs($user);

    $response = $this->put("/cases/{$case->id}/tasks/{$task->id}", ['title' => 'Updated']);

    $response->assertForbidden();
});

test('a user without manage-cases cannot complete a task', function () {
    $case = caseWithoutPermissionSetup();
    $task = CaseTask::query()->create(['case_id' => $case->id, 'title' => 'Follow up']);
    $user = User::factory()->create(['tenant_id' => $case->tenant_id]);
    $this->actingAs($user);

    $response = $this->post("/cases/{$case->id}/tasks/{$task->id}/complete");

    $response->assertForbidden();
});

test('a user without manage-cases cannot delete a task', function () {
    $case = caseWithoutPermissionSetup();
    $task = CaseTask::query()->create(['case_id' => $case->id, 'title' => 'Follow up']);
    $user = User::factory()->create(['tenant_id' => $case->tenant_id]);
    $this->actingAs($user);

    $response = $this->delete("/cases/{$case->id}/tasks/{$task->id}");

    $response->assertForbidden();
});

test('a user with manage-cases can create a task', function () {
    $case = caseWithoutPermissionSetup();
    $user = User::factory()->create(['tenant_id' => $case->tenant_id]);
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'Manage cases',
    ]);
    $this->actingAs($user);

    $response = $this->post("/cases/{$case->id}/tasks", ['title' => 'Follow up']);

    $response->assertRedirect(route('cases.show', $case));
    expect(CaseTask::query()->where('case_id', $case->id)->where('title', 'Follow up')->exists())->toBeTrue();
});

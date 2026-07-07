<?php

use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\NoteType;
use App\Models\RolePermission;
use App\Models\User;

function caseWithWritableNoteType(string $role = 'arbo'): array
{
    $user = User::factory()->create(['current_role' => $role]);
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

    $noteType = new NoteType;
    $noteType->forceFill(['tenant_id' => $user->tenant_id, 'name' => 'General'])->save();
    $noteType->permissions()->create([
        'role' => $role,
        'can_read' => true,
        'can_write' => true,
        'can_update' => true,
        'can_delete' => true,
    ]);

    return [$user, $case, $noteType];
}

test('a user without view-cases cannot add a note even with a writable note type', function () {
    [$user, $case, $noteType] = caseWithWritableNoteType();
    $this->actingAs($user);

    $response = $this->post("/cases/{$case->id}/notes", [
        'note_type_id' => $noteType->id,
        'body' => 'Patient is recovering.',
    ]);

    $response->assertForbidden();
});

test('a user with view-cases and a writable note type can add a note', function () {
    [$user, $case, $noteType] = caseWithWritableNoteType();
    RolePermission::query()->create([
        'tenant_id' => $user->tenant_id,
        'role_name' => $user->current_role,
        'permission' => 'View cases',
    ]);
    $this->actingAs($user);

    $response = $this->post("/cases/{$case->id}/notes", [
        'note_type_id' => $noteType->id,
        'body' => 'Patient is recovering.',
    ]);

    $response->assertRedirect(route('cases.show', $case));
    expect($case->notes()->where('body', 'Patient is recovering.')->exists())->toBeTrue();
});

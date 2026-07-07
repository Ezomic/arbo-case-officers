<?php

use App\Models\CaseFile;
use App\Models\CaseNote;
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

test('a user with can_update permission on the note type can update someone else\'s note', function () {
    [$author, $case, $noteType] = caseWithWritableNoteType();
    $note = CaseNote::query()->create([
        'case_id' => $case->id,
        'note_type_id' => $noteType->id,
        'user_id' => $author->id,
        'body' => 'Original body.',
    ]);

    $editor = User::factory()->create(['tenant_id' => $author->tenant_id, 'current_role' => 'arbo']);
    $this->actingAs($editor);

    $response = $this->put("/cases/{$case->id}/notes/{$note->id}", ['body' => 'Edited body.']);

    $response->assertRedirect(route('cases.show', $case));
    expect($note->fresh()->body)->toBe('Edited body.');
});

test('a user without can_update permission on the note type cannot update someone else\'s note', function () {
    [$author, $case, $noteType] = caseWithWritableNoteType();
    $note = CaseNote::query()->create([
        'case_id' => $case->id,
        'note_type_id' => $noteType->id,
        'user_id' => $author->id,
        'body' => 'Original body.',
    ]);

    $editor = User::factory()->create(['tenant_id' => $author->tenant_id, 'current_role' => 'employer']);
    $this->actingAs($editor);

    $response = $this->put("/cases/{$case->id}/notes/{$note->id}", ['body' => 'Edited body.']);

    $response->assertForbidden();
    expect($note->fresh()->body)->toBe('Original body.');
});

test('the author can always update their own note regardless of note type permissions', function () {
    [$author, $case, $noteType] = caseWithWritableNoteType();
    $noteType->permissions()->update(['can_update' => false]);

    $note = CaseNote::query()->create([
        'case_id' => $case->id,
        'note_type_id' => $noteType->id,
        'user_id' => $author->id,
        'body' => 'Original body.',
    ]);
    $this->actingAs($author);

    $response = $this->put("/cases/{$case->id}/notes/{$note->id}", ['body' => 'Edited by author.']);

    $response->assertRedirect(route('cases.show', $case));
    expect($note->fresh()->body)->toBe('Edited by author.');
});

test('a user with can_delete permission on the note type can delete someone else\'s note', function () {
    [$author, $case, $noteType] = caseWithWritableNoteType();
    $note = CaseNote::query()->create([
        'case_id' => $case->id,
        'note_type_id' => $noteType->id,
        'user_id' => $author->id,
        'body' => 'Original body.',
    ]);

    $deleter = User::factory()->create(['tenant_id' => $author->tenant_id, 'current_role' => 'arbo']);
    $this->actingAs($deleter);

    $response = $this->delete("/cases/{$case->id}/notes/{$note->id}");

    $response->assertRedirect(route('cases.show', $case));
    expect(CaseNote::query()->find($note->id))->toBeNull();
});

test('a user without can_delete permission on the note type cannot delete someone else\'s note', function () {
    [$author, $case, $noteType] = caseWithWritableNoteType();
    $note = CaseNote::query()->create([
        'case_id' => $case->id,
        'note_type_id' => $noteType->id,
        'user_id' => $author->id,
        'body' => 'Original body.',
    ]);

    $deleter = User::factory()->create(['tenant_id' => $author->tenant_id, 'current_role' => 'employer']);
    $this->actingAs($deleter);

    $response = $this->delete("/cases/{$case->id}/notes/{$note->id}");

    $response->assertForbidden();
    expect(CaseNote::query()->find($note->id))->not->toBeNull();
});

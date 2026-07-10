<?php

use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\TaskConditionRun;
use App\Models\TaskType;
use Illuminate\Support\Str;

function seedMatchingCaseAndTaskType(): void
{
    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    CaseFile::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-01-01',
    ]);

    $taskType = new TaskType;
    $taskType->forceFill(['tenant_id' => $tenantId, 'name' => 'Auto task'])->save();
    $taskType->conditions()->create(['type' => 'case_type', 'case_type' => 'verzuim']);
}

test('the command creates a run log with correct counts', function () {
    seedMatchingCaseAndTaskType();

    $this->artisan('task-conditions:evaluate')->assertSuccessful();

    $this->assertDatabaseCount('case_tasks', 1);
    $run = TaskConditionRun::query()->sole();
    expect($run->command)->toBe('task-conditions:evaluate')
        ->and($run->dry_run)->toBeFalse()
        ->and($run->counts)->toBe(['created' => 1, 'skipped_existing' => 0, 'cases_evaluated' => 1])
        ->and($run->finished_at)->not->toBeNull();
});

test('--dry-run creates no tasks but still logs a run', function () {
    seedMatchingCaseAndTaskType();

    $this->artisan('task-conditions:evaluate', ['--dry-run' => true])->assertSuccessful();

    $this->assertDatabaseCount('case_tasks', 0);
    $run = TaskConditionRun::query()->sole();
    expect($run->dry_run)->toBeTrue()
        ->and($run->counts['created'])->toBe(1);
});

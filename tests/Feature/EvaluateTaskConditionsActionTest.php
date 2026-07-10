<?php

use App\Actions\EvaluateTaskConditionsAction;
use App\Models\CaseFile;
use App\Models\CaseTask;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\TaskType;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

function caseWithType(string $tenantId, string $caseType, string $status = 'open', ?string $expectedReturnDate = null, string $openedAt = '2026-01-01'): CaseFile
{
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    return CaseFile::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => $caseType,
        'status' => $status,
        'opened_at' => $openedAt,
        'expected_return_date' => $expectedReturnDate,
    ]);
}

/** @param list<array<string, mixed>> $conditions */
function taskTypeWithConditions(string $tenantId, array $conditions, string $name = 'Auto task'): TaskType
{
    $taskType = new TaskType;
    $taskType->forceFill(['tenant_id' => $tenantId, 'name' => $name])->save();

    foreach ($conditions as $condition) {
        $taskType->conditions()->create($condition);
    }

    return $taskType;
}

afterEach(fn () => Date::setTestNow());

test('creates a task when a case_type condition matches', function () {
    $tenantId = (string) Str::uuid();
    $case = caseWithType($tenantId, 'verzuim');
    $taskType = taskTypeWithConditions($tenantId, [['type' => 'case_type', 'case_type' => 'verzuim']]);

    $result = app(EvaluateTaskConditionsAction::class)->handle();

    expect($result)->toBe(['created' => 1, 'skipped_existing' => 0, 'cases_evaluated' => 1]);
    $this->assertDatabaseHas('case_tasks', ['case_id' => $case->id, 'task_type_id' => $taskType->id, 'title' => 'Auto task']);
});

test('does not create a task when case_type does not match', function () {
    $tenantId = (string) Str::uuid();
    caseWithType($tenantId, 'verzuim');
    taskTypeWithConditions($tenantId, [['type' => 'case_type', 'case_type' => 'pmo']]);

    $result = app(EvaluateTaskConditionsAction::class)->handle();

    expect($result['created'])->toBe(0);
    $this->assertDatabaseCount('case_tasks', 0);
});

test('return_date_overdue condition fires only for a past date on an open case', function () {
    $tenantId = (string) Str::uuid();
    taskTypeWithConditions($tenantId, [['type' => 'return_date_overdue']]);

    Date::setTestNow('2026-02-01');

    $overdue = caseWithType($tenantId, 'verzuim', expectedReturnDate: '2026-01-15');
    caseWithType($tenantId, 'verzuim', expectedReturnDate: '2026-03-01');
    caseWithType($tenantId, 'verzuim', expectedReturnDate: null);

    app(EvaluateTaskConditionsAction::class)->handle();

    $this->assertDatabaseCount('case_tasks', 1);
    $this->assertDatabaseHas('case_tasks', ['case_id' => $overdue->id]);
});

test('milestone_due condition fires once the milestone due date is reached', function () {
    $tenantId = (string) Str::uuid();
    $case = caseWithType($tenantId, 'verzuim', openedAt: '2026-01-01');
    taskTypeWithConditions($tenantId, [['type' => 'milestone_due', 'milestone' => 'probleem_analyse']]);

    Date::setTestNow('2026-02-11');
    app(EvaluateTaskConditionsAction::class)->handle();
    $this->assertDatabaseCount('case_tasks', 0);

    Date::setTestNow('2026-02-12');
    $result = app(EvaluateTaskConditionsAction::class)->handle();
    expect($result['created'])->toBe(1);
    $created = CaseTask::query()->where('case_id', $case->id)->firstOrFail();
    expect($created->due_date->toDateString())->toBe('2026-02-12');
});

test('multiple conditions on a task type are ANDed together', function () {
    $tenantId = (string) Str::uuid();
    taskTypeWithConditions($tenantId, [
        ['type' => 'case_type', 'case_type' => 'verzuim'],
        ['type' => 'milestone_due', 'milestone' => 'probleem_analyse'],
    ]);

    Date::setTestNow('2026-02-12');

    $matches = caseWithType($tenantId, 'verzuim', openedAt: '2026-01-01');
    caseWithType($tenantId, 'pmo', openedAt: '2026-01-01');

    app(EvaluateTaskConditionsAction::class)->handle();

    $this->assertDatabaseCount('case_tasks', 1);
    $this->assertDatabaseHas('case_tasks', ['case_id' => $matches->id]);
});

test('running the action twice does not create a duplicate task', function () {
    $tenantId = (string) Str::uuid();
    caseWithType($tenantId, 'verzuim');
    taskTypeWithConditions($tenantId, [['type' => 'case_type', 'case_type' => 'verzuim']]);

    app(EvaluateTaskConditionsAction::class)->handle();
    $result = app(EvaluateTaskConditionsAction::class)->handle();

    expect($result)->toBe(['created' => 0, 'skipped_existing' => 1, 'cases_evaluated' => 1]);
    $this->assertDatabaseCount('case_tasks', 1);
});

test('a task type with no conditions never auto-creates a task', function () {
    $tenantId = (string) Str::uuid();
    caseWithType($tenantId, 'verzuim');
    $taskType = new TaskType;
    $taskType->forceFill(['tenant_id' => $tenantId, 'name' => 'Manual only'])->save();

    app(EvaluateTaskConditionsAction::class)->handle();

    $this->assertDatabaseCount('case_tasks', 0);
});

test('closed cases are excluded even when conditions would otherwise match', function () {
    $tenantId = (string) Str::uuid();
    caseWithType($tenantId, 'verzuim', status: 'closed');
    taskTypeWithConditions($tenantId, [['type' => 'case_type', 'case_type' => 'verzuim']]);

    $result = app(EvaluateTaskConditionsAction::class)->handle();

    expect($result['cases_evaluated'])->toBe(0);
    $this->assertDatabaseCount('case_tasks', 0);
});

test('dry run reports what would be created without creating anything', function () {
    $tenantId = (string) Str::uuid();
    caseWithType($tenantId, 'verzuim');
    taskTypeWithConditions($tenantId, [['type' => 'case_type', 'case_type' => 'verzuim']]);

    $result = app(EvaluateTaskConditionsAction::class)->handle(dryRun: true);

    expect($result['created'])->toBe(1);
    $this->assertDatabaseCount('case_tasks', 0);
});

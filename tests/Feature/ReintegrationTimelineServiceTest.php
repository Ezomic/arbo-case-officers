<?php

use App\Enums\ReintegrationMilestone;
use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use App\Services\ReintegrationTimelineService;
use Illuminate\Support\Facades\Date;

function caseOfType(string $caseType, string $openedAt, string $status = 'open'): CaseFile
{
    $user = User::factory()->create();
    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    return CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => $caseType,
        'status' => $status,
        'opened_at' => $openedAt,
    ]);
}

test('milestones are computed at the correct week offsets from opened_at', function () {
    $case = caseOfType('verzuim', '2026-01-01');

    $milestones = (new ReintegrationTimelineService)->milestonesFor($case);

    expect($milestones)->toHaveCount(6)
        ->and(collect($milestones)->firstWhere('milestone', 'probleem_analyse')['due_date'])->toBe('2026-02-12')
        ->and(collect($milestones)->firstWhere('milestone', 'plan_van_aanpak')['due_date'])->toBe('2026-02-26')
        ->and(collect($milestones)->firstWhere('milestone', 'uwv_melding')['due_date'])->toBe('2026-10-22')
        ->and(collect($milestones)->firstWhere('milestone', 'eerstejaarsevaluatie')['due_date'])->toBe('2026-12-31')
        ->and(collect($milestones)->firstWhere('milestone', 'wia_voorbereiding')['due_date'])->toBe('2027-09-30')
        ->and(collect($milestones)->firstWhere('milestone', 'wia_aanvraag')['due_date'])->toBe('2027-12-30');
});

test('non-qualifying case types have no milestones', function () {
    $case = caseOfType('pmo', '2026-01-01');

    expect((new ReintegrationTimelineService)->milestonesFor($case))->toBe([]);
});

test('a milestone whose due date has passed on an open case is overdue', function () {
    Date::setTestNow('2026-02-20');
    $case = caseOfType('verzuim', '2026-01-01');

    $probleemAnalyse = collect((new ReintegrationTimelineService)->milestonesFor($case))
        ->firstWhere('milestone', 'probleem_analyse');

    expect($probleemAnalyse['status'])->toBe('overdue');

    Date::setTestNow();
});

test('a milestone whose due date has passed on a closed case is not overdue', function () {
    Date::setTestNow('2026-02-20');
    $case = caseOfType('verzuim', '2026-01-01', status: 'closed');

    $probleemAnalyse = collect((new ReintegrationTimelineService)->milestonesFor($case))
        ->firstWhere('milestone', 'probleem_analyse');

    expect($probleemAnalyse['status'])->not->toBe('overdue');

    Date::setTestNow();
});

test('a milestone within 14 days is due soon', function () {
    Date::setTestNow('2026-02-05');
    $case = caseOfType('verzuim', '2026-01-01');

    $probleemAnalyse = collect((new ReintegrationTimelineService)->milestonesFor($case))
        ->firstWhere('milestone', 'probleem_analyse');

    expect($probleemAnalyse['status'])->toBe('due_soon');

    Date::setTestNow();
});

test('a milestone more than 14 days out is upcoming', function () {
    Date::setTestNow('2026-01-05');
    $case = caseOfType('verzuim', '2026-01-01');

    $probleemAnalyse = collect((new ReintegrationTimelineService)->milestonesFor($case))
        ->firstWhere('milestone', 'probleem_analyse');

    expect($probleemAnalyse['status'])->toBe('upcoming');

    Date::setTestNow();
});

test('isMilestoneDue is true once the due date is reached', function () {
    Date::setTestNow('2026-02-12');
    $case = caseOfType('verzuim', '2026-01-01');

    expect((new ReintegrationTimelineService)->isMilestoneDue($case, ReintegrationMilestone::ProbleemAnalyse))
        ->toBeTrue();

    Date::setTestNow('2026-02-11');
    expect((new ReintegrationTimelineService)->isMilestoneDue($case, ReintegrationMilestone::ProbleemAnalyse))
        ->toBeFalse();

    Date::setTestNow();
});

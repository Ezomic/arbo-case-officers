<?php

use App\Actions\SendReminderDigestsAction;
use App\Mail\ReminderDigestMail;
use App\Models\CaseFile;
use App\Models\CaseTask;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

function caseWithOfficer(string $tenantId, ?User $officer, string $caseType = 'verzuim', string $openedAt = '2026-01-01', string $status = 'open'): CaseFile
{
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);

    return CaseFile::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_officer_user_id' => $officer?->id,
        'case_type' => $caseType,
        'status' => $status,
        'opened_at' => $openedAt,
    ]);
}

afterEach(fn () => Date::setTestNow());

test('a case with an overdue milestone and an assigned officer sends one digest', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    caseWithOfficer($tenantId, $officer, openedAt: '2026-01-01');

    Date::setTestNow('2026-02-20');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['officers_notified'])->toBe(1)
        ->and($result['milestones_included'])->toBeGreaterThan(0)
        ->and($result['cases_evaluated'])->toBe(1);

    Mail::assertSent(ReminderDigestMail::class, fn (ReminderDigestMail $mail) => $mail->hasTo($officer->email) && count($mail->milestones) > 0);
});

test('a case with no assigned officer produces no email', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    caseWithOfficer($tenantId, null, openedAt: '2026-01-01');

    Date::setTestNow('2026-02-20');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['officers_notified'])->toBe(0);
    Mail::assertNothingSent();
});

test('an officer with at-risk items across two cases gets exactly one digest', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    caseWithOfficer($tenantId, $officer, openedAt: '2026-01-01');
    caseWithOfficer($tenantId, $officer, openedAt: '2026-01-01');

    Date::setTestNow('2026-02-20');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['officers_notified'])->toBe(1)
        ->and($result['cases_evaluated'])->toBe(2);
    Mail::assertSent(ReminderDigestMail::class, 1);
});

test('an overdue case task is included and emailed to the assigned officer', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    // pmo has no reintegration milestones, isolating this test to task-only behaviour
    $case = caseWithOfficer($tenantId, $officer, caseType: 'pmo', openedAt: '2026-01-01');
    CaseTask::query()->create([
        'case_id' => $case->id,
        'title' => 'Verzamel documenten',
        'due_date' => '2026-01-10',
    ]);

    Date::setTestNow('2026-02-01');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['tasks_included'])->toBe(1)
        ->and($result['milestones_included'])->toBe(0);
    Mail::assertSent(ReminderDigestMail::class, fn (ReminderDigestMail $mail) => count($mail->tasks) === 1);
});

test('a completed task is not treated as overdue', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    $case = caseWithOfficer($tenantId, $officer, caseType: 'pmo', openedAt: '2026-01-01');
    CaseTask::query()->create([
        'case_id' => $case->id,
        'title' => 'Verzamel documenten',
        'due_date' => '2026-01-10',
        'completed_at' => '2026-01-05',
    ]);

    Date::setTestNow('2026-02-01');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['tasks_included'])->toBe(0);
    Mail::assertNothingSent();
});

test('dry run sends no emails but still reports correct counts', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    caseWithOfficer($tenantId, $officer, openedAt: '2026-01-01');

    Date::setTestNow('2026-02-20');
    $result = app(SendReminderDigestsAction::class)->handle(dryRun: true);

    expect($result['officers_notified'])->toBe(1);
    Mail::assertNothingSent();
});

test('closed cases are excluded even with an at-risk milestone date', function () {
    Mail::fake();
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    caseWithOfficer($tenantId, $officer, openedAt: '2026-01-01', status: 'closed');

    Date::setTestNow('2026-02-20');
    $result = app(SendReminderDigestsAction::class)->handle();

    expect($result['cases_evaluated'])->toBe(0);
    Mail::assertNothingSent();
});

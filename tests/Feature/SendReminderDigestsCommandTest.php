<?php

use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\ReminderRun;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

function seedCaseWithOverdueMilestone(): void
{
    $tenantId = (string) Str::uuid();
    $officer = User::factory()->create(['tenant_id' => $tenantId]);
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    CaseFile::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_officer_user_id' => $officer->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-01-01',
    ]);
}

afterEach(fn () => Date::setTestNow());

test('the command creates a run log with correct counts', function () {
    Mail::fake();
    seedCaseWithOverdueMilestone();
    Date::setTestNow('2026-02-20');

    $this->artisan('reminders:send')->assertSuccessful();

    $run = ReminderRun::query()->sole();
    expect($run->command)->toBe('reminders:send')
        ->and($run->dry_run)->toBeFalse()
        ->and($run->counts['officers_notified'])->toBe(1)
        ->and($run->counts['cases_evaluated'])->toBe(1)
        ->and($run->finished_at)->not->toBeNull();
});

test('--dry-run sends no emails but still logs a run', function () {
    Mail::fake();
    seedCaseWithOverdueMilestone();
    Date::setTestNow('2026-02-20');

    $this->artisan('reminders:send', ['--dry-run' => true])->assertSuccessful();

    Mail::assertNothingSent();
    $run = ReminderRun::query()->sole();
    expect($run->dry_run)->toBeTrue()
        ->and($run->counts['officers_notified'])->toBe(1);
});

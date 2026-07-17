<?php

use App\Mail\ReminderDigestMail;
use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

function seedMilestoneItem(): array
{
    $tenantId = (string) Str::uuid();
    $employer = Employer::query()->create(['tenant_id' => $tenantId, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $tenantId, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    $case = CaseFile::query()->create([
        'tenant_id' => $tenantId,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-01-01',
    ]);

    return [[
        'case_id' => $case->id,
        'employee_name' => 'Jane Doe',
        'case_type_label' => $case->case_type->label(),
        'milestone_label' => 'Probleemanalyse',
        'due_date' => '2026-02-12',
        'status' => 'overdue',
    ]];
}

test('the digest is rendered in Dutch for a preferred_locale of nl', function () {
    $officer = User::factory()->make(['name' => 'Jan Jansen', 'preferred_locale' => 'nl']);
    $mail = new ReminderDigestMail($officer, seedMilestoneItem(), []);

    $html = $mail->locale('nl')->render();

    expect($html)->toContain('Re-integratiemijlpalen')
        ->and($html)->toContain('te laat');
});

test('the digest is rendered in English for a preferred_locale of en', function () {
    $officer = User::factory()->make(['name' => 'Jan Jansen', 'preferred_locale' => 'en']);
    $mail = new ReminderDigestMail($officer, seedMilestoneItem(), []);

    $html = $mail->locale('en')->render();

    expect($html)->toContain('Reintegration milestones')
        ->and($html)->toContain('overdue');
});

test('the subject line is localized', function () {
    $officer = User::factory()->make();
    $mail = new ReminderDigestMail($officer, [], []);

    App::setLocale('nl');
    expect($mail->envelope()->subject)->toBe('Dagelijks overzicht: aandachtspunten in uw dossiers');

    App::setLocale('en');
    expect($mail->envelope()->subject)->toBe('Daily digest: items needing attention in your cases');
});

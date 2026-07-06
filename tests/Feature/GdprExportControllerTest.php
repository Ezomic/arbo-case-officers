<?php

use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;

test('the gdpr export includes case history but never medical detail', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $employer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Acme']);
    $employee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $employer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $employer->id,
        'employee_id' => $employee->id,
        'case_type' => 'verzuim',
        'status' => 'closed',
        'opened_at' => '2026-06-01',
        'closed_at' => '2026-06-20',
        'advice' => 'Gradual return to work.',
        'restrictions' => 'No heavy lifting.',
    ]);

    $response = $this->get("/employees/{$employee->id}/gdpr-export");

    $response->assertOk();
    $payload = json_decode($response->getContent(), true);

    expect($payload)->not->toBeNull()
        ->and($payload['employee']['first_name'])->toBe('Jane')
        ->and($payload['cases'])->toHaveCount(1)
        ->and($payload['cases'][0]['advice'])->toBe('Gradual return to work.')
        ->and($payload['cases'][0])->not->toHaveKey('diagnosis_notes');
});

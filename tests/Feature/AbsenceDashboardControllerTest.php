<?php

use App\Models\CaseFile;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Support\Str;

test('dashboard stats only include cases from the acting tenant', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $ownEmployer = Employer::query()->create(['tenant_id' => $user->tenant_id, 'name' => 'Own Co']);
    $ownEmployee = Employee::query()->create(['tenant_id' => $user->tenant_id, 'employer_id' => $ownEmployer->id, 'first_name' => 'Jane', 'last_name' => 'Doe']);
    CaseFile::query()->create([
        'tenant_id' => $user->tenant_id,
        'employer_id' => $ownEmployer->id,
        'employee_id' => $ownEmployee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $otherTenantId = (string) Str::uuid();
    $otherEmployer = Employer::withoutGlobalScope('tenant')->create(['tenant_id' => $otherTenantId, 'name' => 'Other Co']);
    $otherEmployee = Employee::withoutGlobalScope('tenant')->create(['tenant_id' => $otherTenantId, 'employer_id' => $otherEmployer->id, 'first_name' => 'Bob', 'last_name' => 'Other']);
    CaseFile::withoutGlobalScope('tenant')->create([
        'tenant_id' => $otherTenantId,
        'employer_id' => $otherEmployer->id,
        'employee_id' => $otherEmployee->id,
        'case_type' => 'verzuim',
        'status' => 'open',
        'opened_at' => '2026-07-01',
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertInertia(fn ($page) => $page
        ->component('absence-dashboard/Index')
        ->where('stats.open_cases', 1)
    );
});

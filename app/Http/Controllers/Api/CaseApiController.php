<?php

namespace App\Http\Controllers\Api;

use App\Enums\CaseType;
use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\Employee;
use App\Services\CaseEventService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CaseApiController extends Controller
{
    /**
     * Employers registers the start of an absence course for one of its
     * own employees — the employer is derived from the employee, not
     * chosen separately, same as the web UI in Case Officers itself. No
     * case_officer_user_id yet: nobody's picked it up on this side.
     */
    public function store(Request $request): CaseFile
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'employee_id' => ['required', 'uuid'],
            'start_date' => ['required', 'date'],
        ]);

        $employee = Employee::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail((string) $data['employee_id']);

        return CaseFile::query()->create([
            'tenant_id' => $data['tenant_id'],
            'employer_id' => $employee->employer_id,
            'employee_id' => $employee->id,
            'case_type' => CaseType::Verzuim,
            'opened_at' => $data['start_date'],
        ]);
    }

    /**
     * Open cases for a tenant — Doctors uses this to pick which case a
     * medical file belongs to. Only structured, non-medical fields
     * (advice/restrictions/expected_return_date) are exposed here; the
     * actual medical detail never leaves the Doctors app.
     */
    /**
     * @return Collection<int, CaseFile>
     */
    public function index(Request $request): Collection
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        return CaseFile::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->with(['employer:id,name', 'employee:id,first_name,last_name'])
            ->oldest('opened_at')
            ->get();
    }

    public function show(Request $request, string $case): CaseFile
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        return CaseFile::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->with(['employer:id,name', 'employee:id,first_name,last_name'])
            ->findOrFail($case);
    }

    public function mutate(Request $request, string $case): CaseFile
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'expected_return_date' => ['nullable', 'date'],
        ]);

        $caseFile = CaseFile::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->where('status', 'open')
            ->findOrFail($case);

        $caseFile->update([
            'expected_return_date' => $data['expected_return_date'] ?? null,
        ]);

        return $caseFile;
    }

    public function close(Request $request, string $case): CaseFile
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'recovery_date' => ['required', 'date'],
        ]);

        $caseFile = CaseFile::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->where('status', 'open')
            ->findOrFail($case);

        $caseFile->update([
            'status' => 'closed',
            'closed_at' => $data['recovery_date'],
        ]);

        return $caseFile;
    }

    public function sync(Request $request, string $case): Response
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'employee_id' => ['required', 'uuid'],
            'case_type' => ['nullable', Rule::enum(CaseType::class)],
            'status' => ['required', 'string'],
            'opened_at' => ['required', 'date'],
            'expected_return_date' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
        ]);

        $employee = Employee::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($data['employee_id']);

        CaseFile::withoutGlobalScope('tenant')->updateOrCreate(
            ['id' => $case],
            [
                'tenant_id' => $data['tenant_id'],
                'employer_id' => $employee->employer_id,
                'employee_id' => $data['employee_id'],
                'case_type' => $data['case_type'] ?? CaseType::Verzuim->value,
                'status' => $data['status'],
                'opened_at' => $data['opened_at'],
                'expected_return_date' => $data['expected_return_date'],
                'closed_at' => $data['closed_at'],
            ],
        );

        return response()->noContent();
    }

    public function update(Request $request, string $case, CaseEventService $events): CaseFile
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'advice' => ['nullable', 'string'],
            'restrictions' => ['nullable', 'string'],
            'expected_return_date' => ['nullable', 'date'],
        ]);

        $caseFile = CaseFile::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($case);

        $caseFile->update([
            'advice' => $data['advice'] ?? null,
            'restrictions' => $data['restrictions'] ?? null,
            'expected_return_date' => $data['expected_return_date'] ?? null,
        ]);

        $events->outcomeShared($caseFile->fresh());

        return $caseFile;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employer;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    public function index(Request $request, string $employer): Collection
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);

        return $employerModel->employees()->withoutGlobalScope('tenant')->get();
    }

    public function store(Request $request, string $employer, EmployeeImportService $service): Employee
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'organizational_unit_id' => ['required', 'uuid', 'exists:organizational_units,id'],
        ]);

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($employer);

        return $service->createFromRow($employerModel, $data, 'employers');
    }
}

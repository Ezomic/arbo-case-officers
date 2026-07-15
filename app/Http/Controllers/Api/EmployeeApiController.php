<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employer;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeApiController extends Controller
{
    /**
     * @return Collection<int, Employee>
     */
    public function index(Request $request, string $employer): Collection
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);

        return $employerModel->employees()
            ->withoutGlobalScope('tenant')
            ->with('address')
            ->get()
            ->makeHidden('bsn');
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
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'bsn' => ['nullable', 'digits:9'],
            'nationality' => ['nullable', 'string', 'size:3'],
            'address_line_1' => ['nullable', 'string', 'max:255', 'required_with:postal_code,city'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'required_with:address_line_1'],
            'city' => ['nullable', 'string', 'max:255', 'required_with:address_line_1'],
            'country' => ['nullable', 'string', 'size:2'],
            'organizational_unit_id' => ['required', 'uuid', 'exists:organizational_units,id'],
        ]);

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($employer);

        return $service->createFromRow($employerModel, $data, 'employers');
    }

    public function update(Request $request, string $employer, string $employee): Employee
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'nationality' => ['nullable', 'string', 'size:3'],
            'address_line_1' => ['nullable', 'string', 'max:255', 'required_with:postal_code,city'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'required_with:address_line_1'],
            'city' => ['nullable', 'string', 'max:255', 'required_with:address_line_1'],
            'country' => ['nullable', 'string', 'size:2'],
            'organizational_unit_id' => ['required', 'uuid', 'exists:organizational_units,id'],
        ]);

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($employer);

        $employeeModel = $employerModel->employees()
            ->withoutGlobalScope('tenant')
            ->findOrFail($employee);

        $addressFields = array_intersect_key($data, array_flip(['address_line_1', 'address_line_2', 'postal_code', 'city', 'country']));
        $employeeFields = array_diff_key($data, $addressFields, array_flip(['tenant_id']));

        $employeeModel->update($employeeFields);

        if (($addressFields['address_line_1'] ?? null) !== null) {
            $employeeModel->address()->updateOrCreate([], $addressFields);
        } else {
            $employeeModel->address()->delete();
        }

        return $employeeModel->refresh()->load('address')->makeHidden('bsn');
    }
}

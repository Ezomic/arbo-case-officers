<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Employer;
use App\Models\OrganizationalUnit;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('employees/Index', [
            'employees' => Employee::query()
                ->with(['employer', 'organizationalUnit'])
                ->latest()
                ->get(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->validate(['q' => ['required', 'string', 'min:3']])['q'];

        $employees = Employee::query()
            ->whereRaw("lower(first_name || ' ' || last_name) like ?", ['%'.strtolower($q).'%'])
            ->with(['organizationalUnit', 'employer'])
            ->limit(15)
            ->get();

        $allUnits = OrganizationalUnit::query()->get()->keyBy('id');

        return response()->json($employees->map(fn (Employee $e) => [
            'id' => $e->id,
            'label' => $this->buildLabel($e, $allUnits),
        ]));
    }

    /** @param Collection<string, OrganizationalUnit> $allUnits */
    private function buildLabel(Employee $employee, Collection $allUnits): string
    {
        $parts = [$employee->first_name.' '.$employee->last_name];

        $unit = $allUnits[$employee->organizational_unit_id] ?? null;
        if ($unit) {
            $parts[] = $unit->name;
            $legalEntity = $this->findLegalEntity($unit->parent_id, $allUnits);
            if ($legalEntity && $legalEntity !== $unit->name) {
                $parts[] = $legalEntity;
            }
        }

        if ($employee->employer) {
            $parts[] = $employee->employer->name;
        }

        return implode(' / ', $parts);
    }

    /** @param Collection<string, OrganizationalUnit> $allUnits */
    private function findLegalEntity(?string $unitId, Collection $allUnits): ?string
    {
        if ($unitId === null) {
            return null;
        }
        $unit = $allUnits[$unitId] ?? null;
        if ($unit === null) {
            return null;
        }
        if ($unit->is_legal_entity) {
            return $unit->name;
        }

        return $this->findLegalEntity($unit->parent_id, $allUnits);
    }

    public function store(Request $request, Employer $employer, EmployeeImportService $service): RedirectResponse
    {
        $this->authorize('manage-employees');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'organizational_unit_id' => ['required', 'uuid', 'exists:organizational_units,id'],
        ]);

        $service->createFromRow($employer, $data, 'case-officers');

        return to_route('employers.show', $employer);
    }
}

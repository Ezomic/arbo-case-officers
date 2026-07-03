<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Employer;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request, Employer $employer, EmployeeImportService $service): RedirectResponse
    {
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

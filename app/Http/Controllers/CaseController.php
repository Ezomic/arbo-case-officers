<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('cases/Index', [
            'cases' => CaseFile::query()
                ->with(['employer', 'employee'])
                ->latest('opened_at')
                ->get(),
            'employees' => Employee::query()
                ->with('employer:id,name')
                ->orderBy('first_name')
                ->get(['id', 'employer_id', 'first_name', 'last_name']),
        ]);
    }

    public function show(CaseFile $case): Response
    {
        return Inertia::render('cases/Show', [
            'case' => $case->load(['employer', 'employee']),
        ]);
    }

    /**
     * A case only ever gets created by registering the start of an
     * absence course — for an employee, not independently of one. The
     * employer is derived from the employee, not chosen separately.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
        ]);

        $employee = Employee::query()->findOrFail((string) $data['employee_id']);

        CaseFile::query()->create([
            'employer_id' => $employee->employer_id,
            'employee_id' => $employee->id,
            'case_type' => 'verzuim',
            'opened_at' => $data['start_date'],
            'tenant_id' => Auth::guard('web')->user()->tenant_id,
            'case_officer_user_id' => Auth::id(),
        ]);

        return to_route('cases.index');
    }
}

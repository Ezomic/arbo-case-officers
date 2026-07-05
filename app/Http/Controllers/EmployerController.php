<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Services\ContractTypeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmployerController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('employers/Index', [
            'employers' => Employer::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kvk_number' => ['nullable', 'string', 'max:255'],
        ]);

        $employer = Employer::query()->create($data);

        return to_route('employers.show', $employer);
    }

    public function show(Employer $employer, ContractTypeSyncService $contractTypeSync): Response
    {
        return Inertia::render('employers/Show', [
            'employer' => $employer,
            'contracts' => $employer->contracts()->latest()->get(),
            'contractTypes' => $contractTypeSync->sync(Auth::user()->tenant_id),
            'organizationalUnits' => $employer->organizationalUnits()->oldest()->get(),
            'employees' => $employer->employees()->with('organizationalUnit')->latest()->get(),
            'contactPersons' => $employer->contactPersons()->oldest()->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * contract_type_id is validated against this tenant's own
     * ContractType shadow (synced from Admin right before this form is
     * shown — see EmployerController::show); contract_type_label is
     * cached from it so this app can display it without another lookup.
     */
    public function store(Request $request, Employer $employer): RedirectResponse
    {
        $data = $request->validate([
            'contract_type_id' => ['required', 'uuid', 'exists:contract_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
        ]);

        $contractType = ContractType::withoutGlobalScope('tenant')->findOrFail((string) $data['contract_type_id']);

        $employer->contracts()->create([
            ...$data,
            'contract_type_label' => $contractType->name,
        ]);

        return to_route('employers.show', $employer);
    }
}

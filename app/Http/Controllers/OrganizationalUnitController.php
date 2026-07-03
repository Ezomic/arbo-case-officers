<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\OrganizationalUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationalUnitController extends Controller
{
    public function store(Request $request, Employer $employer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_legal_entity' => ['boolean'],
            'kvk_number' => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                Rule::exists('organizational_units', 'id')->where('employer_id', $employer->id),
            ],
        ]);

        $employer->organizationalUnits()->create($data);

        return to_route('employers.show', $employer);
    }

    public function edit(Employer $employer, OrganizationalUnit $organizationalUnit): Response
    {
        $excludedIds = $organizationalUnit->selfAndDescendantIds();

        return Inertia::render('organizational-units/Edit', [
            'employer' => $employer,
            'organizationalUnit' => $organizationalUnit,
            'availableParents' => $employer->organizationalUnits()
                ->whereNotIn('id', $excludedIds)
                ->oldest()
                ->get(),
        ]);
    }

    public function update(Request $request, Employer $employer, OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        $excludedIds = $organizationalUnit->selfAndDescendantIds();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_legal_entity' => ['boolean'],
            'kvk_number' => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                Rule::exists('organizational_units', 'id')->where('employer_id', $employer->id),
                Rule::notIn($excludedIds),
            ],
        ]);

        $organizationalUnit->update($data);

        return to_route('employers.show', $employer);
    }
}

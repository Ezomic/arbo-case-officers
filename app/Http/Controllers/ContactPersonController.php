<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\Employer;
use App\Services\EmployersClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactPersonController extends Controller
{
    public function store(Request $request, Employer $employer, EmployersClient $employers): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ]);

        $employer->contactPersons()->create($data);

        $this->syncContactPersons($employers, $employer);

        return back();
    }

    public function update(Request $request, Employer $employer, ContactPerson $contactPerson, EmployersClient $employers): RedirectResponse
    {
        abort_if($contactPerson->employer_id !== $employer->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ]);

        $contactPerson->update($data);

        $this->syncContactPersons($employers, $employer);

        return back();
    }

    public function destroy(Employer $employer, ContactPerson $contactPerson, EmployersClient $employers): RedirectResponse
    {
        abort_if($contactPerson->employer_id !== $employer->id, 404);

        $contactPerson->delete();

        $this->syncContactPersons($employers, $employer);

        return back();
    }

    private function syncContactPersons(EmployersClient $employers, Employer $employer): void
    {
        try {
            $employers->syncContactPersons($employer);
        } catch (\Throwable $e) {
            Log::warning('Employers sync failed for contact persons', [
                'employer_id' => $employer->id,
                'error' => $e->getMessage(),
            ]);
            report($e);
        }
    }
}

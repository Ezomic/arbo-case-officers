<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\User;
use App\Services\CaseEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CaseAssignmentController extends Controller
{
    public function update(Request $request, CaseFile $case, CaseEventService $events): RedirectResponse
    {
        $this->authorize('manage-cases');

        $data = $request->validate([
            'case_officer_user_id' => ['required', 'uuid', 'exists:users,id'],
        ]);

        $actor = Auth::user();
        $newOfficer = User::query()->findOrFail($request->string('case_officer_user_id')->value());

        $previousOfficerId = $case->case_officer_user_id;
        $case->update(['case_officer_user_id' => $newOfficer->id]);

        if ($previousOfficerId === null) {
            $events->caseAssigned($case, $newOfficer, $actor);
        } else {
            $previousOfficer = User::query()->find($previousOfficerId);
            if ($previousOfficer !== null) {
                $events->caseHandedOver($case, $previousOfficer, $newOfficer, $actor);
            } else {
                $events->caseAssigned($case, $newOfficer, $actor);
            }
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Case assigned to {$newOfficer->name}.",
        ]);

        return to_route('cases.show', $case);
    }
}

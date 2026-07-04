<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Employee;
use App\Services\EmployersClient;
use App\Services\NoteTypeSyncService;
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

    public function show(CaseFile $case, NoteTypeSyncService $noteTypeSync): Response
    {
        $user = Auth::user();
        $noteTypes = $noteTypeSync->sync($user->tenant_id);
        $userRole  = $user->current_role ?? '';

        $readableTypeIds = $noteTypes
            ->filter(fn ($nt) => $nt->permissionFor($userRole)?->can_read === true)
            ->pluck('id');

        $writableNoteTypes = $noteTypes
            ->filter(fn ($nt) => $nt->permissionFor($userRole)?->can_write === true)
            ->map(fn ($nt) => ['id' => $nt->id, 'name' => $nt->name]);

        $notes = $case->notes()
            ->with(['noteType:id,name', 'author:id,name'])
            ->where(fn ($q) => $q
                ->whereIn('note_type_id', $readableTypeIds)
                ->orWhere('user_id', $user->id)
            )
            ->latest()
            ->get()
            ->map(fn ($note) => [
                'id'            => $note->id,
                'note_type_id'  => $note->note_type_id,
                'note_type_name'=> $note->noteType->name,
                'body'          => $note->body,
                'author_name'   => $note->author->name,
                'is_mine'       => $note->user_id === $user->id,
                'can_update'    => $note->user_id === $user->id || $note->noteType->permissionFor($userRole)?->can_update === true,
                'can_delete'    => $note->user_id === $user->id || $note->noteType->permissionFor($userRole)?->can_delete === true,
                'created_at'    => $note->created_at,
            ]);

        return Inertia::render('cases/Show', [
            'case'             => $case->load(['employer', 'employee']),
            'notes'            => $notes,
            'writableNoteTypes'=> $writableNoteTypes->values(),
        ]);
    }

    public function store(Request $request, EmployersClient $employers): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
        ]);

        $employee = Employee::query()->findOrFail((string) $data['employee_id']);

        $case = CaseFile::query()->create([
            'employer_id' => $employee->employer_id,
            'employee_id' => $employee->id,
            'case_type' => 'verzuim',
            'opened_at' => $data['start_date'],
            'tenant_id' => Auth::guard('web')->user()->tenant_id,
            'case_officer_user_id' => Auth::id(),
        ]);

        rescue(fn () => $employers->syncCase($case->fresh()));

        return to_route('cases.index');
    }

    public function update(Request $request, CaseFile $case, EmployersClient $employers): RedirectResponse
    {
        $data = $request->validate([
            'expected_return_date' => ['nullable', 'date'],
        ]);

        $case->update($data);

        rescue(fn () => $employers->syncCase($case->fresh()));

        return to_route('cases.show', $case);
    }

    public function close(Request $request, CaseFile $case, EmployersClient $employers): RedirectResponse
    {
        $data = $request->validate([
            'recovery_date' => ['required', 'date'],
        ]);

        $case->update([
            'status' => 'closed',
            'closed_at' => $data['recovery_date'],
        ]);

        rescue(fn () => $employers->syncCase($case->fresh()));

        return to_route('cases.show', $case);
    }
}

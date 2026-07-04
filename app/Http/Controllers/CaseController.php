<?php

namespace App\Http\Controllers;

use App\Enums\CaseType;
use App\Models\CaseEvent;
use App\Models\CaseFile;
use App\Models\Contract;
use App\Models\Employee;
use App\Services\CaseEventService;
use App\Services\EmployersClient;
use App\Services\NoteTypeSyncService;
use App\Services\TaskTypeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function index(): Response
    {
        $employees = Employee::query()
            ->with('employer:id,name')
            ->orderBy('first_name')
            ->get(['id', 'employer_id', 'first_name', 'last_name']);

        // For each employer, resolve the active contract's case type allowlist.
        // An empty array means the contract type has no restrictions — all types allowed.
        $employerIds = $employees->pluck('employer_id')->unique()->all();

        $allowedTypesByEmployer = Contract::query()
            ->whereIn('employer_id', $employerIds)
            ->where('status', 'active')
            ->whereNotNull('contract_type_id')
            ->with('contractType.caseTypes')
            ->get()
            ->groupBy('employer_id')
            ->map(function ($contracts) {
                $contract = $contracts->first();
                $caseTypes = $contract->contractType?->caseTypes ?? collect();

                return $caseTypes->pluck('case_type')->all();
            })
            ->all();

        return Inertia::render('cases/Index', [
            'cases' => CaseFile::query()
                ->with(['employer', 'employee'])
                ->latest('opened_at')
                ->get(),
            'employees' => $employees,
            'caseTypes' => array_map(
                fn (CaseType $t) => ['value' => $t->value, 'label' => $t->label()],
                CaseType::cases(),
            ),
            'allowedTypesByEmployer' => $allowedTypesByEmployer,
        ]);
    }

    public function show(CaseFile $case, NoteTypeSyncService $noteTypeSync, TaskTypeSyncService $taskTypeSync, CaseEventService $events): Response
    {
        $timeline = CaseEvent::query()
            ->where('case_id', $case->id)
            ->oldest('occurred_at')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'event' => $e->event,
                'payload' => $e->payload,
                'actor_name' => $e->actor_name,
                'occurred_at' => $e->occurred_at,
            ]);

        $user = Auth::user();
        $noteTypes = $noteTypeSync->sync($user->tenant_id);
        $userRole = $user->current_role ?? '';

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
                'id' => $note->id,
                'note_type_id' => $note->note_type_id,
                'note_type_name' => $note->noteType->name,
                'body' => $note->body,
                'author_name' => $note->author->name,
                'is_mine' => $note->user_id === $user->id,
                'can_update' => $note->user_id === $user->id || $note->noteType->permissionFor($userRole)?->can_update === true,
                'can_delete' => $note->user_id === $user->id || $note->noteType->permissionFor($userRole)?->can_delete === true,
                'created_at' => $note->created_at,
            ]);

        $taskTypes = $taskTypeSync->sync($user->tenant_id)
            ->map(fn ($tt) => ['id' => $tt->id, 'name' => $tt->name]);

        $tasks = $case->tasks()
            ->with(['taskType:id,name', 'assignedUser:id,name'])
            ->oldest('due_date')
            ->get()
            ->map(fn ($task) => [
                'id' => $task->id,
                'task_type_name' => $task->taskType?->name,
                'title' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date?->toDateString(),
                'completed_at' => $task->completed_at,
                'assigned_to' => $task->assignedUser?->name,
            ]);

        return Inertia::render('cases/Show', [
            'case' => $case->load(['employer', 'employee']),
            'case_type_label' => $case->case_type?->label(),
            'notes' => $notes,
            'writableNoteTypes' => $writableNoteTypes->values(),
            'timeline' => $timeline,
            'tasks' => $tasks,
            'taskTypes' => $taskTypes->values(),
        ]);
    }

    public function store(Request $request, EmployersClient $employers, CaseEventService $events): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'case_type' => ['required', Rule::enum(CaseType::class)],
            'start_date' => ['required', 'date'],
        ]);

        $employee = Employee::query()->findOrFail((string) $data['employee_id']);
        $activeContract = Contract::query()
            ->where('employer_id', $employee->employer_id)
            ->where('status', 'active')
            ->whereNotNull('contract_type_id')
            ->with('contractType.caseTypes')
            ->first();

        $allowedTypes = $activeContract?->contractType?->caseTypes->pluck('case_type')->all() ?? [];

        if (! empty($allowedTypes) && ! in_array($data['case_type'], $allowedTypes, true)) {
            return back()->withErrors(['case_type' => 'This case type is not enabled for this employer\'s contract.']);
        }

        $case = CaseFile::query()->create([
            'employer_id' => $employee->employer_id,
            'employee_id' => $employee->id,
            'case_type' => $data['case_type'],
            'opened_at' => $data['start_date'],
            'tenant_id' => Auth::guard('web')->user()->tenant_id,
            'case_officer_user_id' => Auth::id(),
        ]);

        $fresh = $case->fresh();
        $events->caseOpened($fresh, Auth::user());
        $this->syncToEmployers($employers, $fresh, 'store');

        return to_route('cases.index');
    }

    public function update(Request $request, CaseFile $case, EmployersClient $employers, CaseEventService $events): RedirectResponse
    {
        $data = $request->validate([
            'expected_return_date' => ['nullable', 'date'],
        ]);

        $case->update($data);

        $fresh = $case->fresh();
        if ($fresh->expected_return_date !== null) {
            $events->returnDateSet($fresh, Auth::user());
        }
        $this->syncToEmployers($employers, $fresh, 'update');

        return to_route('cases.show', $case);
    }

    public function close(Request $request, CaseFile $case, EmployersClient $employers, CaseEventService $events): RedirectResponse
    {
        $data = $request->validate([
            'recovery_date' => ['required', 'date'],
        ]);

        $case->update([
            'status' => 'closed',
            'closed_at' => $data['recovery_date'],
        ]);

        $fresh = $case->fresh();
        $events->caseClosed($fresh, Auth::user());
        $this->syncToEmployers($employers, $fresh, 'close');

        return to_route('cases.show', $case);
    }

    private function syncToEmployers(EmployersClient $employers, CaseFile $case, string $action): void
    {
        try {
            $employers->syncCase($case);
        } catch (\Throwable $e) {
            Log::warning('Employers sync failed after case '.$action, [
                'case_id' => $case->id,
                'tenant_id' => $case->tenant_id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            report($e);
        }
    }
}

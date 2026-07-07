<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class CaseTaskController extends Controller
{
    public function store(Request $request, CaseFile $case): RedirectResponse
    {
        $this->authorize('manage-cases');

        $data = $request->validate([
            'task_type_id' => ['nullable', 'uuid', 'exists:task_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        CaseTask::query()->create([
            ...$data,
            'case_id' => $case->id,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Task added.']);

        return to_route('cases.show', $case);
    }

    public function update(Request $request, CaseFile $case, CaseTask $task): RedirectResponse
    {
        $this->authorize('manage-cases');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        $task->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Task updated.']);

        return to_route('cases.show', $case);
    }

    public function complete(CaseFile $case, CaseTask $task): RedirectResponse
    {
        $this->authorize('manage-cases');

        $task->update(['completed_at' => Carbon::now()]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Task completed.']);

        return to_route('cases.show', $case);
    }

    public function destroy(CaseFile $case, CaseTask $task): RedirectResponse
    {
        $this->authorize('manage-cases');

        $task->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Task deleted.']);

        return to_route('cases.show', $case);
    }
}

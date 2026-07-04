<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CaseTaskController extends Controller
{
    public function store(Request $request, CaseFile $case): RedirectResponse
    {
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

        return to_route('cases.show', $case);
    }

    public function update(Request $request, CaseFile $case, CaseTask $task): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'assigned_user_id' => ['nullable', 'uuid', 'exists:users,id'],
        ]);

        $task->update($data);

        return to_route('cases.show', $case);
    }

    public function complete(CaseFile $case, CaseTask $task): RedirectResponse
    {
        $task->update(['completed_at' => Carbon::now()]);

        return to_route('cases.show', $case);
    }

    public function destroy(CaseFile $case, CaseTask $task): RedirectResponse
    {
        $task->delete();

        return to_route('cases.show', $case);
    }
}

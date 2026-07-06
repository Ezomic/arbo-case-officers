<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseNote;
use App\Models\NoteType;
use App\Models\User;
use App\Services\CaseEventService;
use App\Services\NoteTypeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CaseNoteController extends Controller
{
    public function store(Request $request, CaseFile $case, NoteTypeSyncService $noteTypeSync, CaseEventService $events): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $noteTypeSync->sync($user->tenant_id);

        $writableIds = NoteType::query()->writableBy($user)->pluck('id')->all();

        $data = $request->validate([
            'note_type_id' => ['required', 'uuid', 'in:'.implode(',', $writableIds)],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note = CaseNote::query()->create([
            'case_id' => $case->id,
            'note_type_id' => $data['note_type_id'],
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $noteType = NoteType::query()->where('id', $data['note_type_id'])->firstOrFail();
        $events->noteAdded($case->id, $noteType->name, $user);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Note added.']);

        return to_route('cases.show', $case);
    }

    public function update(Request $request, CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note->update(['body' => $data['body']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Note updated.']);

        return to_route('cases.show', $case);
    }

    public function destroy(CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Note deleted.']);

        return to_route('cases.show', $case);
    }
}

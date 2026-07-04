<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseNote;
use App\Models\NoteType;
use App\Services\CaseEventService;
use App\Services\NoteTypeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseNoteController extends Controller
{
    public function store(Request $request, CaseFile $case, NoteTypeSyncService $noteTypeSync, CaseEventService $events): RedirectResponse
    {
        $user = Auth::user();
        $noteTypes = $noteTypeSync->sync($user->tenant_id);
        $writableIds = $noteTypes
            ->filter(fn ($nt) => $nt->permissionFor($user->current_role ?? '')?->can_write === true)
            ->pluck('id')
            ->all();

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

        $noteType = NoteType::query()->find($data['note_type_id']);
        $events->noteAdded($case->id, $noteType?->name ?? 'Note', $user);

        return to_route('cases.show', $case);
    }

    public function update(Request $request, CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note->update(['body' => $data['body']]);

        return to_route('cases.show', $case);
    }

    public function destroy(CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return to_route('cases.show', $case);
    }
}

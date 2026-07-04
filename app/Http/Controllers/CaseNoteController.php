<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseNote;
use App\Models\NoteType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CaseNoteController extends Controller
{
    public function store(Request $request, CaseFile $case): RedirectResponse
    {
        $data = $request->validate([
            'note_type_id' => ['required', 'uuid', Rule::exists('note_types', 'id')],
            'body'         => ['required', 'string', 'max:10000'],
        ]);

        $noteType = NoteType::with('permissions')->findOrFail($data['note_type_id']);

        $this->authorize('create', $noteType);

        $case->notes()->create([
            'note_type_id' => $noteType->id,
            'user_id'      => Auth::id(),
            'body'         => $data['body'],
        ]);

        return to_route('cases.show', $case);
    }

    public function update(Request $request, CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $note->update($data);

        return to_route('cases.show', $case);
    }

    public function destroy(CaseFile $case, CaseNote $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return to_route('cases.show', $case);
    }
}

<?php

namespace App\Policies;

use App\Models\CaseNote;
use App\Models\NoteType;
use App\Models\User;

class CaseNotePolicy
{
    public function create(User $user, NoteType $noteType): bool
    {
        $perm = $noteType->permissionFor($user->current_role ?? '');

        return $perm?->can_write === true;
    }

    public function update(User $user, CaseNote $note): bool
    {
        if ($note->user_id === $user->id) {
            return true;
        }

        $perm = $note->noteType->permissionFor($user->current_role ?? '');

        return $perm?->can_update === true;
    }

    public function delete(User $user, CaseNote $note): bool
    {
        if ($note->user_id === $user->id) {
            return true;
        }

        $perm = $note->noteType->permissionFor($user->current_role ?? '');

        return $perm?->can_delete === true;
    }
}

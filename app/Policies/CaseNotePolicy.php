<?php

namespace App\Policies;

use App\Models\CaseNote;
use App\Models\User;

class CaseNotePolicy
{
    public function update(User $user, CaseNote $note): bool
    {
        return $note->user_id === $user->id
            || $note->noteType->permissionFor($user->current_role ?? '')?->can_update === true;
    }

    public function delete(User $user, CaseNote $note): bool
    {
        return $note->user_id === $user->id
            || $note->noteType->permissionFor($user->current_role ?? '')?->can_delete === true;
    }
}

<?php

namespace App\Services;

use App\Models\CaseEvent;
use App\Models\CaseFile;
use App\Models\User;

class CaseEventService
{
    public function caseOpened(CaseFile $case, User $actor): void
    {
        $this->record($case->id, 'case_opened', [], $actor);
    }

    public function caseClosed(CaseFile $case, User $actor): void
    {
        $this->record($case->id, 'case_closed', [
            'closed_at' => $case->closed_at?->toDateString(),
        ], $actor);
    }

    public function returnDateSet(CaseFile $case, User $actor): void
    {
        $this->record($case->id, 'return_date_set', [
            'return_date' => $case->expected_return_date?->toDateString(),
        ], $actor);
    }

    public function outcomeShared(CaseFile $case): void
    {
        // Actor is the doctors portal (no user context); actor_name describes the source.
        CaseEvent::create([
            'case_id' => $case->id,
            'event' => 'outcome_shared',
            'payload' => array_filter([
                'advice' => $case->advice,
                'restrictions' => $case->restrictions,
                'expected_return_date' => $case->expected_return_date?->toDateString(),
            ]),
            'actor_user_id' => null,
            'actor_name' => 'Company doctor',
        ]);
    }

    public function noteAdded(string $caseId, string $noteTypeName, User $actor): void
    {
        $this->record($caseId, 'note_added', ['note_type' => $noteTypeName], $actor);
    }

    public function caseAssigned(CaseFile $case, User $assignee, User $actor): void
    {
        $this->record($case->id, 'case_assigned', [
            'assigned_to' => $assignee->name,
        ], $actor);
    }

    public function caseHandedOver(CaseFile $case, User $previousOfficer, User $newOfficer, User $actor): void
    {
        $this->record($case->id, 'case_handed_over', [
            'from' => $previousOfficer->name,
            'to' => $newOfficer->name,
        ], $actor);
    }

    private function record(string $caseId, string $event, array $payload, User $actor): void
    {
        CaseEvent::create([
            'case_id' => $caseId,
            'event' => $event,
            'payload' => $payload ?: null,
            'actor_user_id' => $actor->id,
            'actor_name' => $actor->name,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ReintegrationMilestone;
use App\Models\CaseFile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

class ReintegrationTimelineService
{
    /** @return list<array{milestone: string, label: string, due_date: string, status: string}> */
    public function milestonesFor(CaseFile $case): array
    {
        if (! $case->case_type->hasReintegrationMilestones()) {
            return [];
        }

        return array_map(
            fn (ReintegrationMilestone $milestone) => [
                'milestone' => $milestone->value,
                'label' => $milestone->label(),
                'due_date' => $this->dueDate($case, $milestone)->toDateString(),
                'status' => $this->statusFor($case, $milestone),
            ],
            ReintegrationMilestone::cases(),
        );
    }

    public function isMilestoneDue(CaseFile $case, ReintegrationMilestone $milestone): bool
    {
        if (! $case->case_type->hasReintegrationMilestones()) {
            return false;
        }

        return $this->dueDate($case, $milestone)->lte(Date::now()->startOfDay());
    }

    private function dueDate(CaseFile $case, ReintegrationMilestone $milestone): CarbonImmutable
    {
        return $case->opened_at->startOfDay()->addWeeks($milestone->weekOffset());
    }

    private function statusFor(CaseFile $case, ReintegrationMilestone $milestone): string
    {
        $dueDate = $this->dueDate($case, $milestone);
        $today = Date::now()->startOfDay();

        if ($case->status === 'open' && $dueDate->lt($today)) {
            return 'overdue';
        }

        if ($dueDate->diffInDays($today, true) <= 14) {
            return 'due_soon';
        }

        return 'upcoming';
    }
}

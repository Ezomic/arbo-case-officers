<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CaseFile;
use App\Models\CaseTask;
use App\Models\TaskType;
use App\Models\TaskTypeCondition;
use App\Services\ReintegrationTimelineService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection as SupportCollection;

class EvaluateTaskConditionsAction
{
    public function __construct(private readonly ReintegrationTimelineService $timeline) {}

    /** @return array{created: int, skipped_existing: int, cases_evaluated: int} */
    public function handle(bool $dryRun = false): array
    {
        $created = 0;
        $skippedExisting = 0;
        $casesEvaluated = 0;

        $taskTypesByTenant = TaskType::withoutGlobalScope('tenant')
            ->whereHas('conditions')
            ->with('conditions')
            ->get()
            ->groupBy('tenant_id');

        /** @var SupportCollection<int, TaskType> $taskTypes */
        foreach ($taskTypesByTenant as $tenantId => $taskTypes) {
            $openCases = CaseFile::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->where('status', 'open')
                ->get();

            $casesEvaluated += $openCases->count();

            foreach ($openCases as $case) {
                foreach ($taskTypes as $taskType) {
                    if (! $this->conditionsMatch($case, $taskType->conditions)) {
                        continue;
                    }

                    if (CaseTask::query()->where('case_id', $case->id)->where('task_type_id', $taskType->id)->exists()) {
                        $skippedExisting++;

                        continue;
                    }

                    if ($dryRun) {
                        $created++;

                        continue;
                    }

                    try {
                        CaseTask::query()->create([
                            'case_id' => $case->id,
                            'task_type_id' => $taskType->id,
                            'title' => $taskType->name,
                            'due_date' => $this->matchedMilestoneDueDate($case, $taskType->conditions),
                        ]);
                        $created++;
                    } catch (QueryException) {
                        $skippedExisting++;
                    }
                }
            }
        }

        return ['created' => $created, 'skipped_existing' => $skippedExisting, 'cases_evaluated' => $casesEvaluated];
    }

    /** @param Collection<int, TaskTypeCondition> $conditions */
    private function conditionsMatch(CaseFile $case, Collection $conditions): bool
    {
        foreach ($conditions as $condition) {
            if (! $this->conditionMatches($case, $condition)) {
                return false;
            }
        }

        return true;
    }

    private function conditionMatches(CaseFile $case, TaskTypeCondition $condition): bool
    {
        return match ($condition->type) {
            'case_type' => $case->case_type === $condition->case_type,
            'return_date_overdue' => $case->status === 'open'
                && $case->expected_return_date !== null
                && $case->expected_return_date->isPast(),
            'milestone_due' => $condition->milestone !== null
                && $this->timeline->isMilestoneDue($case, $condition->milestone),
            default => false,
        };
    }

    /** @param Collection<int, TaskTypeCondition> $conditions */
    private function matchedMilestoneDueDate(CaseFile $case, Collection $conditions): ?string
    {
        $milestone = $conditions
            ->first(fn (TaskTypeCondition $c) => $c->type === 'milestone_due' && $c->milestone !== null)
            ?->milestone;

        if ($milestone === null) {
            return null;
        }

        $due = collect($this->timeline->milestonesFor($case))->firstWhere('milestone', $milestone->value);

        return $due['due_date'] ?? null;
    }
}

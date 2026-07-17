<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\ReminderDigestMail;
use App\Models\CaseFile;
use App\Models\CaseTask;
use App\Models\User;
use App\Services\ReintegrationTimelineService;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class SendReminderDigestsAction
{
    public function __construct(private readonly ReintegrationTimelineService $timeline) {}

    /** @return array{cases_evaluated: int, officers_notified: int, milestones_included: int, tasks_included: int} */
    public function handle(bool $dryRun = false): array
    {
        $casesEvaluated = 0;
        $milestonesIncluded = 0;
        $tasksIncluded = 0;

        /**
         * @var array<string, array{
         *     officer: User,
         *     milestones: list<array{case_id: string, employee_name: string, case_type_label: string, milestone_label: string, due_date: string, status: string}>,
         *     tasks: list<array{case_id: string, employee_name: string, case_type_label: string, title: string, due_date: string|null}>,
         * }> $byOfficer
         */
        $byOfficer = [];

        $openCasesByTenant = CaseFile::withoutGlobalScope('tenant')
            ->where('status', 'open')
            ->with(['employee', 'assignedOfficer'])
            ->get()
            ->groupBy('tenant_id');

        foreach ($openCasesByTenant as $openCases) {
            $casesEvaluated += $openCases->count();

            foreach ($openCases as $case) {
                if ($case->case_officer_user_id === null || $case->assignedOfficer === null) {
                    continue;
                }

                $milestones = collect($this->timeline->milestonesFor($case))
                    ->whereIn('status', ['overdue', 'due_soon'])
                    ->values();

                $overdueTasks = CaseTask::query()
                    ->where('case_id', $case->id)
                    ->whereNull('completed_at')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', Date::now()->startOfDay())
                    ->get();

                if ($milestones->isEmpty() && $overdueTasks->isEmpty()) {
                    continue;
                }

                $byOfficer[$case->case_officer_user_id] ??= [
                    'officer' => $case->assignedOfficer,
                    'milestones' => [],
                    'tasks' => [],
                ];

                foreach ($milestones as $milestone) {
                    $byOfficer[$case->case_officer_user_id]['milestones'][] = [
                        'case_id' => $case->id,
                        'employee_name' => trim("{$case->employee->first_name} {$case->employee->last_name}"),
                        'case_type_label' => $case->case_type->label(),
                        'milestone_label' => $milestone['label'],
                        'due_date' => $milestone['due_date'],
                        'status' => $milestone['status'],
                    ];
                    $milestonesIncluded++;
                }

                foreach ($overdueTasks as $task) {
                    $byOfficer[$case->case_officer_user_id]['tasks'][] = [
                        'case_id' => $case->id,
                        'employee_name' => trim("{$case->employee->first_name} {$case->employee->last_name}"),
                        'case_type_label' => $case->case_type->label(),
                        'title' => $task->title,
                        'due_date' => $task->due_date?->toDateString(),
                    ];
                    $tasksIncluded++;
                }
            }
        }

        $officersNotified = 0;

        foreach ($byOfficer as $entry) {
            if ($dryRun) {
                $officersNotified++;

                continue;
            }

            $sent = rescue(fn () => Mail::to($entry['officer']->email)
                ->locale($entry['officer']->preferred_locale)
                ->send(new ReminderDigestMail($entry['officer'], $entry['milestones'], $entry['tasks'])), false);

            if ($sent !== false) {
                $officersNotified++;
            }
        }

        return [
            'cases_evaluated' => $casesEvaluated,
            'officers_notified' => $officersNotified,
            'milestones_included' => $milestonesIncluded,
            'tasks_included' => $tasksIncluded,
        ];
    }
}

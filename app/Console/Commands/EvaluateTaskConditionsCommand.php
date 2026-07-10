<?php

namespace App\Console\Commands;

use App\Actions\EvaluateTaskConditionsAction;
use App\Models\TaskConditionRun;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class EvaluateTaskConditionsCommand extends Command
{
    protected $signature = 'task-conditions:evaluate {--dry-run : Preview without creating tasks}';

    protected $description = 'Auto-create dossier tasks for open cases that match a task type\'s conditions';

    public function handle(EvaluateTaskConditionsAction $action): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $startedAt = CarbonImmutable::now();

        $counts = $action->handle($dryRun);

        $this->info("Cases evaluated: {$counts['cases_evaluated']}");
        $this->info("Tasks created: {$counts['created']}");
        $this->info("Skipped (already exists): {$counts['skipped_existing']}");

        TaskConditionRun::create([
            'command' => 'task-conditions:evaluate',
            'dry_run' => $dryRun,
            'counts' => $counts,
            'started_at' => $startedAt,
            'finished_at' => CarbonImmutable::now(),
        ]);

        $this->info($dryRun ? 'Dry run — no tasks created.' : 'Task condition evaluation complete.');

        return self::SUCCESS;
    }
}

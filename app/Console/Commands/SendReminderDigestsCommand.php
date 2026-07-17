<?php

namespace App\Console\Commands;

use App\Actions\SendReminderDigestsAction;
use App\Models\ReminderRun;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SendReminderDigestsCommand extends Command
{
    protected $signature = 'reminders:send {--dry-run : Preview without sending emails}';

    protected $description = 'Email case officers a digest of at-risk reintegration milestones and overdue tasks';

    public function handle(SendReminderDigestsAction $action): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $startedAt = CarbonImmutable::now();

        $counts = $action->handle($dryRun);

        $this->info("Cases evaluated: {$counts['cases_evaluated']}");
        $this->info("Officers notified: {$counts['officers_notified']}");
        $this->info("Milestones included: {$counts['milestones_included']}");
        $this->info("Tasks included: {$counts['tasks_included']}");

        ReminderRun::create([
            'command' => 'reminders:send',
            'dry_run' => $dryRun,
            'counts' => $counts,
            'started_at' => $startedAt,
            'finished_at' => CarbonImmutable::now(),
        ]);

        $this->info($dryRun ? 'Dry run — no emails sent.' : 'Reminder digests sent.');

        return self::SUCCESS;
    }
}

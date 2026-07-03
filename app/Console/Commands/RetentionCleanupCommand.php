<?php

namespace App\Console\Commands;

use App\Models\CaseFile;
use App\Models\Employee;
use Illuminate\Console\Command;

class RetentionCleanupCommand extends Command
{
    protected $signature = 'retention:cleanup {--dry-run : Preview without making changes}';

    protected $description = 'Anonymise records past their AVG retention period (7 years after case closure)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $expiredCases = CaseFile::withoutGlobalScope('tenant')
            ->whereNotNull('closed_at')
            ->where('closed_at', '<', now()->subYears(7))
            ->get();

        $this->info("Cases past 7-year retention: {$expiredCases->count()}");

        if (! $dryRun) {
            foreach ($expiredCases as $case) {
                $case->update(['advice' => null, 'restrictions' => null]);
            }
        }

        $expiredEmployees = Employee::withoutGlobalScope('tenant')
            ->whereDoesntHave('cases', fn ($q) => $q->where('status', 'open'))
            ->whereHas('cases', fn ($q) => $q->where('closed_at', '<', now()->subYears(7)))
            ->whereDoesntHave('cases', fn ($q) => $q->where('closed_at', '>=', now()->subYears(7)))
            ->get();

        $this->info("Employees past retention: {$expiredEmployees->count()}");

        if (! $dryRun) {
            foreach ($expiredEmployees as $employee) {
                $employee->update([
                    'first_name'      => '[VERWIJDERD]',
                    'last_name'       => '[VERWIJDERD]',
                    'email'           => null,
                    'date_of_birth'   => null,
                    'bsn'             => null,
                    'employee_number' => null,
                ]);
            }
            $this->info('Retention cleanup complete.');
        } else {
            $this->warn('Dry run — no changes made.');
        }

        return self::SUCCESS;
    }
}

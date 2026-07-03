<?php

namespace App\Jobs;

use App\Models\EmployeeImport;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessEmployeeImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly EmployeeImport $import) {}

    public function handle(EmployeeImportService $service): void
    {
        $this->import->update(['status' => 'processing']);

        try {
            $service->process($this->import);
        } catch (\Throwable $e) {
            $this->import->update([
                'status' => 'failed',
                'error_log' => [['row' => 0, 'message' => $e->getMessage()]],
            ]);

            throw $e;
        }
    }
}

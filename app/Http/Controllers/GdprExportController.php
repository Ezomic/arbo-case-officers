<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class GdprExportController extends Controller
{
    // AVG Art. 15 (right of access) + Art. 20 (portability) + WGBO Art. 456.
    // Exports the employee's personal data and case history — no medical detail
    // (diagnosis_notes never leaves doctors; this export is from case-officers only).
    public function employee(Employee $employee): Response
    {
        $employee->load(['employer', 'organizationalUnit', 'cases']);

        $payload = [
            'export_generated_at' => CarbonImmutable::now()->toIso8601String(),
            'legal_basis' => 'AVG Art. 15 (right of access) / Art. 20 (portability)',
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'date_of_birth' => $employee->date_of_birth?->toDateString(),
                'employee_number' => $employee->employee_number,
                'bsn' => $employee->bsn ? '*** (encrypted at rest, not included in export)' : null,
                'status' => $employee->status,
                'employer' => [
                    'name' => $employee->employer?->name,
                ],
                'organizational_unit' => [
                    'name' => $employee->organizationalUnit?->name,
                ],
            ],
            'cases' => $employee->cases->map(fn ($case) => [
                'id' => $case->id,
                'case_type' => $case->case_type,
                'status' => $case->status,
                'opened_at' => $case->opened_at->toDateString(),
                'closed_at' => $case->closed_at?->toDateString(),
                'expected_return_date' => $case->expected_return_date?->toDateString(),
                'advice' => $case->advice,
                'restrictions' => $case->restrictions,
                // diagnosis_notes intentionally absent — never stored here
            ])->values()->all(),
        ];

        $filename = sprintf(
            'gdpr-export-%s-%s.json',
            strtolower($employee->last_name),
            now()->format('Y-m-d'),
        );

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        abort_if($json === false, 500, 'Failed to generate GDPR export.');

        return response(
            $json,
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ],
        );
    }
}

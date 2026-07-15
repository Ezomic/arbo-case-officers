<?php

namespace App\Services\Imports;

use App\Models\Employee;
use App\Models\EmployeeImport;
use App\Models\Employer;
use App\Models\OrganizationalUnit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;

class EmployeeImportService
{
    public function __construct(private readonly XmlEmployeeImportParser $xmlParser) {}

    /**
     * Parse the stored file for the given import and create one Employee
     * per valid row — the single creation path CSV, XLSX, and XML all feed,
     * regardless of which app (Case Officers or Employers) triggered it.
     */
    public function process(EmployeeImport $import): void
    {
        $employer = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $import->tenant_id)
            ->findOrFail($import->employer_id);

        $path = Storage::path($this->storagePath($import));

        $rows = match ($import->file_type) {
            'xml' => $this->xmlParser->parse($path),
            default => $this->readSpreadsheetRows($path),
        };

        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                $this->createFromRow($employer, $row, $import->initiated_by_app);
                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = ['row' => $index + 1, 'message' => $e->getMessage()];
            }
        }

        $import->update([
            'status' => 'completed',
            'total_rows' => count($rows),
            'success_count' => $successCount,
            'error_count' => count($errors),
            'error_log' => $errors,
        ]);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function createFromRow(Employer $employer, array $row, string $source): Employee
    {
        $validated = Validator::make($row, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'bsn' => ['nullable', 'digits:9'],
            'nationality' => ['nullable', 'string', 'size:3'],
            'address_line_1' => ['nullable', 'string', 'max:255', 'required_with:postal_code,city'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'required_with:address_line_1'],
            'city' => ['nullable', 'string', 'max:255', 'required_with:address_line_1'],
            'country' => ['nullable', 'string', 'size:2'],
        ])->validate();

        $addressFields = array_intersect_key($validated, array_flip(['address_line_1', 'address_line_2', 'postal_code', 'city', 'country']));
        $employeeFields = array_diff_key($validated, $addressFields);

        $organizationalUnit = $this->resolveOrganizationalUnit($employer, $row);

        $employee = Employee::query()->create([
            ...$employeeFields,
            'tenant_id' => $employer->tenant_id,
            'employer_id' => $employer->id,
            'organizational_unit_id' => $organizationalUnit->id,
            'source' => "import_{$source}",
        ]);

        if (($addressFields['address_line_1'] ?? null) !== null) {
            $employee->address()->create($addressFields);
        }

        return $employee;
    }

    public function storagePath(EmployeeImport $import): string
    {
        return "employee-imports/{$import->id}.{$import->file_type}";
    }

    /**
     * A row may target a specific unit (by id, or by name for import files
     * where an id isn't practical to fill in by hand); otherwise every
     * employee falls back to the employer's auto-created root unit.
     *
     * @param  array<string, mixed>  $row
     */
    private function resolveOrganizationalUnit(Employer $employer, array $row): OrganizationalUnit
    {
        $unitId = $row['organizational_unit_id'] ?? null;
        $unitName = $row['organizational_unit'] ?? null;

        $query = $employer->organizationalUnits()->withoutGlobalScope('tenant');

        $unit = match (true) {
            $unitId !== null => (clone $query)->find((string) $unitId),
            $unitName !== null => (clone $query)->where('name', $unitName)->first(),
            default => null,
        };

        if ($unit !== null) {
            return $unit;
        }

        if ($unitId !== null || $unitName !== null) {
            throw new RuntimeException("Unknown organizational unit \"{$unitId}{$unitName}\" for this employer.");
        }

        $default = $employer->defaultOrganizationalUnit();

        if ($default === null) {
            throw new RuntimeException('Employer has no organizational unit to default to.');
        }

        return $default;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readSpreadsheetRows(string $path): array
    {
        $import = new EmployeeRowsImport;

        Excel::import($import, $path);

        return $import->rows->all();
    }
}

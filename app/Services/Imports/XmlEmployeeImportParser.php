<?php

namespace App\Services\Imports;

use SimpleXMLElement;

/**
 * Parses a `<employees><employee>...</employee></employees>` document into
 * the same row-array shape EmployeeRowsImport produces from CSV/XLSX, so
 * EmployeeImportService has exactly one creation path regardless of format.
 */
class XmlEmployeeImportParser
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $path): array
    {
        $xml = new SimpleXMLElement(file_get_contents($path));

        $rows = [];

        foreach ($xml->employee as $employee) {
            $rows[] = [
                'first_name' => (string) $employee->first_name,
                'last_name' => (string) $employee->last_name,
                'email' => (string) $employee->email ?: null,
                'employee_number' => (string) $employee->employee_number ?: null,
                'date_of_birth' => (string) $employee->date_of_birth ?: null,
            ];
        }

        return $rows;
    }
}

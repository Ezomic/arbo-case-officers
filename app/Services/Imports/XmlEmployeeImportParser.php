<?php

namespace App\Services\Imports;

use RuntimeException;
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
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read import file at \"{$path}\".");
        }

        $xml = new SimpleXMLElement($contents);

        $rows = [];

        foreach ($xml->employee as $employee) {
            $rows[] = [
                'first_name' => (string) $employee->first_name,
                'last_name' => (string) $employee->last_name,
                'email' => (string) $employee->email ?: null,
                'employee_number' => (string) $employee->employee_number ?: null,
                'date_of_birth' => (string) $employee->date_of_birth ?: null,
                'gender' => (string) $employee->gender ?: null,
                'bsn' => (string) $employee->bsn ?: null,
                'nationality' => (string) $employee->nationality ?: null,
                'address_line_1' => (string) $employee->address_line_1 ?: null,
                'address_line_2' => (string) $employee->address_line_2 ?: null,
                'postal_code' => (string) $employee->postal_code ?: null,
                'city' => (string) $employee->city ?: null,
                'country' => (string) $employee->country ?: null,
            ];
        }

        return $rows;
    }
}

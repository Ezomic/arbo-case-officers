<?php

namespace App\Services\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Reads a CSV/XLSX employee roster into raw rows only — row validation and
 * Employee creation happen in EmployeeImportService, so this class has no
 * domain logic of its own and the XML path can feed the same service.
 */
class EmployeeRowsImport implements ToCollection, WithHeadingRow
{
    public Collection $rows;

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->map(fn (Collection $row) => $row->toArray());
    }
}

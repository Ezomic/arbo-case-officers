<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessEmployeeImportJob;
use App\Models\EmployeeImport;
use App\Models\Employer;
use App\Services\Imports\EmployeeImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeImportApiController extends Controller
{
    /**
     * Accept an uploaded roster file, queue it for processing, and return
     * immediately with an import id to poll — the caller (e.g. the Employers
     * app) never blocks on parsing, which may take a while for large files.
     */
    public function store(Request $request, string $employer, EmployeeImportService $service): EmployeeImport
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'uuid'],
            'initiated_by_user_id' => ['nullable', 'uuid'],
            'initiated_by_app' => ['required', 'in:case-officers,employers'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xml'],
        ]);

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $data['tenant_id'])
            ->findOrFail($employer);

        $import = EmployeeImport::query()->create([
            'tenant_id' => $employerModel->tenant_id,
            'employer_id' => $employerModel->id,
            'initiated_by_user_id' => $data['initiated_by_user_id'] ?? null,
            'initiated_by_app' => $data['initiated_by_app'],
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'status' => 'pending',
        ]);

        Storage::putFileAs('employee-imports', $request->file('file'), "{$import->id}.{$import->file_type}");

        ProcessEmployeeImportJob::dispatch($import);

        return $import;
    }

    public function show(Request $request, EmployeeImport $employeeImport): EmployeeImport
    {
        $request->validate(['tenant_id' => ['required', 'uuid']]);

        abort_unless($employeeImport->tenant_id === $request->string('tenant_id')->value(), 404);

        return $employeeImport;
    }
}

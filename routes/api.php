<?php

use App\Http\Controllers\Api\CaseApiController;
use App\Http\Controllers\Api\ContractApiController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\EmployeeImportApiController;
use App\Http\Controllers\Api\EmployerApiController;
use App\Http\Controllers\Api\OrganizationalUnitApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api-client', 'throttle:api-client'])->group(function () {
    Route::post('cases', [CaseApiController::class, 'store'])
        ->middleware('ability:cases:create');

    Route::get('cases', [CaseApiController::class, 'index'])
        ->middleware('ability:cases:read');

    Route::get('cases/{case}', [CaseApiController::class, 'show'])
        ->middleware('ability:cases:read');

    Route::put('cases/{case}', [CaseApiController::class, 'sync'])
        ->middleware('ability:cases:write');

    Route::patch('cases/{case}', [CaseApiController::class, 'update'])
        ->middleware('ability:cases:write');

    Route::post('cases/{case}/mutate', [CaseApiController::class, 'mutate'])
        ->middleware('ability:cases:write');

    Route::post('cases/{case}/close', [CaseApiController::class, 'close'])
        ->middleware('ability:cases:write');

    Route::get('employers/{employer}', [EmployerApiController::class, 'show'])
        ->middleware('ability:employers:read');

    Route::get('employers/{employer}/contracts', [ContractApiController::class, 'index'])
        ->middleware('ability:employers:read');

    Route::get('employers/{employer}/organizational-units', [OrganizationalUnitApiController::class, 'index'])
        ->middleware('ability:employers:read');

    Route::get('employers/{employer}/employees', [EmployeeApiController::class, 'index'])
        ->middleware('ability:employers:read');

    Route::post('employers/{employer}/employees', [EmployeeApiController::class, 'store'])
        ->middleware('ability:employees:write');

    Route::post('employers/{employer}/employees/import', [EmployeeImportApiController::class, 'store'])
        ->middleware('ability:employees:write');

    Route::get('employee-imports/{employeeImport}', [EmployeeImportApiController::class, 'show'])
        ->middleware('ability:employees:write');
});

<?php

use App\Http\Controllers\CaseController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\OrganizationalUnitController;
use Illuminate\Support\Facades\Route;
use RobbinThijssen\IdentitySsoKit\Http\Controllers\LogoutController;
use RobbinThijssen\IdentitySsoKit\Http\Controllers\RedirectToIdentityController;
use RobbinThijssen\IdentitySsoKit\Http\Controllers\SsoCallbackController;

Route::get('login', RedirectToIdentityController::class)->name('login');
Route::get('sso/callback', SsoCallbackController::class)->name('sso.callback');
Route::post('logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/employers')->name('home');
    Route::redirect('dashboard', '/employers')->name('dashboard');

    Route::get('employers', [EmployerController::class, 'index'])->name('employers.index');
    Route::post('employers', [EmployerController::class, 'store'])->name('employers.store');
    Route::get('employers/{employer}', [EmployerController::class, 'show'])->name('employers.show');

    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');

    Route::get('cases', [CaseController::class, 'index'])->name('cases.index');
    Route::post('cases', [CaseController::class, 'store'])->name('cases.store');
    Route::get('cases/{case}', [CaseController::class, 'show'])->name('cases.show');

    Route::post('employers/{employer}/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::post('employers/{employer}/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('employers/{employer}/organizational-units', [OrganizationalUnitController::class, 'store'])->name('organizational-units.store');
    Route::get('employers/{employer}/organizational-units/{organizationalUnit}/edit', [OrganizationalUnitController::class, 'edit'])->name('organizational-units.edit');
    Route::put('employers/{employer}/organizational-units/{organizationalUnit}', [OrganizationalUnitController::class, 'update'])->name('organizational-units.update');
});

require __DIR__.'/settings.php';

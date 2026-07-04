<?php

use App\Http\Controllers\CaseController;
use App\Http\Controllers\CaseNoteController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\UserController;
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

    Route::post('employers/{employer}/contact-persons', [ContactPersonController::class, 'store'])->name('contact-persons.store');
    Route::put('employers/{employer}/contact-persons/{contactPerson}', [ContactPersonController::class, 'update'])->name('contact-persons.update');
    Route::delete('employers/{employer}/contact-persons/{contactPerson}', [ContactPersonController::class, 'destroy'])->name('contact-persons.destroy');

    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search');

    Route::get('cases', [CaseController::class, 'index'])->name('cases.index');
    Route::post('cases', [CaseController::class, 'store'])->name('cases.store');
    Route::get('cases/{case}', [CaseController::class, 'show'])->name('cases.show');
    Route::put('cases/{case}', [CaseController::class, 'update'])->name('cases.update');
    Route::post('cases/{case}/close', [CaseController::class, 'close'])->name('cases.close');

    Route::post('cases/{case}/notes', [CaseNoteController::class, 'store'])->name('case-notes.store');
    Route::put('cases/{case}/notes/{note}', [CaseNoteController::class, 'update'])->name('case-notes.update');
    Route::delete('cases/{case}/notes/{note}', [CaseNoteController::class, 'destroy'])->name('case-notes.destroy');

    Route::post('employers/{employer}/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::post('employers/{employer}/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('employers/{employer}/organizational-units', [OrganizationalUnitController::class, 'store'])->name('organizational-units.store');
    Route::get('employers/{employer}/organizational-units/{organizationalUnit}/edit', [OrganizationalUnitController::class, 'edit'])->name('organizational-units.edit');
    Route::put('employers/{employer}/organizational-units/{organizationalUnit}', [OrganizationalUnitController::class, 'update'])->name('organizational-units.update');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{uuid}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{uuid}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/settings.php';

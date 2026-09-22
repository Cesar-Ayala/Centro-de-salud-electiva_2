<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorVacationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeVacationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubstitutionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas publicas
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

// Pagina institucional publica del centro de salud
Route::get('inicio', fn () => view('welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Panel principal (el contenido cambia segun el rol)
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
     * Administrador y operador: gestion completa de registros.
     * El borrado definitivo queda reservado al administrador.
     */
    Route::middleware('role:admin,operator')->group(function () {
        Route::resource('doctors', DoctorController::class)->except('destroy');
        Route::resource('employees', EmployeeController::class)->except('destroy');
        Route::resource('patients', PatientController::class)->except('destroy');
        Route::resource('schedules', ScheduleController::class)->except(['show', 'destroy']);
        Route::resource('substitutions', SubstitutionController::class)->except(['show', 'destroy']);
        Route::resource('doctor-vacations', DoctorVacationController::class)->except(['show', 'destroy']);
        Route::resource('employee-vacations', EmployeeVacationController::class)->except(['show', 'destroy']);

        // Reportes en pantalla
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Buscador global (medicos, empleados y pacientes en una sola consulta)
        Route::get('search', [SearchController::class, 'index'])->name('search');

        // Descarga en CSV de cualquiera de los listados, respetando los filtros activos
        Route::get('exports/{resource}', [ExportController::class, 'download'])->name('exports.download');
    });

    // Solo administrador: eliminacion definitiva de registros
    Route::middleware('role:admin')->group(function () {
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
        Route::delete('substitutions/{substitution}', [SubstitutionController::class, 'destroy'])->name('substitutions.destroy');
        Route::delete('doctor-vacations/{doctorVacation}', [DoctorVacationController::class, 'destroy'])->name('doctor-vacations.destroy');
        Route::delete('employee-vacations/{employeeVacation}', [EmployeeVacationController::class, 'destroy'])->name('employee-vacations.destroy');
    });

    // Consultas en formato JSON (utiles para pruebas y sustentacion)
    Route::middleware('role:admin,operator')->prefix('reports')->name('reports.')->group(function () {
        Route::get('active-substitutions', [ReportController::class, 'activeSubstitutions'])->name('active-substitutions');
        Route::get('doctor/{doctorId}/patients', [ReportController::class, 'patientsByDoctor'])->name('doctor-patients');
        Route::get('doctor/{doctorId}/schedule', [ReportController::class, 'doctorSchedule'])->name('doctor-schedule');
        Route::get('planned-vacations', [ReportController::class, 'plannedEmployeeVacations'])->name('planned-vacations');
    });
});

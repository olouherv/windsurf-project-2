<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramYearController;
use App\Http\Controllers\UeController;
use App\Http\Controllers\EcuController;
use App\Http\Controllers\StudentContractController;
use App\Http\Controllers\VacataireContractController;
use App\Http\Controllers\EvaluationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('locale/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return back();
})->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Students
    Route::resource('students', StudentController::class);

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Programs
    Route::resource('programs', ProgramController::class);

    // Program Years (Années de formation)
    Route::get('programs/{program}/years', [ProgramYearController::class, 'index'])->name('programs.years.index');
    Route::get('programs/{program}/years/create', [ProgramYearController::class, 'create'])->name('programs.years.create');
    Route::get('programs/{program}/years/{year}', [ProgramYearController::class, 'show'])->name('programs.years.show');
    Route::get('programs/{program}/years/{year}/edit', [ProgramYearController::class, 'edit'])->name('programs.years.edit');

    // UEs (Unités d'Enseignement)
    Route::get('semesters/{semester}/ues/create', [UeController::class, 'create'])->name('ues.create');
    Route::get('ues/{ue}', [UeController::class, 'show'])->name('ues.show');
    Route::get('ues/{ue}/edit', [UeController::class, 'edit'])->name('ues.edit');
    Route::delete('ues/{ue}', [UeController::class, 'destroy'])->name('ues.destroy');

    // ECUs (Éléments Constitutifs)
    Route::get('ues/{ue}/ecus/create', [EcuController::class, 'create'])->name('ecus.create');
    Route::get('ecus/{ecu}', [EcuController::class, 'show'])->name('ecus.show');
    Route::get('ecus/{ecu}/edit', [EcuController::class, 'edit'])->name('ecus.edit');
    Route::delete('ecus/{ecu}', [EcuController::class, 'destroy'])->name('ecus.destroy');

    // Contrats étudiants
    Route::get('contracts', [StudentContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/create/{student?}', [StudentContractController::class, 'create'])->name('contracts.create');
    Route::get('contracts/{contract}', [StudentContractController::class, 'show'])->name('contracts.show');
    Route::get('contracts/{contract}/edit', [StudentContractController::class, 'edit'])->name('contracts.edit');

    // Contrats vacataires
    Route::get('vacataire-contracts', [VacataireContractController::class, 'index'])->name('vacataire-contracts.index');
    Route::get('vacataire-contracts/create/{teacher?}', [VacataireContractController::class, 'create'])->name('vacataire-contracts.create');
    Route::get('vacataire-contracts/{vacataireContract}', [VacataireContractController::class, 'show'])->name('vacataire-contracts.show');
    Route::get('vacataire-contracts/{vacataireContract}/edit', [VacataireContractController::class, 'edit'])->name('vacataire-contracts.edit');

    // Évaluations et Notes
    Route::get('evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('evaluations/create/{ecu?}', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::get('evaluations/{evaluation}/grades', [EvaluationController::class, 'grades'])->name('evaluations.grades');

    // Schedules (placeholder)
    Route::view('schedules', 'schedules.index')->name('schedules.index');

    // Settings (admin only)
    Route::middleware(['can:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::view('/', 'settings.index')->name('index');
        Route::view('/modules', 'settings.modules')->name('modules');
        Route::view('/moodle', 'settings.moodle')->name('moodle');
    });
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UniversitySubscriptionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramYearController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentContractController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\UeController;
use App\Http\Controllers\VacataireContractController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Admin\PricingPlanController as AdminPricingPlanController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);

Route::get('locale/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return back();
})->name('locale.switch');

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('students-by-program', fn() => view('students.by-program'))->name('students.by-program');

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Programs
    Route::resource('programs', ProgramController::class);

    // Rooms (Salles) - lié au module schedules
    Route::middleware(['module:schedules'])->group(function () {
        Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
        Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');

        // Equipments (Équipements)
        Route::get('equipments', [EquipmentController::class, 'index'])->name('equipments.index');
        Route::get('equipments/create', [EquipmentController::class, 'create'])->name('equipments.create');
        Route::get('equipments/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipments.edit');
    });

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
    Route::middleware(['module:contracts'])->group(function () {
        Route::get('contracts', [StudentContractController::class, 'index'])->name('contracts.index');
        Route::get('contracts/export/csv', [StudentContractController::class, 'exportCsv'])->name('contracts.export.csv');
        Route::get('contracts/create/{student?}', [StudentContractController::class, 'create'])->name('contracts.create');
        Route::get('contracts/{contract}', [StudentContractController::class, 'show'])->name('contracts.show');
        Route::get('contracts/{contract}/pdf', [StudentContractController::class, 'pdf'])->name('contracts.pdf');
        Route::get('contracts/{contract}/edit', [StudentContractController::class, 'edit'])->name('contracts.edit');
    });

    // Contrats vacataires
    Route::middleware(['module:vacataire_contracts'])->group(function () {
        Route::get('vacataire-contracts', [VacataireContractController::class, 'index'])->name('vacataire-contracts.index');
        Route::get('vacataire-contracts/create/{teacher?}', [VacataireContractController::class, 'create'])->name('vacataire-contracts.create');
        Route::get('vacataire-contracts/{vacataireContract}', [VacataireContractController::class, 'show'])->name('vacataire-contracts.show');
        Route::get('vacataire-contracts/{vacataireContract}/edit', [VacataireContractController::class, 'edit'])->name('vacataire-contracts.edit');
    });

    // Évaluations et Notes
    Route::middleware(['module:grades'])->group(function () {
        Route::get('evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('evaluations/create/{ecu?}', [EvaluationController::class, 'create'])->name('evaluations.create');
        Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
        Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
        Route::get('evaluations/{evaluation}/grades', [EvaluationController::class, 'grades'])->name('evaluations.grades');
    });

    // Documents officiels (PDF)
    Route::middleware(['module:documents'])->group(function () {
        Route::get('documents/students/{student}/attestation-inscription', [DocumentsController::class, 'attestationInscription'])
            ->name('documents.students.attestation-inscription');
        Route::get('documents/students/{student}/certificat-scolarite', [DocumentsController::class, 'certificatScolarite'])
            ->name('documents.students.certificat-scolarite');
        Route::get('documents/students/{student}/bulletin-notes/{academicYearId?}', [DocumentsController::class, 'bulletinNotes'])
            ->name('documents.students.bulletin-notes');
        Route::get('documents/payments/{payment}/recu', [DocumentsController::class, 'recuPaiement'])
            ->name('documents.payments.recu');
        Route::get('documents/liste-etudiants', [DocumentsController::class, 'listeEtudiants'])
            ->name('documents.liste-etudiants');
    });

    // Stages & Mémoires
    Route::middleware(['module:stages'])->group(function () {
        Route::resource('internships', InternshipController::class);
        Route::resource('theses', ThesisController::class);
    });

    // Notifications
    Route::middleware(['module:notifications'])->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });

    // Schedules (Planification)
    Route::middleware(['module:schedules'])->group(function () {
        Route::view('schedules', 'schedules.index')->name('schedules.index');
        Route::view('schedules/calendar', 'schedules.calendar')->name('schedules.calendar');
        Route::view('schedules/sessions', 'schedules.sessions')->name('schedules.sessions');
    });

    // Délibérations
    Route::middleware(['module:grades'])->group(function () {
        Route::view('deliberations', 'deliberations.index')->name('deliberations.index');
    });

    // Absences & Présences
    Route::middleware(['module:absences'])->group(function () {
        Route::get('attendance', fn() => view('attendance.index'))->name('attendance.index');
        Route::get('attendance/student/{student}', fn(\App\Models\Student $student) => view('attendance.student', compact('student')))->name('attendance.student');
    });

    // Settings (admin only)
    Route::middleware(['can:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::view('/', 'settings.index')->name('index');
        Route::view('/modules', 'settings.modules')->name('modules');
        Route::view('/moodle', 'settings.moodle')->name('moodle');
        Route::view('/deliberation', 'settings.deliberation')->name('deliberation');

        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::put('/moodle', [SettingsController::class, 'updateMoodle'])->name('moodle.update');
    });

    // Abonnement / Offre (université)
    Route::middleware(['can:admin'])->group(function () {
        Route::get('subscription', [UniversitySubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('subscription/request-change', [UniversitySubscriptionController::class, 'requestChange'])->name('subscription.request-change');
    });

    // Super Admin - Universities overview
    Route::middleware(['can:super_admin'])->group(function () {
        Route::get('admin/universities', [AdminUniversityController::class, 'index'])->name('admin.universities.index');
        Route::post('admin/universities/{university}/modules/{moduleKey}/toggle', [AdminUniversityController::class, 'toggleModule'])
            ->name('admin.universities.modules.toggle');
        Route::post('admin/universities/{university}/plan', [AdminUniversityController::class, 'setPlan'])
            ->name('admin.universities.plan.set');

        Route::get('admin/pricing-plans', [AdminPricingPlanController::class, 'index'])->name('admin.pricing-plans.index');
        Route::get('admin/pricing-plans/create', [AdminPricingPlanController::class, 'create'])->name('admin.pricing-plans.create');
        Route::post('admin/pricing-plans', [AdminPricingPlanController::class, 'store'])->name('admin.pricing-plans.store');
        Route::get('admin/pricing-plans/{pricingPlan}/edit', [AdminPricingPlanController::class, 'edit'])->name('admin.pricing-plans.edit');
        Route::put('admin/pricing-plans/{pricingPlan}', [AdminPricingPlanController::class, 'update'])->name('admin.pricing-plans.update');
        Route::delete('admin/pricing-plans/{pricingPlan}', [AdminPricingPlanController::class, 'destroy'])->name('admin.pricing-plans.destroy');

        // Super Admin - Paiements
        Route::get('admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('admin/payments/{payment}/mark-paid', [AdminPaymentController::class, 'markPaid'])->name('admin.payments.mark-paid');
    });
});

require __DIR__.'/auth.php';

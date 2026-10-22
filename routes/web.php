<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AntecedentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\GynecoObstetricHistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\DischargeRecordController;

use Illuminate\Support\Facades\Route;

/* ===========================
   🔓 RUTAS PÚBLICAS
=========================== */

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.access');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');


/* ===========================
   🔐 RUTAS PROTEGIDAS (AUTH)
=========================== */
Route::middleware(['auth'])->group(function () {

    /* ===============================
       📌 PERFIL (todos los usuarios)
    =============================== */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'passwordForm'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');


    /* ==========================================================
       🟥 ADMINISTRADOR → acceso total incluyendo Usuarios
    =========================================================== */
    Route::middleware(['role:administrador'])->group(function () {

        // Gestión de usuarios
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle', [UserController::class, 'toggle'])->name('toggle');
        });
    });


    /* ===================================================================================
       🟧 ADMINISTRADOR + FISIOTERAPEUTA → Todo excepto usuarios
    =================================================================================== */
    Route::middleware(['role:administrador,fisioterapeuta,interno'])->group(function () {

        // Rutas para ficha de alta
        Route::post('/discharge/store', [DischargeRecordController::class, 'store'])
            ->name('discharge.store');

        Route::put('/discharge/{dischargeRecord}/update', [DischargeRecordController::class, 'update'])
            ->name('discharge.update');


        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Pacientes
        Route::resource('patients', PatientController::class);

        Route::get('/patients/{id}/work', [PatientController::class, 'work'])->name('patients.work');

        // Evaluaciones
        Route::get('/patients/{patient}/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/patients/{patient}/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
        Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
        Route::get('/patients/{patient}/evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
        Route::put('/evaluations/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update');
        Route::delete('/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');

        // Antecedentes
        Route::resource('antecedents', AntecedentController::class);

        // Sesiones terapéuticas
        Route::prefix('patients/{patient}/evaluations/{evaluation}')->group(function () {
            Route::get('therapy-sessions', [TherapySessionController::class, 'index'])->name('evaluations.therapy-sessions.index');
            Route::get('therapy-sessions/create', [TherapySessionController::class, 'create'])->name('evaluations.therapy-sessions.create');
            Route::post('therapy-sessions', [TherapySessionController::class, 'store'])->name('evaluations.therapy-sessions.store');
            Route::get('therapy-sessions/{session}/edit', [TherapySessionController::class, 'edit'])->name('evaluations.therapy-sessions.edit');
            Route::put('therapy-sessions/{session}', [TherapySessionController::class, 'update'])->name('evaluations.therapy-sessions.update');
            Route::delete('therapy-sessions/{session}', [TherapySessionController::class, 'destroy'])->name('evaluations.therapy-sessions.destroy');
        });

        // Historia gineco-obstétrica
        Route::prefix('evaluations/{evaluation}')->group(function () {
            Route::get('gyneco-obstetric/create', [GynecoObstetricHistoryController::class, 'create'])->name('gyneco.create');
            Route::post('gyneco-obstetric', [GynecoObstetricHistoryController::class, 'store'])->name('gyneco.store');
        });

        Route::get('gyneco-obstetric/{gynecoObstetricHistory}/edit', [GynecoObstetricHistoryController::class, 'edit'])->name('gyneco.edit');
        Route::put('gyneco-obstetric/{gynecoObstetricHistory}', [GynecoObstetricHistoryController::class, 'update'])->name('gyneco.update');

        // REPORTES
        Route::get('/reportes/completo', [ReportController::class, 'full'])->name('reports.full');

        Route::get('/reports/evaluations', [ReportController::class, 'evaluations'])->name('reports.evaluations');
        Route::get('/reports/sessions', [ReportController::class, 'sessions'])->name('reports.sessions');

        Route::get('/evaluation/{evaluation}/view', [ReportController::class, 'viewEvaluation'])->name('reports.evaluation.view');
        Route::get('/evaluation/{evaluation}/download', [ReportController::class, 'downloadEvaluation'])->name('reports.evaluation.download');

        Route::get('/session/{session}/view', [ReportController::class, 'viewSession'])->name('reports.session.view');
        Route::get('/session/{session}/download', [ReportController::class, 'downloadSession'])->name('reports.session.download');

        Route::get('/reportes/paciente/{patient}/ver', [ReportController::class, 'viewPatientPdf'])->name('reports.patients.view');
        Route::get('/reportes/paciente/{patient}/descargar', [ReportController::class, 'downloadPatientPdf'])->name('reports.patients.download');

        // Formulario para seleccionar año
        Route::get('/reportes/costos/form', [StatisticController::class, 'costosForm'])->name('statistics.costos.form');
        Route::get('/reportes/costos', [StatisticController::class, 'reporte_costos'])->name('statistics.costos');
        
        Route::get('/reports/diagnosticos', [StatisticController::class, 'diagnosticoMedicoForm'])->name('statistics.diagnosticos.form');
        Route::get('/reports/diagnosticos/pdf', [StatisticController::class, 'diagnosticoMedicoPDF'])->name('statistics.diagnosticos.pdf');
    });



    /* ======================================================
       🟦 INTERNO → SOLO Dashboard, Pacientes (ver), Perfil
    ====================================================== */
    Route::middleware(['role:administrador,fisioterapeuta,interno'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Pacientes (solo ver)
        Route::resource('patients', PatientController::class)->only(['index', 'show']);

        // Perfil ya está incluido arriba
    });
});

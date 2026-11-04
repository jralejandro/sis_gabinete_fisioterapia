<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AntecedentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\GynecoObstetricHistoryController;
use App\Http\Controllers\Api\DischargeRecordController;
use App\Http\Controllers\Api\TherapySessionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\BackupController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        if ($user && $user->estado != 1) {
            $user->currentAccessToken()?->delete();
            return response()->json(['message' => 'Su cuenta ha sido inhabilitada.'], 401);
        }
        return $user;
    });

    // Ruta para revocar el token de sesión actual
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas del CRUD de Usuarios (Listado seguro para selectores; mutaciones exclusivas de Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::middleware('can:gestionar-usuarios')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle']);
    });

    // Rutas del CRUD de Pacientes
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->middleware('can:eliminar-paciente');
    Route::get('/patients/{id}/work', [PatientController::class, 'work']);
    Route::put('/patients/{patient}/antecedents', [AntecedentController::class, 'updateForPatient']);

    // Rutas de Evaluaciones
    Route::post('/patients/{patient}/evaluations', [EvaluationController::class, 'store']);
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show']);
    Route::put('/evaluations/{evaluation}', [EvaluationController::class, 'update']);
    Route::delete('/evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->middleware('can:eliminar-evaluacion');
    // Ruta exclusiva para Antecedentes Gineco-Obstétricos
    Route::put('/evaluations/{evaluation}/gyneco-obstetric', [GynecoObstetricHistoryController::class, 'updateForEvaluation']);
    // Rutas para Sesiones Clínicas
    Route::post('/evaluations/{evaluation}/sessions', [TherapySessionController::class, 'store']);
    Route::put('/sessions/{session}', [TherapySessionController::class, 'update']);
    Route::delete('/sessions/{session}', [TherapySessionController::class, 'destroy'])->middleware('can:eliminar-sesion');

    // Ruta ÚNICA para Ficha de Alta (Crea y Edita)
    Route::post('/discharge-records', [DischargeRecordController::class, 'store']);
    Route::put('/discharge-records/{dischargeRecord}', [DischargeRecordController::class, 'update']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 🔥 Rutas del Perfil
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // 🔒 RUTAS DE REPORTES Y ESTADÍSTICAS BI (Solo Admin y Fisioterapeuta/Kinesiólogo)
    Route::middleware('can:ver-reportes')->prefix('reports')->group(function () {
        Route::get('/patients', [ReportController::class, 'patients']);
        Route::get('/evaluations', [ReportController::class, 'evaluations']);
        Route::get('/sessions', [ReportController::class, 'sessions']);
        Route::get('/patients/{patient}/pdf', [ReportController::class, 'patientPdf']);
        Route::get('/evaluations/{evaluation}/pdf', [ReportController::class, 'evaluationPdf']);
        Route::get('/sessions/{session}/pdf', [ReportController::class, 'sessionPdf']);
        Route::get('/patients/{id}/full', [ReportController::class, 'patientFullData']);
    });

    // 🔒 RESPALDOS (Solo Admin)
    Route::middleware('can:gestionar-backups')->prefix('backups')->group(function () {
        Route::get('/', [BackupController::class, 'index']);
        Route::post('/', [BackupController::class, 'store']);
        Route::get('/{filename}/download', [BackupController::class, 'download']);
        Route::delete('/{filename}', [BackupController::class, 'destroy']);
    });
});

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Evaluation;
use App\Models\TherapySession;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /* ============================================================
     * 1. LISTADOS JSON ULTRA-LIGEROS PARA TABLAS EN REACT
     * ============================================================ */

    /**
     * Listado de pacientes (Solo columnas visibles en ReportesPacientes.jsx)
     */
    public function patients()
    {
        $patients = Patient::select([
            'id',
            'ci',
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'celular',
            'fecha_nacimiento',
            'sexo'
        ])
            ->orderBy('apellido_paterno')
            ->orderBy('nombres')
            ->get();

        return response()->json($patients, 200);
    }

    /**
     * Listado de evaluaciones (Eliminamos la carga innecesaria de therapySessions
     * y excluimos los 30+ campos largos de examen físico)
     */
    public function evaluations()
    {
        $evaluations = Evaluation::select([
            'id',
            'patient_id',
            'fecha',
            'motivo_consulta',
            'diagnostico_medico',
            'estado',
            'costo_evaluacion'
        ])
            ->with([
                // 🔥 AQUÍ ESTABA EL ERROR: Agregamos celular, fecha_nacimiento y sexo
                'patient:id,nombres,apellido_paterno,apellido_materno,ci,celular,fecha_nacimiento,sexo'
            ])
            ->latest('fecha')
            ->get();

        return response()->json($evaluations, 200);
    }

    /**
     * Listado de sesiones (Evitamos traer toda la evaluación completa en cada fila)
     */
    public function sessions()
    {
        $sessions = TherapySession::select([
            'id',
            'evaluation_id',
            'fecha',
            'que_se_trabajo',
            'costo_sesion'
        ])
            ->with([
                'evaluation:id,patient_id,motivo_consulta',
                // 🔥 AGREGAMOS CELULAR, FECHA NACIMIENTO Y SEXO AQUÍ:
                'evaluation.patient:id,nombres,apellido_paterno,apellido_materno,ci,celular,fecha_nacimiento,sexo'
            ])
            ->latest('fecha')
            ->get();

        return response()->json($sessions, 200);
    }

    /* ============================================================
     * 2. GENERADORES DE PDF OPTIMIZADOS
     * ============================================================ */

    /**
     * 📄 PDF de Paciente (Ficha General)
     */
    public function patientPdf(Patient $patient)
    {
        $evaluation = Evaluation::with([
            'therapySessions.responsable:id,nombres,apellido_paterno,apellido_materno,cargo',
            'gynecoObstetricHistory',
            'dischargeRecord'
        ])
            ->where('patient_id', $patient->id)
            ->latest('fecha')
            ->first();

        [$responsableNombre, $responsableCargo] = $this->obtenerDatosResponsable(
            $evaluation?->responsable_evaluacion
        );

        $pdf = Pdf::loadView('reports.patient-pdf', compact(
            'patient',
            'evaluation',
            'responsableNombre',
            'responsableCargo'
        ))->setPaper('letter');

        return $pdf->stream("reporte_paciente_{$patient->ci}.pdf");
    }

    /**
     * 🩺 PDF de Evaluación Clínica
     */
    public function evaluationPdf(Evaluation $evaluation)
    {
        $evaluation->load([
            'patient',
            'therapySessions.responsable:id,nombres,apellido_paterno,apellido_materno,cargo',
            'gynecoObstetricHistory',
            'dischargeRecord'
        ]);

        $patient = $evaluation->patient;

        [$responsableNombre, $responsableCargo] = $this->obtenerDatosResponsable(
            $evaluation->responsable_evaluacion
        );

        $pdf = Pdf::loadView('reports.pdf-evaluation', compact(
            'evaluation',
            'patient',
            'responsableNombre',
            'responsableCargo'
        ))->setPaper('letter');

        return $pdf->stream("evaluacion_{$patient->ci}.pdf");
    }

    /**
     * 👁️ PDF de Sesión Terapéutica Diaria
     */
    public function sessionPdf(TherapySession $session)
    {
        $session->load([
            'evaluation.patient',
            'responsable:id,nombres,apellido_paterno,apellido_materno,cargo'
        ]);

        $patient = $session->evaluation->patient;

        // 1. Responsable de la Evaluación Padre
        [$responsableNombre, $responsableCargo] = $this->obtenerDatosResponsable(
            $session->evaluation?->responsable_evaluacion
        );

        // 2. Responsable de la Sesión Diaria
        $responsableSesionNombre = $session->responsable
            ? trim("{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno}")
            : '—';
        $responsableSesionCargo = $session->responsable->cargo ?? '—';

        $pdf = Pdf::loadView('reports.pdf-session', compact(
            'session',
            'patient',
            'responsableNombre',
            'responsableCargo',
            'responsableSesionNombre',
            'responsableSesionCargo'
        ))->setPaper('letter');

        return $pdf->stream("session_{$session->id}.pdf");
    }

    /* ============================================================
     * 3. HELPER PRIVADO (Evita repetir código en los 3 PDFs)
     * ============================================================ */
    private function obtenerDatosResponsable(?int $userId): array
    {
        if (!$userId) {
            return ['—', '—'];
        }

        $user = User::select('nombres', 'apellido_paterno', 'apellido_materno', 'cargo')
            ->find($userId);

        if (!$user) {
            return ['—', '—'];
        }

        $nombreCompleto = trim("{$user->nombres} {$user->apellido_paterno} {$user->apellido_materno}");
        return [$nombreCompleto, $user->cargo ?? '—'];
    }

    /**
     * 📥 Obtener Expediente Clínico Completo para el PDF del Frontend
     */
    /**
     * 📥 Obtener Expediente Clínico Completo para el PDF del Frontend
     */
    public function patientFullData($id)
    {
        // 1. Cargamos solo las relaciones que SABEMOS que existen en tus modelos
        $patient = Patient::with([
            'antecedent',
            'evaluations' => function ($query) {
                $query->latest('fecha');
            },
            'evaluations.therapySessions' => function ($query) {
                $query->oldest('fecha');
            },
            'evaluations.therapySessions.responsable', // Esta sí existe en tu modelo
            'evaluations.gynecoObstetricHistory',
            'evaluations.dischargeRecord'
        ])->findOrFail($id);

        // 2. Recopilamos todos los IDs de los responsables (Evaluaciones y Altas)
        $userIds = [];
        foreach ($patient->evaluations as $ev) {
            if ($ev->responsable_evaluacion) {
                $userIds[] = $ev->responsable_evaluacion;
            }
            if ($ev->dischargeRecord && $ev->dischargeRecord->responsable_alta) {
                $userIds[] = $ev->dischargeRecord->responsable_alta;
            }
        }

        // 3. Buscamos a todos los usuarios involucrados en 1 sola consulta SQL súper rápida
        $users = User::select('id', 'nombres', 'apellido_paterno', 'apellido_materno', 'cargo')
            ->whereIn('id', array_unique($userIds))
            ->get()
            ->keyBy('id');

        // 4. "Hidratamos" el JSON manualmente pegando los usuarios a cada evaluación
        foreach ($patient->evaluations as $ev) {
            $ev->responsableUser = $ev->responsable_evaluacion ? $users->get($ev->responsable_evaluacion) : null;

            if ($ev->dischargeRecord) {
                $ev->dischargeRecord->responsableUser = $ev->dischargeRecord->responsable_alta
                    ? $users->get($ev->dischargeRecord->responsable_alta)
                    : null;
            }
        }

        return response()->json($patient, 200);
    }
}

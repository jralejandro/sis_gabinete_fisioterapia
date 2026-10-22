<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reporte completo de pacientes
     */

    public function full(Request $request)
    {
        $patients = Patient::all();
        return view('reports.full', compact('patients'));
    }



    /**
     * PDF individual
     */
    public function viewPatientPdf(Patient $patient)
    {
        // Cargar la evaluación junto con las sesiones y sus responsables
        $evaluation = Evaluation::with(['therapySessions.responsable'])
            ->where('patient_id', $patient->id)
            ->first();

        // Variable para enviar al PDF
        $responsableNombre = '—';
        $responsableCargo = '—';

        if ($evaluation) {
            $user = \App\Models\User::find($evaluation->responsable_evaluacion);

            if ($user) {
                $responsableNombre = "{$user->nombres} {$user->apellido_paterno} {$user->apellido_materno}";
                $responsableCargo = $user->cargo ?? '—';
            }
        }

        $pdf = Pdf::loadView('reports.patient-pdf', compact('patient', 'evaluation', 'responsableNombre', 'responsableCargo'));
        return $pdf->stream("reporte_paciente_{$patient->id}.pdf");
    }

    public function downloadPatientPdf(Patient $patient)
    {
        // Cargar la evaluación junto con las sesiones y sus responsables
        $evaluation = Evaluation::with(['therapySessions.responsable'])
            ->where('patient_id', $patient->id)
            ->first();

        // Variable para enviar al PDF
        $responsableNombre = '—';
        $responsableCargo = '—';

        if ($evaluation) {
            $user = \App\Models\User::find($evaluation->responsable_evaluacion);

            if ($user) {
                $responsableNombre = "{$user->nombres} {$user->apellido_paterno} {$user->apellido_materno}";
                $responsableCargo = $user->cargo ?? '—';
            }
        }
        $pdf = Pdf::loadView('reports.patient-pdf', compact('patient','evaluation', 'responsableNombre', 'responsableCargo'));
        return $pdf->download("reporte_paciente_{$patient->id}.pdf");
    }

    public function evaluations()
    {
        // Cargamos todas las evaluaciones con su paciente y sesiones
        $evaluations = \App\Models\Evaluation::with(['patient', 'therapySessions'])
            ->latest('fecha')
            ->get();

        // Renderizamos tu vista personalizada
        return view('reports.report-evaluation', compact('evaluations'));
    }

    /**
     * 🩺 Ver PDF en el navegador con evaluación + sesiones
     */
    public function viewEvaluation(Evaluation $evaluation)
    {
        $evaluation->load(['patient', 'therapySessions.responsable']); // Cargamos relaciones

        // Extraer paciente
        $patient = $evaluation->patient;

        // Responsable
        $responsableNombre = '—';
        $responsableCargo = '—';

        if ($evaluation->responsable_evaluacion) {
            $u = \App\Models\User::find($evaluation->responsable_evaluacion);
            if ($u) {
                $responsableNombre = "{$u->nombres} {$u->apellido_paterno} {$u->apellido_materno}";
                $responsableCargo  = $u->cargo ?? '—';
            }
        }

        // Pasar ambas variables a la vista
        $pdf = Pdf::loadView('reports.pdf-evaluation', compact('evaluation', 'patient', 'responsableNombre', 'responsableCargo'))
            ->setPaper('letter');

        return $pdf->stream('evaluacion_' . $patient->ci . '.pdf');
    }


    /**
     * 📥 Descargar PDF con evaluación + sesiones
     */
    public function downloadEvaluation(Evaluation $evaluation)
    {
        $evaluation->load(['patient', 'therapySessions.responsable']); // Cargamos relaciones

        // Extraemos el paciente
        $patient = $evaluation->patient;

        // Responsable
        $responsableNombre = '—';
        $responsableCargo = '—';

        if ($evaluation->responsable_evaluacion) {
            $u = \App\Models\User::find($evaluation->responsable_evaluacion);
            if ($u) {
                $responsableNombre = "{$u->nombres} {$u->apellido_paterno} {$u->apellido_materno}";
                $responsableCargo  = $u->cargo ?? '—';
            }
        }


        // Enviamos evaluation + patient
        $pdf = Pdf::loadView('reports.pdf-evaluation', compact('evaluation', 'patient', 'responsableNombre', 'responsableCargo'))
            ->setPaper('letter');

        return $pdf->download('evaluacion_' . $patient->ci . '.pdf');
    }


    public function sessions()
    {
        // Cargamos todas las sesiones con su evaluación y paciente
        $sessions = \App\Models\TherapySession::with(['evaluation.patient'])
            ->latest('fecha')
            ->get();

        // Renderizamos la vista correspondiente
        return view('reports.report-session', compact('sessions'));
    }

    /**
     * 👁️ Ver PDF de una sesión en el navegador
     */
    public function viewSession(TherapySession $session)
    {
        // Cargar relaciones
        $session->load(['evaluation.patient', 'responsable']); // 🔹 cargamos responsable de la sesión


        // Extraer paciente
        $patient = $session->evaluation->patient;

        // 🔹 Obtener responsable de la evaluación
        $responsableNombre = '—';
        $responsableCargo  = '—';

        if ($session->evaluation->responsable_evaluacion) {
            $user = \App\Models\User::find($session->evaluation->responsable_evaluacion);
            if ($user) {
                $responsableNombre = "{$user->nombres} {$user->apellido_paterno} {$user->apellido_materno}";
                $responsableCargo  = $user->cargo ?? '—';
            }
        }

        // 🔹 Obtener responsable de la sesión
        $responsableSesionNombre = $session->responsable
            ? "{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno}"
            : '—';
        $responsableSesionCargo  = $session->responsable->cargo ?? '—';

        // Enviar session + patient
        $pdf = Pdf::loadView('reports.pdf-session', compact('session', 'patient', 'responsableNombre', 'responsableCargo', 'responsableSesionNombre', 'responsableSesionCargo'))
            ->setPaper('letter');

        return $pdf->stream('session_' . $session->id . '.pdf');
    }

    /**
     * 📥 Descargar PDF de una sesión
     */
    public function downloadSession(TherapySession $session)
    {
        $session->load(['evaluation.patient', 'responsable']); // 🔹 cargamos responsable de la sesión

        // Extraer paciente
        $patient = $session->evaluation->patient;

        // 🔹 Obtener responsable de la evaluación
        $responsableNombre = '—';
        $responsableCargo  = '—';

        if ($session->evaluation->responsable_evaluacion) {
            $user = \App\Models\User::find($session->evaluation->responsable_evaluacion);
            if ($user) {
                $responsableNombre = "{$user->nombres} {$user->apellido_paterno} {$user->apellido_materno}";
                $responsableCargo  = $user->cargo ?? '—';
            }
        }
        // 🔹 Obtener responsable de la sesión
        $responsableSesionNombre = $session->responsable
            ? "{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno}"
            : '—';
        $responsableSesionCargo  = $session->responsable->cargo ?? '—';

        $pdf = Pdf::loadView('reports.pdf-session', compact('session', 'patient', 'responsableNombre', 'responsableCargo', 'responsableSesionNombre', 'responsableSesionCargo'))
            ->setPaper('letter');

        return $pdf->download('session_' . $session->id . '.pdf');
    }
}

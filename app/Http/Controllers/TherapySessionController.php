<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class TherapySessionController extends Controller
{
    public function index(Patient $patient, Evaluation $evaluation)
    {
        // Cargar relaciones necesarias: sesiones, gineco-obstétrico y discharge record
        $evaluation->load(['therapySessions', 'gynecoObstetricHistory', 'dischargeRecord']);


        // Obtener sesiones ordenadas por fecha descendente
        $sessions = $evaluation->therapySessions()->latest('fecha')->get();

        // Obtener responsables activos con cargo Fisioterapeuta o Interno
        $responsables = User::whereIn('cargo', ['Fisioterapeuta', 'Interno'])
            ->where('estado', 1)
            ->orderBy('nombres')
            ->get();

        // Obtener responsable de la evaluación
        $responsableEvaluacion = User::find($evaluation->responsable_evaluacion);
        $nombreResponsable = $responsableEvaluacion
            ? "{$responsableEvaluacion->nombres} {$responsableEvaluacion->apellido_paterno} {$responsableEvaluacion->apellido_materno} - {$responsableEvaluacion->cargo}"
            : '—';

        $record = $evaluation->dischargeRecord;

        if ($record) {
            $record->load('responsableUser');
        }

        return view('therapy-sessions.index', compact('patient', 'evaluation', 'sessions', 'responsables', 'nombreResponsable', 'record'));
    }





    // Guardar nueva sesión
    public function store(Request $request, Patient $patient, Evaluation $evaluation)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'que_se_trabajo' => 'required|string',
            'evolucion' => 'nullable|string',
            'observaciones' => 'required|string',
            'costo_sesion' => 'required|numeric',
            'responsable_sesion' => 'required|exists:users,id', // ✅ validación del responsable
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('editing', false) // indica que el error pertenece al modal NUEVO
                ->withInput();
        }

        $data = $validator->validated();
        $data['evaluation_id'] = $evaluation->id;
        $data['created_by'] = auth()->id();

        TherapySession::create($data);

        return redirect()->route('evaluations.therapy-sessions.index', compact('patient', 'evaluation'))
            ->with('success', 'Sesión registrada correctamente.');
    }



    // Actualizar sesión
    public function update(Request $request, Patient $patient, Evaluation $evaluation, TherapySession $session)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'que_se_trabajo' => 'required|string',
            'evolucion' => 'nullable|string',
            'costo_sesion' => 'required|numeric',
            'observaciones' => 'required|string',
            'responsable_sesion' => 'required|exists:users,id', // ✅ validación del responsable
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('editing', $session->id) // indica qué modal editar se debe abrir
                ->withInput();
        }

        $session->update($validator->validated());

        return redirect()->route('evaluations.therapy-sessions.index', compact('patient', 'evaluation'))
            ->with('success', 'Sesión actualizada correctamente.');
    }



    // Eliminar sesión
    public function destroy(Patient $patient, Evaluation $evaluation, TherapySession $session)
    {
        $session->delete();

        return redirect()->route('evaluations.therapy-sessions.index', compact('patient', 'evaluation'))
            ->with('success', 'Sesión eliminada correctamente.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Illuminate\Http\Request;

class TherapySessionController extends Controller
{
    // Guardar nueva sesión (API)
    public function store(Request $request, Evaluation $evaluation)
    {
        // 1. Validación con mensajes en español
        $data = $request->validate([
            'fecha'              => 'required|date',
            'responsable_sesion' => 'required|exists:users,id',
            'costo_sesion'       => 'required|numeric',
            'que_se_trabajo'     => 'required|string',
            'evolucion'          => 'required|string', // Cambiado a obligatorio
            'observaciones'      => 'nullable|string', // Cambiado a opcional
        ], [
            'fecha.required'              => 'La fecha de la sesión es obligatoria.',
            'responsable_sesion.required' => 'Debe seleccionar al fisioterapeuta responsable.',
            'costo_sesion.required'       => 'El costo de la sesión es obligatorio.',
            'que_se_trabajo.required'     => 'Debe detallar qué se trabajó en la sesión.',
            'evolucion.required'          => 'La evolución clínica del paciente es obligatoria.',
        ]);

        $data['evaluation_id'] = $evaluation->id;
        $data['created_by']    = auth()->id();

        $session = TherapySession::create($data);

        // 2. Respuesta en JSON para React
        return response()->json([
            'message' => 'Sesión registrada correctamente.',
            'session' => $session
        ], 201);
    }

    // Actualizar sesión (API)
    public function update(Request $request, TherapySession $session)
    {
        $data = $request->validate([
            'fecha'              => 'required|date',
            'responsable_sesion' => 'required|exists:users,id',
            'costo_sesion'       => 'required|numeric',
            'que_se_trabajo'     => 'required|string',
            'evolucion'          => 'required|string',
            'observaciones'      => 'nullable|string',
        ], [
            'fecha.required'              => 'La fecha de la sesión es obligatoria.',
            'responsable_sesion.required' => 'Debe seleccionar al fisioterapeuta responsable.',
            'costo_sesion.required'       => 'El costo de la sesión es obligatorio.',
            'que_se_trabajo.required'     => 'Debe detallar qué se trabajó en la sesión.',
            'evolucion.required'          => 'La evolución clínica del paciente es obligatoria.',
        ]);

        $session->update($data);

        return response()->json([
            'message' => 'Sesión actualizada correctamente.',
            'session' => $session
        ], 200);
    }

    // Eliminar sesión (API)
    public function destroy(TherapySession $session)
    {
        $session->delete();

        return response()->json([
            'message' => 'Sesión eliminada correctamente.'
        ], 200);
    }
}

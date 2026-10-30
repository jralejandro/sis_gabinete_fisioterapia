<?php

namespace App\Http\Controllers\Api; // Asegúrate de que coincida con tu carpeta

use App\Http\Controllers\Controller;
use App\Models\DischargeRecord;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class DischargeRecordController extends Controller
{
    /**
     * Registrar ficha de alta (POST)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_alta'           => 'required|date',
            'responsable_alta'     => 'required|integer|exists:users,id',
            'estado'               => 'required|in:ACTIVO,ABANDONO,ALTA',
            'evolucion_alta'       => 'required|string',
            'observaciones_alta'   => 'required|string',
            'recomendaciones_alta' => 'required|string',
            'evaluation_id'        => 'required|exists:evaluations,id',
        ], [
            'fecha_alta.required'           => 'La fecha de alta es obligatoria.',
            'responsable_alta.required'     => 'Debe seleccionar al responsable.',
            'estado.required'               => 'Debe indicar el estado final.',
            'evolucion_alta.required'       => 'La evolución es obligatoria.',
            'observaciones_alta.required'   => 'Las observaciones son obligatorias.',
            'recomendaciones_alta.required' => 'Las recomendaciones son obligatorias.',
        ]);

        // Crear registro
        $record = DischargeRecord::create([
            'fecha_alta'           => $data['fecha_alta'],
            'responsable_alta'     => $data['responsable_alta'],
            'estado'               => $data['estado'],
            'evolucion_alta'       => $data['evolucion_alta'],
            'observaciones_alta'   => $data['observaciones_alta'],
            'recomendaciones_alta' => $data['recomendaciones_alta'],
            'evaluation_id'        => $data['evaluation_id'],
        ]);

        // Actualizar estado de la evaluación
        $evaluation = Evaluation::find($data['evaluation_id']);
        $evaluation->estado = $data['estado'];
        $evaluation->save();

        // 🔥 En lugar de redirect()->back(), respondemos JSON
        return response()->json([
            'message' => 'Ficha de alta registrada correctamente.',
            'record'  => $record
        ], 201);
    }

    /**
     * Actualizar ficha de alta (PUT)
     */
    /**
     * Actualizar ficha de alta (PUT)
     */
    public function update(Request $request, DischargeRecord $dischargeRecord)
    {
        $data = $request->validate([
            'fecha_alta'           => 'required|date',
            'responsable_alta'     => 'required|integer|exists:users,id',
            'estado'               => 'required|in:ACTIVO,ABANDONO,ALTA', // 🔥 Ahora validamos el estado al editar
            'evolucion_alta'       => 'required|string',
            'observaciones_alta'   => 'required|string',
            'recomendaciones_alta' => 'required|string',
            'evaluation_id'        => 'required|exists:evaluations,id',
        ], [
            'fecha_alta.required'           => 'La fecha de alta es obligatoria.',
            'responsable_alta.required'     => 'Debe seleccionar al responsable.',
            'estado.required'               => 'Debe indicar el estado final.',
            'evolucion_alta.required'       => 'La evolución es obligatoria.',
            'observaciones_alta.required'   => 'Las observaciones son obligatorias.',
            'recomendaciones_alta.required' => 'Las recomendaciones son obligatorias.',
        ]);

        // 1. Actualizar la ficha de alta (incluyendo el nuevo estado)
        $dischargeRecord->update([
            'fecha_alta'           => $data['fecha_alta'],
            'responsable_alta'     => $data['responsable_alta'],
            'estado'               => $data['estado'],
            'evolucion_alta'       => $data['evolucion_alta'],
            'observaciones_alta'   => $data['observaciones_alta'],
            'recomendaciones_alta' => $data['recomendaciones_alta'],
        ]);

        // 2. 🔥 Sincronizar el estado en la Evaluación principal
        $evaluation = Evaluation::find($data['evaluation_id']);
        if ($evaluation) {
            $evaluation->estado = $data['estado'];
            $evaluation->save();
        }

        return response()->json([
            'message' => 'Ficha de alta actualizada correctamente.',
            'record'  => $dischargeRecord
        ], 200);
    }
}

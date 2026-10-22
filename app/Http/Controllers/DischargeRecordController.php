<?php

namespace App\Http\Controllers;

use App\Models\DischargeRecord;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class DischargeRecordController extends Controller
{
    /**
     * Registrar ficha de alta
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'fecha_alta'           => 'required|date',
                'responsable_alta'     => 'required|integer|exists:users,id',
                'estado'               => 'required|in:ACTIVO,ABANDONO,ALTA',
                'evolucion_alta'       => 'required|string',
                'observaciones_alta'   => 'required|string',
                'recomendaciones_alta' => 'required|string',
                'evaluation_id'        => 'required|exists:evaluations,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            // 🔥 Reabrir modal ficha de alta
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_alta_modal', true);
        }

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

        return redirect()
            ->back()
            ->with('success', 'Ficha de alta registrada correctamente.');
    }

    /**
     * Actualizar ficha de alta
     */
    public function update(Request $request, DischargeRecord $dischargeRecord)
    {
        try {
            $data = $request->validate([
                'fecha_alta'           => 'required|date',
                'responsable_alta'     => 'required|integer|exists:users,id',
                'evolucion_alta'       => 'required|string',
                'observaciones_alta'   => 'required|string',
                'recomendaciones_alta' => 'required|string',
                'evaluation_id'        => 'required|exists:evaluations,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_alta_modal', true);
        }

        // Actualizar ficha de alta
        $dischargeRecord->update([
            'fecha_alta'           => $data['fecha_alta'],
            'responsable_alta'     => $data['responsable_alta'],
            'evolucion_alta'       => $data['evolucion_alta'],
            'observaciones_alta'   => $data['observaciones_alta'],
            'recomendaciones_alta' => $data['recomendaciones_alta'],
        ]);

        // ⚠️ No actualizar el estado de la evaluación al editar
        // El estado sigue igual

        return redirect()
            ->back()
            ->with('success', 'Ficha de alta actualizada correctamente.');
    }
}

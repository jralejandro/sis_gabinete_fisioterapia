<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\GynecoObstetricHistory;
use Illuminate\Http\Request;

class GynecoObstetricHistoryController extends Controller
{
    /**
     * Guardar o actualizar los antecedentes Gineco-Obstétricos de una evaluación.
     * PUT /api/evaluations/{evaluation_id}/gyneco-obstetric
     */
    public function updateForEvaluation(Request $request, $evaluationId)
    {
        // 1. Validar datos (respetando los nombres de tus columnas en inglés)
        $data = $request->validate([
            'gestations'             => 'nullable|integer|min:0',
            'deliveries'             => 'nullable|integer|min:0',
            'abortions'              => 'nullable|integer|min:0',
            'cesarean_sections'      => 'nullable|integer|min:0',
            'last_menstruation_date' => 'nullable|date',
            'last_delivery_date'     => 'nullable|date',
            'last_cesarean_date'     => 'nullable|date',
            'last_abortion_date'     => 'nullable|date',
            'menarche_age'           => 'nullable|integer|min:8|max:60',
            'catamenial_pattern'     => 'nullable|string|max:100',
            'menstrual_cycle'        => 'nullable|string|max:100',
        ], [
            'integer' => 'Este campo debe ser un número entero.',
            'min'     => 'El valor no puede ser menor a :min.',
            'max'     => 'El valor no puede ser mayor a :max.',
            'date'    => 'Ingrese una fecha válida.'
        ]);

        // 2. Verificar que la evaluación exista
        $evaluation = Evaluation::findOrFail($evaluationId);

        // 3. updateOrCreate: Si ya existe historial para esta evaluación, lo actualiza. 
        // Si no existe, lo crea automáticamente.
        $gynecoHistory = $evaluation->gynecoObstetricHistory()->updateOrCreate(
            ['evaluation_id' => $evaluation->id],
            $data
        );

        // 4. Devolver respuesta JSON estandarizada
        return response()->json([
            'message'        => 'Antecedentes gineco-obstétricos actualizados correctamente.',
            'gyneco_history' => $gynecoHistory
        ], 200);
    }
}

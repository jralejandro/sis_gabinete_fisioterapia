<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Antecedent;
use App\Models\Patient;
use Illuminate\Http\Request;

class AntecedentController extends Controller
{
    /**
     * Guardar o actualizar los antecedentes de un paciente específico.
     * En una API RESTful, esto suele mapearse a: PUT /api/patients/{id}/antecedents
     */
    public function updateForPatient(Request $request, $patientId)
    {
        // 1. Validar los datos de entrada
        $data = $request->validate([
            'patologicos'    => 'nullable|string',
            'familiares'     => 'nullable|string',
            'no_patologicos' => 'nullable|string',
        ]);

        // 2. Verificar que el paciente exista
        $patient = Patient::findOrFail($patientId);

        // 3. updateOrCreate: Si el paciente ya tiene antecedentes, los actualiza. 
        // Si no tiene, los inserta automáticamente.
        $antecedent = $patient->antecedent()->updateOrCreate(
            ['patient_id' => $patient->id],
            $data
        );

        // 4. Devolver respuesta JSON estandarizada
        return response()->json([
            'message'    => 'Antecedentes guardados correctamente.',
            'antecedent' => $antecedent
        ], 200);
    }

    /**
     * (Opcional) Si en el futuro necesitas un endpoint directo al ID del antecedente
     * PUT /api/antecedents/{id}
     */
    public function update(Request $request, Antecedent $antecedent)
    {
        $data = $request->validate([
            'patologicos'    => 'nullable|string',
            'familiares'     => 'nullable|string',
            'no_patologicos' => 'nullable|string',
        ]);

        $antecedent->update($data);

        return response()->json([
            'message'    => 'Antecedentes actualizados correctamente.',
            'antecedent' => $antecedent
        ], 200);
    }
}

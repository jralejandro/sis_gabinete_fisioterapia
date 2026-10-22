<?php

namespace App\Http\Controllers;

use App\Models\Antecedent;
use App\Models\Patient;
use Illuminate\Http\Request;

class AntecedentController extends Controller
{
    /**
     * Guardar un nuevo antecedente o actualizar el existente (uno por paciente)
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'patologicos' => 'nullable|string',
            'familiares' => 'nullable|string',
            'no_patologicos' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($request->patient_id);

        // Si ya tiene antecedente, actualizamos
        if ($patient->antecedent) {
            $patient->antecedent->update($request->only([
                'patologicos',
                'familiares',
                'no_patologicos',
            ]));
        } else {
            // Si no tiene, creamos uno nuevo
            Antecedent::create([
                'patient_id' => $patient->id,
                'patologicos' => $request->patologicos,
                'familiares' => $request->familiares,
                'no_patologicos' => $request->no_patologicos,
            ]);
        }

        return back()->with('success', 'Antecedentes guardados correctamente.');
    }

    /**
     * Actualizar antecedentes (si usas ruta PUT /antecedents/{id})
     */
    public function update(Request $request, Antecedent $antecedent)
    {
        $request->validate([
            'patologicos' => 'nullable|string',
            'familiares' => 'nullable|string',
            'no_patologicos' => 'nullable|string',
        ]);

        $antecedent->update($request->only([
            'patologicos',
            'familiares',
            'no_patologicos',
        ]));

        return back()->with('success', 'Antecedentes actualizados correctamente.');
    }
}

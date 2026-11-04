<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluationController extends Controller
{
    /**
     * Mensajes de validación en español
     */
    private function mensajesValidacion(): array
    {
        return [
            'required' => 'Este campo es obligatorio.',
            'numeric'  => 'Este campo debe ser un número válido.',
            'date'     => 'Ingrese una fecha válida.',
            'exists'   => 'El registro seleccionado no existe.',
            'diagnostico_medico_otro.required_if' => 'Debe especificar el diagnóstico si seleccionó "Otro".',
            'gestaciones.min' => 'El valor no puede ser negativo.',
            'edad_menarca.max' => 'La edad de menarca no parece correcta.',
            'archivo_examen_complementario.mimes' => 'El archivo adjunto debe ser obligatoriamente un formato PDF.',
            'archivo_examen_complementario.max' => 'El archivo PDF no debe pesar más de 5 Megabytes (5120 KB).',
        ];
    }

    /**
     * Mostrar una evaluación específica con sus relaciones
     */
    public function show(Evaluation $evaluation)
    {
        // Traemos la evaluación con los datos del paciente, sus sesiones y datos ginecológicos
        $evaluation->load([
            'patient',
            'therapySessions',
            'gynecoObstetricHistory',
            'dischargeRecord'
        ]);

        return response()->json($evaluation);
    }

    /**
     * Registrar una nueva evaluación para un paciente específico
     */
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'motivo_consulta'            => 'required|string',
            'medico_referencia'          => 'nullable|string',
            'centro_referencia'          => 'nullable|string|max:255',
            'diagnostico_medico'         => 'required|string',
            'diagnostico_medico_otro'    => 'required_if:diagnostico_medico,Otro',
            'costo_evaluacion'           => 'required|numeric',
            'responsable_evaluacion'     => 'required|integer|exists:users,id',
            'historia_enfermedad_actual' => 'required|string',

            'frecuencia_cardiaca'        => 'required|numeric',
            'frecuencia_respiratoria'    => 'required|numeric',
            'presion_arterial'           => 'required|string',
            'temperatura'                => 'required|numeric',
            'fecha'                      => 'required|date',

            'caracteristicas_dolor'      => 'required|string',
            'inspeccion'                 => 'required|string',
            'evaluacion_postural'        => 'required|string',
            'contractura_muscular'       => 'required|string',
            'puntos_dolorosos'           => 'required|string',
            'puntos_gatillo'             => 'required|string',
            'retreaccion_acortamiento'   => 'required|string',

            'tono'                       => 'required|string',
            'tono_detalle'               => 'required|string',
            'trofismo'                   => 'required|string',
            'trofismo_detalle'           => 'required|string',

            'evaluacion_marcha'          => 'required|string',
            'balance_articular'          => 'required|string',
            'balance_muscular'           => 'required|string',
            'sensibilidad'               => 'required|string',

            'examenes_complementarios'   => 'required|string',

            // 🔥 VALIDACIÓN PARA EL PDF DEL EXAMEN COMPLEMENTARIO
            'archivo_examen_complementario' => 'nullable|file|mimes:pdf|max:5120',

            'diagnostico_kinesico'       => 'required|string',
            'objetivos_tratamiento'      => 'required|string',
            'plan_tratamiento'           => 'required|string',

            // Datos gineco-obstétricos (Solo mujeres)
            'gestaciones'                => 'nullable|integer|min:0',
            'partos'                     => 'nullable|integer|min:0',
            'abortos'                    => 'nullable|integer|min:0',
            'cesareas'                   => 'nullable|integer|min:0',
            'fecha_ultima_menstruacion'  => 'nullable|date',
            'fecha_ultimo_parto'         => 'nullable|date',
            'fecha_ultima_cesarea'       => 'nullable|date',
            'fecha_ultimo_aborto'        => 'nullable|date',
            'edad_menarca'               => 'nullable|integer|max:60',
            'patron_catamenial'          => 'nullable|string|max:100',
            'ciclo_menstrual'            => 'nullable|string|max:100',
        ], $this->mensajesValidacion());

        // Asignar paciente y usuario que registra
        $validated['patient_id'] = $patient->id;
        $validated['created_by'] = auth()->id();
        $validated['estado'] = 'ACTIVO'; // Estado por defecto

        // 🔥 GESTIÓN DE SUBIDA DEL ARCHIVO PDF (STORE)
        if ($request->hasFile('archivo_examen_complementario')) {
            $path = $request->file('archivo_examen_complementario')->store('examenes_complementarios', 'public');
            $validated['archivo_examen_complementario'] = $path;
        }

        // Crear la evaluación
        $evaluation = Evaluation::create($validated);

        // Si es mujer, guardamos los datos gineco-obstétricos
        if ($patient->sexo === 'Femenino') {
            $evaluation->gynecoObstetricHistory()->create($request->only([
                'gestaciones',
                'partos',
                'abortos',
                'cesareas',
                'fecha_ultima_menstruacion',
                'fecha_ultimo_parto',
                'fecha_ultima_cesarea',
                'fecha_ultimo_aborto',
                'edad_menarca',
                'patron_catamenial',
                'ciclo_menstrual'
            ]));
        }

        return response()->json([
            'message' => 'Evaluación registrada exitosamente.',
            'evaluation' => $evaluation
        ], 201);
    }

    /**
     * Actualizar una evaluación existente
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'motivo_consulta'            => 'required|string',
            'medico_referencia'          => 'nullable|string',
            'centro_referencia'          => 'nullable|string|max:255',
            'diagnostico_medico'         => 'required|string',
            'diagnostico_medico_otro'    => 'required_if:diagnostico_medico,Otro',
            'costo_evaluacion'           => 'required|numeric',
            'responsable_evaluacion'     => 'required|integer|exists:users,id',
            'historia_enfermedad_actual' => 'required|string',

            'frecuencia_cardiaca'        => 'required|numeric',
            'frecuencia_respiratoria'    => 'required|numeric',
            'presion_arterial'           => 'required|string',
            'temperatura'                => 'required|numeric',
            'fecha'                      => 'required|date',

            'caracteristicas_dolor'      => 'required|string',
            'inspeccion'                 => 'required|string',
            'evaluacion_postural'        => 'required|string',
            'contractura_muscular'       => 'required|string',
            'puntos_dolorosos'           => 'required|string',
            'puntos_gatillo'             => 'required|string',
            'retreaccion_acortamiento'   => 'required|string',

            'tono'                       => 'required|string',
            'tono_detalle'               => 'required|string',
            'trofismo'                   => 'required|string',
            'trofismo_detalle'           => 'required|string',

            'evaluacion_marcha'          => 'required|string',
            'balance_articular'          => 'required|string',
            'balance_muscular'           => 'required|string',
            'sensibilidad'               => 'required|string',

            'examenes_complementarios'   => 'required|string',

            // 🔥 VALIDACIÓN PARA EL PDF DEL EXAMEN COMPLEMENTARIO
            'archivo_examen_complementario' => 'nullable|file|mimes:pdf|max:5120',

            'diagnostico_kinesico'       => 'required|string',
            'objetivos_tratamiento'      => 'required|string',
            'plan_tratamiento'           => 'required|string',

            // Datos gineco-obstétricos
            'gestaciones'                => 'nullable|integer|min:0',
            'partos'                     => 'nullable|integer|min:0',
            'abortos'                    => 'nullable|integer|min:0',
            'cesareas'                   => 'nullable|integer|min:0',
            'fecha_ultima_menstruacion'  => 'nullable|date',
            'fecha_ultimo_parto'         => 'nullable|date',
            'fecha_ultima_cesarea'       => 'nullable|date',
            'fecha_ultimo_aborto'        => 'nullable|date',
            'edad_menarca'               => 'nullable|integer|max:60',
            'patron_catamenial'          => 'nullable|string|max:100',
            'ciclo_menstrual'            => 'nullable|string|max:100',
        ], $this->mensajesValidacion());

        // 🔥 GESTIÓN DE SUBIDA DEL ARCHIVO PDF (UPDATE)
        if ($request->hasFile('archivo_examen_complementario')) {
            // Eliminar el archivo viejo si existe para no saturar el servidor
            if ($evaluation->archivo_examen_complementario) {
                Storage::disk('public')->delete($evaluation->archivo_examen_complementario);
            }

            // Subir el nuevo archivo
            $path = $request->file('archivo_examen_complementario')->store('examenes_complementarios', 'public');
            $validated['archivo_examen_complementario'] = $path;
        }

        // Actualizar la evaluación
        $evaluation->update($validated);

        // Actualizar o crear antecedentes gineco-obstétricos (solo si es mujer)
        $patient = $evaluation->patient;
        if ($patient->sexo === 'Femenino') {
            $gynecoData = $request->only([
                'gestaciones',
                'partos',
                'abortos',
                'cesareas',
                'fecha_ultima_menstruacion',
                'fecha_ultimo_parto',
                'fecha_ultima_cesarea',
                'fecha_ultimo_aborto',
                'edad_menarca',
                'patron_catamenial',
                'ciclo_menstrual'
            ]);

            // Solo guardamos si hay al menos un dato ingresado (evita registros vacíos)
            if (array_filter($gynecoData, fn($value) => !is_null($value) && $value !== '')) {
                $evaluation->gynecoObstetricHistory()->updateOrCreate(
                    ['evaluation_id' => $evaluation->id],
                    $gynecoData
                );
            }
        }

        return response()->json([
            'message' => 'Evaluación actualizada exitosamente.',
            'evaluation' => $evaluation
        ], 200);
    }

    /**
     * Eliminar evaluación
     */
    public function destroy(Evaluation $evaluation)
    {
        // 🔥 Limpieza en cascada: Eliminar el PDF del servidor antes de borrar el registro
        if ($evaluation->archivo_examen_complementario) {
            Storage::disk('public')->delete($evaluation->archivo_examen_complementario);
        }

        $evaluation->delete();
        return response()->json(['message' => 'Evaluación y archivos adjuntos eliminados correctamente.'], 200);
    }
}

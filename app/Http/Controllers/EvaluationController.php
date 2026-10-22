<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\User;

class EvaluationController extends Controller
{
    public function create(Patient $patient)
    {
        $diagnosticos = [
            // Músculo-esqueléticos
            'Hernia de disco',
            'Lumbalgia',
            'Cervicalgia',
            'Dorsalgia',
            'Ciatalgia',
            'Mialgias',
            'Contractura muscular',
            'Desgarro muscular',
            'Esguince',
            'Fracturas',
            'Tendinitis',
            'Tendinosis',
            'Bursitis',
            'Sinovitis',
            'Artrosis',
            'Artritis reumatoide',
            'Condromalacia rotuliana',
            'Alteraciones posturales',
            'Fascitis plantar',
            'Lesión del manguito rotador',
            'Escoliosis',
            'Cifosis',
            'Lordosis',
            'Pie plano',
            'Pie cavo',
            'Síndrome patelofemoral',
            'Síndrome miofascial',
            'Fibromialgia',
            'Espolón calcáneo',
            'Deformidades angulares de rodilla',
            'Deformidades de columna',

            // Neurológicos
            'Parálisis facial',
            'Parálisis cerebral',
            'Esclerosis múltiple',
            'Enfermedades neurodegenerativas',
            'Polineuropatía',
            'Síndrome de Guillain-Barré',
            'Accidente cerebrovascular (ACV)',
            'Lesión medular',
            'Parkinson',
            'Neuropatía periférica',
            'Ataxia',
            'Hemiplejia',
            'Tetraplejia',
            'Monoparesia',
            'Radiculopatía',

            // Respiratorios
            'EPOC',
            'Enfermedades pulmonares restrictivas',
            'Enfermedades pulmonares obstructivas',
            'Asma',
            'Fibrosis pulmonar',
            'Bronquiectasias',

            // Geriátricos
            'Síndrome geriátrico',
            'Síndrome de inmovilidad',
            'Sarcopenia',
            'Fragilidad muscular',
            'Osteoporosis',

            // Circulatorios
            'Eritrocitosis',
            'Insuficiencia venosa',
            'Linfedema',
            'Edema postquirúrgico',
            'Trombosis venosa profunda (rehabilitación)',

            // Pediátricos
            'Retraso del desarrollo psicomotor',
            'Displasia de cadera',
            'Hipotonía',
            'Hipertonía',
            'Pie equino varo',
            'Torsión tibial',

            // Posquirúrgicos
            'Rehabilitación post artroplastia de rodilla',
            'Rehabilitación post artroplastia de cadera',
            'Post artroscopia',
            'Post reparación de manguito rotador',

            // Otros
            'Dolor crónico inespecífico',
            'Rehabilitación deportiva',
            'Lesiones por esfuerzo repetitivo (LER)',
            'Síndrome del túnel carpiano',
            'Síndrome de salida torácica'
        ];

        // Obtener usuarios con cargo Fisioterapeuta o Interno
        $responsables = User::whereIn('cargo', ['Fisioterapeuta', 'Interno'])
            ->where('estado', 1) // si usas campo estado como activo/inactivo
            ->orderBy('nombres')
            ->get();

        // Enviar datos a la vista
        return view('evaluations.create', compact('patient', 'responsables', 'diagnosticos'));
    }


    public function store(Request $request, Patient $patient)
    {
        // 🔹 Validar campos principales de evaluación
        $validated = $request->validate([
            'motivo_consulta' => 'required|string',
            'medico_referencia' => 'nullable|string',
            'centro_referencia' => 'nullable|string',
            'diagnostico_medico' => 'required|string',
            'diagnostico_medico_otro' => 'required_if:diagnostico_medico,Otro',
            'costo_evaluacion' => 'required|numeric',
            'responsable_evaluacion' => 'required|integer|exists:users,id',
            'historia_enfermedad_actual' => 'required|string',

            'frecuencia_cardiaca' => 'required|numeric',
            'frecuencia_respiratoria' => 'required|numeric',
            'presion_arterial' => 'required|string',
            'temperatura' => 'required|numeric',
            'fecha' => 'required|date',

            // 🔥 Nuevos campos requeridos
            'caracteristicas_dolor'       => 'required|string',
            'inspeccion'                  => 'required|string',
            'evaluacion_postural'         => 'required|string',

            'contractura_muscular'        => 'required|string',
            'puntos_dolorosos'            => 'required|string',
            'puntos_gatillo'              => 'required|string',
            'retreaccion_acortamiento'    => 'required|string',

            'tono'                        => 'required|string',
            'tono_detalle'                => 'required|string',

            'trofismo'                    => 'required|string',
            'trofismo_detalle'            => 'required|string',

            'evaluacion_marcha'           => 'required|string',
            'balance_articular'           => 'required|string',
            'balance_muscular'            => 'required|string',
            'sensibilidad'                => 'required|string',

            // ❗ Ya NO están los campos eliminados (aparicion, localizacion, etc.)

            // Otros campos clínicos
            'examenes_complementarios'    => 'required|string',
            'diagnostico_kinesico'        => 'required|string',
            'objetivos_tratamiento'       => 'required|string',
            'plan_tratamiento'            => 'required|string',

            // 🔹 Datos gineco-obstétricos
            'gestaciones' => 'nullable|integer|min:0',
            'partos' => 'nullable|integer|min:0',
            'abortos' => 'nullable|integer|min:0',
            'cesareas' => 'nullable|integer|min:0',
            'fecha_ultima_menstruacion' => 'nullable|date',
            'fecha_ultimo_parto' => 'nullable|date',
            'fecha_ultima_cesarea' => 'nullable|date',
            'fecha_ultimo_aborto' => 'nullable|date',
            'edad_menarca' => 'nullable|integer|max:60',
            'patron_catamenial' => 'nullable|string|max:100',
            'ciclo_menstrual' => 'nullable|string|max:100',
        ]);

        // 🔹 Datos básicos de la evaluación
        $validated['patient_id'] = $patient->id;
        $validated['created_by'] = auth()->id();

        // ✅ Crear evaluación principal
        $evaluation = Evaluation::create($validated);

        // 🔸 Registrar antecedentes gineco-obstétricos si corresponde
        if ($patient->sexo === 'Femenino') {
            $evaluation->gynecoObstetricHistory()->create([
                'gestaciones' => $request->gestaciones,
                'partos' => $request->partos,
                'abortos' => $request->abortos,
                'cesareas' => $request->cesareas,
                'fecha_ultima_menstruacion' => $request->fecha_ultima_menstruacion,
                'fecha_ultimo_parto' => $request->fecha_ultimo_parto,
                'fecha_ultima_cesarea' => $request->fecha_ultima_cesarea,
                'fecha_ultimo_aborto' => $request->fecha_ultimo_aborto,
                'edad_menarca' => $request->edad_menarca,
                'patron_catamenial' => $request->patron_catamenial,
                'ciclo_menstrual' => $request->ciclo_menstrual,
            ]);
        }

        return redirect()
            ->route('patients.work', $patient->id)
            ->with('success', 'Evaluación creada correctamente.');
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['patient', 'therapySessions', 'gynecoObstetric']);
        return view('evaluation.show', compact('evaluation'));
    }


    // Mostrar formulario de edición
    public function edit(Patient $patient, Evaluation $evaluation)
    {
        $diagnosticos = [
            // Músculo-esqueléticos
            'Hernia de disco',
            'Lumbalgia',
            'Cervicalgia',
            'Dorsalgia',
            'Ciatalgia',
            'Mialgias',
            'Contractura muscular',
            'Desgarro muscular',
            'Esguince',
            'Fracturas',
            'Tendinitis',
            'Tendinosis',
            'Bursitis',
            'Sinovitis',
            'Artrosis',
            'Artritis reumatoide',
            'Condromalacia rotuliana',
            'Alteraciones posturales',
            'Fascitis plantar',
            'Lesión del manguito rotador',
            'Escoliosis',
            'Cifosis',
            'Lordosis',
            'Pie plano',
            'Pie cavo',
            'Síndrome patelofemoral',
            'Síndrome miofascial',
            'Fibromialgia',
            'Espolón calcáneo',
            'Deformidades angulares de rodilla',
            'Deformidades de columna',

            // Neurológicos
            'Parálisis facial',
            'Parálisis cerebral',
            'Esclerosis múltiple',
            'Enfermedades neurodegenerativas',
            'Polineuropatía',
            'Síndrome de Guillain-Barré',
            'Accidente cerebrovascular (ACV)',
            'Lesión medular',
            'Parkinson',
            'Neuropatía periférica',
            'Ataxia',
            'Hemiplejia',
            'Tetraplejia',
            'Monoparesia',
            'Radiculopatía',

            // Respiratorios
            'EPOC',
            'Enfermedades pulmonares restrictivas',
            'Enfermedades pulmonares obstructivas',
            'Asma',
            'Fibrosis pulmonar',
            'Bronquiectasias',

            // Geriátricos
            'Síndrome geriátrico',
            'Síndrome de inmovilidad',
            'Sarcopenia',
            'Fragilidad muscular',
            'Osteoporosis',

            // Circulatorios
            'Eritrocitosis',
            'Insuficiencia venosa',
            'Linfedema',
            'Edema postquirúrgico',
            'Trombosis venosa profunda (rehabilitación)',

            // Pediátricos
            'Retraso del desarrollo psicomotor',
            'Displasia de cadera',
            'Hipotonía',
            'Hipertonía',
            'Pie equino varo',
            'Torsión tibial',

            // Posquirúrgicos
            'Rehabilitación post artroplastia de rodilla',
            'Rehabilitación post artroplastia de cadera',
            'Post artroscopia',
            'Post reparación de manguito rotador',

            // Otros
            'Dolor crónico inespecífico',
            'Rehabilitación deportiva',
            'Lesiones por esfuerzo repetitivo (LER)',
            'Síndrome del túnel carpiano',
            'Síndrome de salida torácica',
        ];

        $responsables = User::whereIn('cargo', ['Fisioterapeuta', 'Interno'])
            ->where('estado', 1)
            ->get();

        return view('evaluations.edit', compact('patient', 'evaluation', 'responsables', 'diagnosticos'));
    }

    // 🔹 Actualizar evaluación
    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'motivo_consulta' => 'required|string',
            'medico_referencia' => 'nullable|string',
            'centro_referencia' => 'nullable|string',
            'diagnostico_medico' => 'required|string',
            'diagnostico_medico_otro' => 'required_if:diagnostico_medico,Otro',
            'costo_evaluacion' => 'required|numeric',
            'responsable_evaluacion' => 'required|integer|exists:users,id',
            'historia_enfermedad_actual' => 'required|string',

            'frecuencia_cardiaca' => 'required|numeric',
            'frecuencia_respiratoria' => 'required|numeric',
            'presion_arterial' => 'required|string',
            'temperatura' => 'required|numeric',
            'fecha' => 'required|date',

            // 🔥 Nuevos campos (mismos que STORE)
            'caracteristicas_dolor'       => 'required|string',
            'inspeccion'                  => 'required|string',
            'evaluacion_postural'         => 'required|string',

            'contractura_muscular'        => 'required|string',
            'puntos_dolorosos'            => 'required|string',
            'puntos_gatillo'              => 'required|string',
            'retreaccion_acortamiento'    => 'required|string',

            'tono'                        => 'required|string',
            'tono_detalle'                => 'required|string',

            'trofismo'                    => 'required|string',
            'trofismo_detalle'            => 'required|string',

            'evaluacion_marcha'           => 'required|string',
            'balance_articular'           => 'required|string',
            'balance_muscular'            => 'required|string',
            'sensibilidad'                => 'required|string',

            // ❗ Campos clínicos
            'examenes_complementarios'    => 'required|string',
            'diagnostico_kinesico'        => 'required|string',
            'objetivos_tratamiento'       => 'required|string',
            'plan_tratamiento'            => 'required|string',

            // 🔹 Datos gineco-obstétricos
            'gestaciones' => 'nullable|integer|min:0',
            'partos' => 'nullable|integer|min:0',
            'abortos' => 'nullable|integer|min:0',
            'cesareas' => 'nullable|integer|min:0',
            'fecha_ultima_menstruacion' => 'nullable|date',
            'fecha_ultimo_parto' => 'nullable|date',
            'fecha_ultima_cesarea' => 'nullable|date',
            'fecha_ultimo_aborto' => 'nullable|date',
            'edad_menarca' => 'nullable|integer|max:60',
            'patron_catamenial' => 'nullable|string|max:100',
            'ciclo_menstrual' => 'nullable|string|max:100',
        ]);

        // ✅ Actualizar evaluación
        $evaluation->update($validated);

        // 🔸 Obtener el paciente
        $patient = $evaluation->patient;

        // 🔸 Actualizar o crear antecedentes gineco-obstétricos
        if ($patient->sexo === 'Femenino') {

            $gynecoData = [
                'gestaciones' => $request->gestaciones,
                'partos' => $request->partos,
                'abortos' => $request->abortos,
                'cesareas' => $request->cesareas,
                'fecha_ultima_menstruacion' => $request->fecha_ultima_menstruacion,
                'fecha_ultimo_parto' => $request->fecha_ultimo_parto,
                'fecha_ultima_cesarea' => $request->fecha_ultima_cesarea,
                'fecha_ultimo_aborto' => $request->fecha_ultimo_aborto,
                'edad_menarca' => $request->edad_menarca,
                'patron_catamenial' => $request->patron_catamenial,
                'ciclo_menstrual' => $request->ciclo_menstrual,
            ];

            if ($evaluation->gynecoObstetricHistory) {
                // ✔️ Si ya existe, actualizar
                $evaluation->gynecoObstetricHistory->update($gynecoData);
            } else {
                // ✔️ Si no existe pero hay datos → crear
                if (
                    $request->filled('gestaciones') ||
                    $request->filled('fecha_ultima_menstruacion') ||
                    $request->filled('edad_menarca')
                ) {

                    $evaluation->gynecoObstetricHistory()->create($gynecoData);
                }
            }
        }

        return redirect()
            ->route('patients.work', $evaluation->patient_id)
            ->with('success', 'Evaluación actualizada correctamente.');
    }




    public function destroy(Evaluation $evaluation)
    {
        $patientId = $evaluation->patient_id;
        $evaluation->delete();

        return redirect()
            ->route('patients.show', $patientId)
            ->with('success', 'Evaluación eliminada correctamente.');
    }
}

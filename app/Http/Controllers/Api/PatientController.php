<?php

namespace App\Http\Controllers\Api; // Cambia a App\Http\Controllers si no usas la subcarpeta Api

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Listar todos los pacientes
     */
    public function index()
    {
        $patients = Patient::orderBy('id', 'desc')->get();
        return response()->json($patients);
    }

    /**
     * Diccionario centralizado de mensajes en español
     */
    private function mensajesValidacion(): array
    {
        return [
            'ci.required'               => 'La Cédula de Identidad es obligatoria.',
            'ci.numeric'                => 'El CI solo debe contener números.',
            'ci.digits_between'         => 'El CI debe tener entre 5 y 15 dígitos.',
            'ci.unique'                 => 'Ya existe un paciente registrado con este CI y complemento.',
            'complemento.regex'         => 'El complemento solo puede contener hasta 3 caracteres alfanuméricos.',
            'complemento.max'           => 'El complemento no puede exceder los 3 caracteres.',
            'nombres.required'          => 'El nombre es obligatorio.',
            'sexo.required'             => 'Debe especificar el sexo del paciente.',
            'ocupacion.required'        => 'La ocupación o profesión es obligatoria.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'     => 'La fecha de nacimiento no tiene un formato válido.',
            'estado_civil.required'     => 'El estado civil es obligatorio.',
        ];
    }

    /**
     * Registrar un nuevo paciente
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('patients')->where(function ($query) use ($request) {
                    $complemento = $request->complemento ?: null;
                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                $q->whereNull('complemento')->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                }),
            ],
            'complemento'         => ['nullable', 'string', 'max:3', 'regex:/^[A-Za-z0-9]{0,3}$/'],
            'nombres'             => 'required|string|max:255',
            'apellido_paterno'    => 'nullable|string|max:255',
            'apellido_materno'    => 'nullable|string|max:255',
            'sexo'                => 'required|string|max:10',
            'ocupacion'           => 'required|string|max:255',
            'fecha_nacimiento'    => 'required|date',
            'domicilio'           => 'nullable|string|max:255',
            'estado_civil'        => 'required|string|max:50',
            'celular'             => 'nullable|string|max:20',
            'persona_referencia'  => 'nullable|string|max:255',
            'celular_referencia'  => 'nullable|string|max:20',
            'medio_contacto'      => 'nullable|string|max:255', // 🔥 NUEVO CAMPO DE MARKETING
        ], $this->mensajesValidacion());

        // Normalizar complemento
        $validatedData['complemento'] = !empty($validatedData['complemento']) ? strtoupper($validatedData['complemento']) : null;

        // Vincular al usuario (fisioterapeuta/interno) que registra al paciente
        $validatedData['user_id'] = auth()->id();

        $patient = Patient::create($validatedData);

        return response()->json([
            'message' => 'Paciente registrado exitosamente.',
            'patient' => $patient
        ], 201);
    }

    /**
     * Actualizar datos del paciente
     */
    public function update(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('patients')->where(function ($query) use ($request, $patient) {
                    $complemento = $request->complemento ?: null;
                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                $q->whereNull('complemento')->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                })->ignore($patient->id),
            ],
            'complemento'         => ['nullable', 'string', 'max:3', 'regex:/^[A-Za-z0-9]{0,3}$/'],
            'nombres'             => 'required|string|max:255',
            'apellido_paterno'    => 'nullable|string|max:255',
            'apellido_materno'    => 'nullable|string|max:255',
            'sexo'                => 'required|string|max:10',
            'ocupacion'           => 'required|string|max:255',
            'fecha_nacimiento'    => 'required|date',
            'domicilio'           => 'nullable|string|max:255',
            'estado_civil'        => 'required|string|max:50',
            'celular'             => 'nullable|string|max:20',
            'persona_referencia'  => 'nullable|string|max:255',
            'celular_referencia'  => 'nullable|string|max:20',
            'medio_contacto'      => 'nullable|string|max:255', // 🔥 NUEVO CAMPO DE MARKETING
        ], $this->mensajesValidacion());

        $validatedData['complemento'] = !empty($validatedData['complemento']) ? strtoupper($validatedData['complemento']) : null;

        $patient->update($validatedData);

        return response()->json([
            'message' => 'Paciente actualizado exitosamente.',
            'patient' => $patient
        ]);
    }

    public function work($id)
    {
        // 🔹 Aquí está la clave: le pedimos a Laravel que traiga al paciente, 
        // JUNTO con sus antecedentes y JUNTO con sus evaluaciones y sesiones.
        $patient = Patient::with(['antecedent', 'evaluations.therapySessions'])
            ->findOrFail($id);

        return response()->json($patient);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        // Obtiene los pacientes del último creado al primero
        $patients = Patient::latest()->get();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $profesiones = [
            'Abogado',
            'Administrador',
            'Actor',
            'Adaptador de sistemas',
            'Agente de seguros',
            'Agricultor',
            'Albañil',
            'Alfombrista',
            'Analista financiero',
            'Ama de casa',
            'Analista de datos',
            'Analista de sistemas',
            'Antropólogo',
            'Arquitecto',
            'Artesano',
            'Aseador',
            'Asistente administrativo',
            'Atleta',
            'Auditor',
            'Auxiliar contable',
            'Auxiliar de enfermería',
            'Barbero',
            'Barman',
            'Bibliotecario',
            'Biólogo',
            'Bombero',
            'Cajero',
            'Carpintero',
            'Carnicero',
            'Chef',
            'Chofer',
            'Científico',
            'Comerciante',
            'Comunicador social',
            'Constructor',
            'Contador',
            'Conserje',
            'Costurera',
            'Cristalero',
            'Dentista',
            'Desarrollador web',
            'Diseñador gráfico',
            'Docente',
            'Domador',
            'Dramaturgo',
            'Economista',
            'Electricista',
            'Empleada doméstica',
            'Enfermero',
            'Entrenador',
            'Especialista en marketing',
            'Estadístico',
            'Esteticista',
            'Estudiante',
            'Farmacéutico',
            'Ferrero',
            'Físico',
            'Fisioterapeuta',
            'Fotógrafo',
            'Fruticultor',
            'Gasfitero',
            'Gerente',
            'Geólogo',
            'Guía turístico',
            'Herrero',
            'Historiador',
            'Host',
            'Horticultor',
            'Ingeniero civil',
            'Ingeniero comercial',
            'Ingeniero de sistemas',
            'Ingeniero industrial',
            'Instructor',
            'Investigador',
            'Jardinero',
            'Jefe de almacén',
            'Joyero',
            'Jornalero',
            'Jubilado',
            'Lavandero',
            'Leñador',
            'Licenciado en enfermería',
            'Luthier',
            'Mecánico',
            'Médico',
            'Mensajero',
            'Minero',
            'Modista',
            'Músico',
            'Notario',
            'Obrero',
            'oficinista',
            'otro',
            'Odontólogo',
            'Operador de maquinaria',
            'Panadero',
            'Paramédico',
            'Pastor',
            'Pediatra',
            'Peluquero',
            'Periodista',
            'Pescador',
            'Pintor',
            'Plomero',
            'Policía',
            'Profesor',
            'Programador',
            'Psicólogo',
            'Recepcionista',
            'Relaciones públicas',
            'Sastre',
            'Secretaria',
            'Soldador',
            'Supervisor',
            'Taxista',
            'Técnico de laboratorio',
            'Técnico en computación',
            'Terapista ocupacional',
            'Topógrafo',
            'Trabajador social',
            'Vendedor',
            'Veterinario',
            'Yesero',
            'Zapatero',
        ];

        return view('patients.create', compact('profesiones'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                // Unique compuesto
                Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('ci', $request->ci)
                        ->where('complemento', $request->complemento);
                }),
            ],
            'complemento' => [
                'nullable',
                'string',
                'max:3',
                'regex:/^[A-Za-z0-9]{0,3}$/'
            ],
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'sexo' => 'required|string|max:10',
            'ocupacion' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'domicilio' => 'nullable|string|max:255',
            'estado_civil' => 'required|string|max:50',
            'celular' => 'nullable|string|max:20',
            'persona_referencia' => 'nullable|string|max:255',
            'celular_referencia' => 'nullable|string|max:20',
            'centro_procendencia' => 'nullable|string|max:255',
            'responsable_centro' => 'nullable|string|max:255',
        ], [
            'complemento.regex' => 'El complemento solo puede contener hasta 3 caracteres alfanuméricos.',
            'ci.unique' => 'Ya existe un paciente con este CI y complemento.',
        ]);

        $validatedData['user_id'] = auth()->id();

        Patient::create($validatedData);

        return redirect()->route('patients.index')->with('success', 'Paciente creado exitosamente.');
    }


    public function edit(Patient $patient)
    {
        $profesiones = [
            'Abogado',
            'Administrador',
            'Actor',
            'Adaptador de sistemas',
            'Agente de seguros',
            'Agricultor',
            'Albañil',
            'Alfombrista',
            'Analista financiero',
            'Ama de casa',
            'Analista de datos',
            'Analista de sistemas',
            'Antropólogo',
            'Arquitecto',
            'Artesano',
            'Aseador',
            'Asistente administrativo',
            'Atleta',
            'Auditor',
            'Auxiliar contable',
            'Auxiliar de enfermería',

            'Barbero',
            'Barman',
            'Bibliotecario',
            'Biólogo',
            'Bombero',

            'Cajero',
            'Carpintero',
            'Carnicero',
            'Chef',
            'Chofer',
            'Científico',
            'Comerciante',
            'Comunicador social',
            'Constructor',
            'Contador',
            'Conserje',
            'Costurera',
            'Cristalero',

            'Dentista',
            'Desarrollador web',
            'Diseñador gráfico',
            'Docente',
            'Domador',
            'Dramaturgo',
            'Jubilado',

            'Economista',
            'Electricista',
            'Empleada doméstica',
            'Enfermero',
            'Entrenador',
            'Especialista en marketing',
            'Estadístico',
            'Esteticista',
            'Estudiante',

            'Farmacéutico',
            'Ferrero',
            'Físico',
            'Fisioterapeuta',
            'Fotógrafo',
            'Fruticultor',

            'Gasfitero',
            'Gerente',
            'Geólogo',
            'Guía turístico',

            'Herrero',
            'Historiador',
            'Host',
            'Horticultor',

            'Ingeniero civil',
            'Ingeniero comercial',
            'Ingeniero de sistemas',
            'Ingeniero industrial',
            'Instructor',
            'Investigador',

            'Jardinero',
            'Jefe de almacén',
            'Joyero',
            'Jornalero',

            'Lavandero',
            'Leñador',
            'Licenciado en enfermería',
            'Luthier',

            'Mecánico',
            'Médico',
            'Mensajero',
            'Minero',
            'Modista',
            'Músico',

            'Notario',

            'Obrero',
            'oficinista',
            'otro',
            'Odontólogo',
            'Operador de maquinaria',

            'Panadero',
            'Paramédico',
            'Pastor',
            'Pediatra',
            'Peluquero',
            'Periodista',
            'Pescador',
            'Pintor',
            'Plomero',
            'Policía',
            'Profesor',
            'Programador',
            'Psicólogo',

            'Recepcionista',
            'Relaciones públicas',

            'Sastre',
            'Secretaria',
            'Soldador',
            'Supervisor',

            'Taxista',
            'Técnico de laboratorio',
            'Técnico en computación',
            'Terapista ocupacional',
            'Topógrafo',
            'Trabajador social',

            'Vendedor',
            'Veterinario',

            'Yesero',
            'Zapatero',
        ];
        return view('patients.edit', compact('patient', 'profesiones'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('patients')->where(function ($query) use ($request) {
                    return $query->where('ci', $request->ci)
                        ->where('complemento', $request->complemento);
                })->ignore($patient->id),
            ],
            'complemento' => [
                'nullable',
                'string',
                'max:3',
                'regex:/^[A-Za-z0-9]{0,3}$/'
            ],
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'sexo' => 'required|string|max:10',
            'ocupacion' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'domicilio' => 'nullable|string|max:255',
            'estado_civil' => 'required|string|max:50',
            'celular' => 'nullable|string|max:20',
            'persona_referencia' => 'nullable|string|max:255',
            'celular_referencia' => 'nullable|string|max:20',
            'centro_procendencia' => 'nullable|string|max:255',
            'responsable_centro' => 'nullable|string|max:255',
        ], [
            'complemento.regex' => 'El complemento solo puede contener hasta 3 caracteres alfanuméricos.',
            'ci.unique' => 'Ya existe un paciente con este CI y complemento.',
        ]);

        $patient->update($validatedData);

        return redirect()->route('patients.work', $patient->id)->with('success', 'Paciente actualizado exitosamente.');
    }

    public function work($id)
    {
        $patient = Patient::with('antecedent')->findOrFail($id);
        return view('patients.work', compact('patient'));
    }
}

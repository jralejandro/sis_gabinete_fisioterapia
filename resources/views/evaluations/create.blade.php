@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            {{-- Encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-notes-medical me-2"></i> Nueva Evaluación Inicial
                </h2>
                <a href="{{ route('patients.work', $patient->id) }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver al Paciente
                </a>
            </div>

            {{-- Tarjeta principal --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold text-secondary ">
                        <i class="fa-solid fa-file-medical "></i> Complete los datos de la evaluación
                    </h5>
                </div>

                <form action="{{ route('evaluations.store', $patient->id) }}" method="POST" autocomplete="off"
                    class="pb-3">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    <div class="card-body">
                        {{-- REFERENCIA --}}
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-file-lines"></i>
                            Datos de Referencia
                        </h5>
                        <div class="row g-4">
                            <!-- Fecha -->
                            <div class="col-md-3">
                                <label for="fecha" class="form-label fw-semibold">Fecha de Evaluación:</label>
                                <input type="date" name="fecha" id="fecha"
                                    class="form-control rounded-3 @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha') }}">
                                @error('fecha')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Médico Referencia -->
                            <div class="col-md-3">
                                <label for="medico_referencia" class="form-label fw-semibold">Médico de Referencia</label>
                                <input type="text" name="medico_referencia" id="medico_referencia"
                                    class="form-control rounded-3" placeholder="Ingrese el nombre del médico"
                                    value="{{ old('medico_referencia') }}">
                                @error('medico_referencia')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Centro referencia -->
                            <div class="col-md-3">
                                <label for="centro_referencia" class="form-label fw-semibold">Centro de Referencia</label>
                                <input name="centro_referencia" id="centro_referencia" class="form-control rounded-3"
                                    placeholder="Ingrese el centro" value="{{ old('centro_referencia') }}">
                                @error('centro_referencia')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Responsable -->
                            <div class="col-md-3">
                                <label for="responsable_evaluacion" class="form-label fw-semibold">Responsable</label>
                                <select name="responsable_evaluacion" id="responsable_evaluacion"
                                    class="form-select rounded-3 @error('responsable_evaluacion') is-invalid @enderror">
                                    <option value="">Seleccione</option>
                                    @foreach ($responsables as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('responsable_evaluacion') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nombres }} {{ $user->apellido_paterno }}
                                            {{ $user->apellido_materno }} ({{ $user->cargo }})
                                        </option>
                                    @endforeach
                                </select>

                                @error('responsable_evaluacion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Diagnóstico Médico -->
                            <div class="col-md-3">
                                <label for="diagnostico_medico" class="form-label fw-semibold">Diagnóstico Médico</label>

                                <input list="lista_diagnosticos" name="diagnostico_medico" id="diagnostico_medico"
                                    class="form-control rounded-3 @error('diagnostico_medico') is-invalid @enderror"
                                    value="{{ old('diagnostico_medico') }}" placeholder="Seleccione o escriba">

                                <datalist id="lista_diagnosticos">
                                    @foreach ($diagnosticos as $diag)
                                        <option value="{{ $diag }}"></option>
                                    @endforeach
                                    <option value="Otro"></option>
                                </datalist>

                                @error('diagnostico_medico')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            {{-- OTRO DIAGNÓSTICO --}}
                            {{-- OTRO DIAGNÓSTICO --}}
                            <div class="col-md-3" id="div_otro_diagnostico"
                                style="{{ old('diagnostico_medico') == 'Otro' ? 'display:block' : 'display:none' }}">
                                <label class="form-label fw-semibold">Especifique otro diagnóstico</label>
                                <input type="text" name="diagnostico_medico_otro"
                                    class="form-control rounded-3 @error('diagnostico_medico_otro') is-invalid @enderror"
                                    value="{{ old('diagnostico_medico_otro') }}" placeholder="Ingrese el diagnóstico">

                                @error('diagnostico_medico_otro')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Costo Evaluación -->
                            <div class="col-md-3">
                                <label for="costo_evaluacion" class="form-label fw-semibold">Costo Evaluación (Bs.)</label>
                                <input type="number" step="0.01" name="costo_evaluacion" id="costo_evaluacion"
                                    class="form-control rounded-3 @error('costo_evaluacion') is-invalid @enderror"
                                    value="{{ old('costo_evaluacion', '30.00') }}" placeholder="Ej. 30.00">

                                @error('costo_evaluacion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- ANAMNESIS --}}
                        <hr class="my-4">
                        <h5 class="text-primary fw-semibold mt-3 mb-3">
                            <i class="fa-solid fa-user-pen me-2"></i> Anamnesis
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="motivo_consulta" class="form-label fw-semibold">Motivo de consulta</label>
                                <textarea name="motivo_consulta" id="motivo_consulta"
                                    class="form-control rounded-3 @error('motivo_consulta') is-invalid @enderror" rows="3"
                                    placeholder="Describa el motivo de consulta...">{{ old('motivo_consulta') }}</textarea>
                                @error('motivo_consulta')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="historia_enfermedad_actual" class="form-label fw-semibold">Historia de la
                                    enfermedad actual</label>
                                <textarea name="historia_enfermedad_actual" id="historia_enfermedad_actual"
                                    class="form-control rounded-3 @error('historia_enfermedad_actual') is-invalid @enderror" rows="3"
                                    placeholder="Describa la evolución de los síntomas...">{{ old('historia_enfermedad_actual') }}</textarea>
                                @error('historia_enfermedad_actual')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- 🔸 SECCIÓN GINECO-OBSTÉTRICA (solo para mujeres) --}}
                        @if ($patient->sexo === 'Femenino')
                            <hr class="my-4">
                            <h5 class="text-danger fw-semibold mb-3">
                                <i class="fa-solid fa-venus me-2"></i> Antecedentes Gineco-Obstétricos
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label for="gestaciones" class="form-label fw-semibold">Gestaciones</label>
                                    <input type="number" name="gestaciones" id="gestaciones"
                                        class="form-control rounded-3" value="{{ old('gestaciones') }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="partos" class="form-label fw-semibold">Partos</label>
                                    <input type="number" name="partos" id="partos" class="form-control rounded-3"
                                        value="{{ old('partos') }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="abortos" class="form-label fw-semibold">Abortos</label>
                                    <input type="number" name="abortos" id="abortos" class="form-control rounded-3"
                                        value="{{ old('abortos') }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="cesareas" class="form-label fw-semibold">Cesáreas</label>
                                    <input type="number" name="cesareas" id="cesareas" class="form-control rounded-3"
                                        value="{{ old('cesareas') }}" min="0">
                                </div>

                                <div class="col-md-3">
                                    <label for="fecha_ultima_menstruacion" class="form-label fw-semibold">Última
                                        menstruación</label>
                                    <input type="date" name="fecha_ultima_menstruacion" id="fecha_ultima_menstruacion"
                                        class="form-control rounded-3" value="{{ old('fecha_ultima_menstruacion') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_ultimo_parto" class="form-label fw-semibold">Último parto</label>
                                    <input type="date" name="fecha_ultimo_parto" id="fecha_ultimo_parto"
                                        class="form-control rounded-3" value="{{ old('fecha_ultimo_parto') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_ultima_cesarea" class="form-label fw-semibold">Última
                                        cesárea</label>
                                    <input type="date" name="fecha_ultima_cesarea" id="fecha_ultima_cesarea"
                                        class="form-control rounded-3" value="{{ old('fecha_ultima_cesarea') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_ultimo_aborto" class="form-label fw-semibold">Último aborto</label>
                                    <input type="date" name="fecha_ultimo_aborto" id="fecha_ultimo_aborto"
                                        class="form-control rounded-3" value="{{ old('fecha_ultimo_aborto') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="edad_menarca" class="form-label fw-semibold">Edad de Menarca</label>
                                    <input type="number" name="edad_menarca" id="edad_menarca"
                                        class="form-control rounded-3" value="{{ old('edad_menarca') }}"
                                        placeholder="Ej. 13">
                                </div>
                                <div class="col-md-4">
                                    <label for="patron_catamenial" class="form-label fw-semibold">Patrón
                                        Catamenial</label>
                                    <input type="text" name="patron_catamenial" id="patron_catamenial"
                                        class="form-control rounded-3" value="{{ old('patron_catamenial') }}"
                                        placeholder="Ej. Regular, 28 días">
                                </div>
                                <div class="col-md-5">
                                    <label for="ciclo_menstrual" class="form-label fw-semibold">Ciclo Menstrual</label>
                                    <select name="ciclo_menstrual" id="ciclo_menstrual" class="form-select rounded-3">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="Regular"
                                            {{ old('ciclo_menstrual') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="Irregular"
                                            {{ old('ciclo_menstrual') == 'Irregular' ? 'selected' : '' }}>Irregular
                                        </option>
                                    </select>
                                </div>

                            </div>
                        @endif
                        <hr class="my-4">
                        {{-- EXAMEN FÍSICO --}}
                        <h5 class="text-primary fw-semibold mb-3">
                            <i class="fa-solid fa-heart-pulse me-2"></i> Examen Físico
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label for="frecuencia_cardiaca" class="form-label fw-semibold">Frecuencia Cardíaca
                                    (lpm)</label>
                                <input type="number" step="0.01" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                                    class="form-control rounded-3 @error('frecuencia_cardiaca') is-invalid @enderror"
                                    value="{{ old('frecuencia_cardiaca') }}" placeholder="Ej. 72">
                                @error('frecuencia_cardiaca')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="frecuencia_respiratoria" class="form-label fw-semibold">Frecuencia
                                    Respiratoria (rpm)</label>
                                <input type="number" step="0.01" name="frecuencia_respiratoria"
                                    id="frecuencia_respiratoria"
                                    class="form-control rounded-3 @error('frecuencia_respiratoria') is-invalid @enderror"
                                    value="{{ old('frecuencia_respiratoria') }}" placeholder="Ej. 18">
                                @error('frecuencia_respiratoria')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="presion_arterial" class="form-label fw-semibold">Presión Arterial
                                    (mmHg)</label>
                                <input type="text" name="presion_arterial" id="presion_arterial"
                                    class="form-control rounded-3 @error('presion_arterial') is-invalid @enderror"
                                    value="{{ old('presion_arterial') }}" placeholder="Ej. 120/80">
                                @error('presion_arterial')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="temperatura" class="form-label fw-semibold">Temperatura (°C)</label>
                                <input type="number" step="0.1" name="temperatura" id="temperatura"
                                    class="form-control rounded-3 @error('temperatura') is-invalid @enderror"
                                    value="{{ old('temperatura') }}" placeholder="Ej. 36.5">
                                @error('temperatura')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- EVALUACIÓN KINÉSICA --}}
                        <h5 class="text-primary fw-semibold mb-3">
                            <i class="fa-solid fa-person-walking me-2"></i> Evaluación Kinésica Específica
                        </h5>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="caracteristicas_dolor" class="form-label fw-semibold">Características del
                                    Dolor</label>
                                <textarea name="caracteristicas_dolor" id="caracteristicas_dolor"
                                    class="form-control rounded-3 @error('caracteristicas_dolor') is-invalid @enderror" rows="2"
                                    placeholder="Inicio de los síntomas...">{{ old('caracteristicas_dolor') }}</textarea>
                                @error('caracteristicas_dolor')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="inspeccion" class="form-label fw-semibold">Observaciones</label>
                                <textarea name="inspeccion" id="inspeccion" class="form-control rounded-3 @error('inspeccion') is-invalid @enderror"
                                    rows="2" placeholder="Inicio de los síntomas...">{{ old('inspeccion') }}</textarea>
                                @error('inspeccion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="evaluacion_postural" class="form-label fw-semibold">Evaluación
                                    Postural</label>
                                <textarea name="evaluacion_postural" id="evaluacion_postural"
                                    class="form-control rounded-3 @error('evaluacion_postural') is-invalid @enderror" rows="2"
                                    placeholder="Inicio de los síntomas...">{{ old('evaluacion_postural') }}</textarea>
                                @error('evaluacion_postural')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- PALPACIÓN / MOVILIZACIÓN --}}
                            <div class="col-12 mt-0">
                                <fieldset class="border rounded-3 p-4 position-relative mt-4 shadow-sm"
                                    style="border-width: 2px !important;">

                                    <legend class="float-none w-auto px-3 fw-semibold mb-0" style="font-size: 1rem;">
                                        Palpación / Movilización
                                    </legend>

                                    <div class="row g-4">

                                        {{-- Contractura Muscular --}}
                                        <div class="col-md-3">
                                            <label for="contractura_muscular" class="form-label fw-semibold">Contractura
                                                Muscular</label>
                                            <textarea name="contractura_muscular" id="contractura_muscular"
                                                class="form-control rounded-3 @error('contractura_muscular') is-invalid @enderror" rows="2"
                                                placeholder="Ej. Leve, moderada...">{{ old('contractura_muscular') }}</textarea>
                                            @error('contractura_muscular')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Puntos Dolorosos --}}
                                        <div class="col-md-3">
                                            <label for="puntos_dolorosos" class="form-label fw-semibold">Puntos
                                                Dolorosos</label>
                                            <textarea name="puntos_dolorosos" id="puntos_dolorosos"
                                                class="form-control rounded-3 @error('puntos_dolorosos') is-invalid @enderror" rows="2"
                                                placeholder="Ej. Trapecio, lumbar...">{{ old('puntos_dolorosos') }}</textarea>
                                            @error('puntos_dolorosos')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Puntos Gatillo --}}
                                        <div class="col-md-3">
                                            <label for="puntos_gatillo" class="form-label fw-semibold">Puntos
                                                Gatillo</label>
                                            <textarea name="puntos_gatillo" id="puntos_gatillo"
                                                class="form-control rounded-3 @error('puntos_gatillo') is-invalid @enderror" rows="2"
                                                placeholder="Ej. Trapecio sup...">{{ old('puntos_gatillo') }}</textarea>
                                            @error('puntos_gatillo')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Retracción / Acortamiento --}}
                                        <div class="col-md-3">
                                            <label for="retreaccion_acortamiento"
                                                class="form-label fw-semibold">Retracción/Acortamiento</label>
                                            <textarea name="retreaccion_acortamiento" id="retreaccion_acortamiento"
                                                class="form-control rounded-3 @error('retreaccion_acortamiento') is-invalid @enderror" rows="2"
                                                placeholder="Ej. Isquiotibiales...">{{ old('retreaccion_acortamiento') }}</textarea>
                                            @error('retreaccion_acortamiento')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Tono Muscular --}}
                                        <div class="col-md-3">
                                            <h6 class="fw-semibold mb-2">Tono Muscular</h6>

                                            <select name="tono" id="tono"
                                                class="form-select rounded-3 @error('tono') is-invalid @enderror">

                                                <option value="">Seleccione...</option>

                                                <option value="Hipotono"
                                                    {{ old('tono', $evaluation->tono ?? '') == 'Hipotono' ? 'selected' : '' }}>
                                                    Hipotono
                                                </option>

                                                <option value="Hipertono"
                                                    {{ old('tono', $evaluation->tono ?? '') == 'Hipertono' ? 'selected' : '' }}>
                                                    Hipertono
                                                </option>

                                                <option value="Normal"
                                                    {{ old('tono', $evaluation->tono ?? '') == 'Normal' ? 'selected' : '' }}>
                                                    Normal
                                                </option>

                                            </select>


                                            @error('tono')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror

                                            <input type="text" name="tono_detalle"
                                                class="form-control mt-2 rounded-3 @error('tono_detalle') is-invalid @enderror"
                                                placeholder="Detalle adicional..." value="{{ old('tono_detalle') }}">

                                            @error('tono_detalle')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>


                                        {{-- Trofismo --}}
                                        <div class="col-md-3">
                                            <h6 class="fw-semibold mb-2">Trofismo</h6>

                                            <select name="trofismo" id="trofismo"
                                                class="form-select rounded-3 @error('trofismo') is-invalid @enderror">
                                                <option value="">Seleccione...</option>

                                                <option value="Hipertrofia"
                                                    {{ old('trofismo') == 'Hipertrofia' ? 'selected' : '' }}>
                                                    Hipertrofia
                                                </option>

                                                <option value="Hipotrofia"
                                                    {{ old('trofismo') == 'Hipotrofia' ? 'selected' : '' }}>
                                                    Hipotrofia
                                                </option>

                                                <option value="Normal"
                                                    {{ old('trofismo') == 'Normal' ? 'selected' : '' }}>
                                                    Normal
                                                </option>
                                            </select>

                                            @error('trofismo')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror

                                            <input type="text" name="trofismo_detalle"
                                                class="form-control mt-2 rounded-3 @error('trofismo_detalle') is-invalid @enderror"
                                                placeholder="Detalle adicional..." value="{{ old('trofismo_detalle') }}">

                                            @error('trofismo_detalle')
                                                <div class="text-danger mt-1">
                                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>



                                        {{-- Evaluación de la Marcha --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Evaluación de la Marcha</label>
                                            <textarea name="evaluacion_marcha" class="form-control rounded-3 @error('evaluacion_marcha') is-invalid @enderror"
                                                rows="2" placeholder="Describa patrón, alteraciones...">{{ old('evaluacion_marcha') }}</textarea>
                                            @error('evaluacion_marcha')
                                                <div class="text-danger mt-1"><i
                                                        class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Balance Articular --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Balance Articular</label>
                                            <textarea name="balance_articular" class="form-control rounded-3 @error('balance_articular') is-invalid @enderror"
                                                rows="2" placeholder="Limitaciones, dolor...">{{ old('balance_articular') }}</textarea>
                                            @error('balance_articular')
                                                <div class="text-danger mt-1"><i
                                                        class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Balance Muscular --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Balance Muscular</label>
                                            <textarea name="balance_muscular" class="form-control rounded-3 @error('balance_muscular') is-invalid @enderror"
                                                rows="2" placeholder="Grado muscular, compensaciones...">{{ old('balance_muscular') }}</textarea>
                                            @error('balance_muscular')
                                                <div class="text-danger mt-1"><i
                                                        class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Sensibilidad --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Sensibilidad</label>
                                            <textarea name="sensibilidad" class="form-control rounded-3 @error('sensibilidad') is-invalid @enderror"
                                                rows="2" placeholder="Táctil, térmica, dolorosa...">{{ old('sensibilidad') }}</textarea>
                                            @error('sensibilidad')
                                                <div class="text-danger mt-1"><i
                                                        class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    </div>
                                </fieldset>
                            </div>


                        </div>



                        <hr class="my-4">

                        {{-- INSPECCIÓN Y COMPLEMENTARIOS --}}
                        <h5 class="text-primary fw-semibold mb-3">
                            <i class="fa-solid fa-eye me-2"></i>Exámenes Complementarios
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label for="examenes_complementarios" class="form-label fw-semibold">Exámenes
                                    Complementarios</label>
                                <textarea name="examenes_complementarios" id="examenes_complementarios"
                                    class="form-control rounded-3 @error('examenes_complementarios') is-invalid @enderror" rows="3"
                                    placeholder="Resultados de estudios adicionales...">{{ old('examenes_complementarios') }}</textarea>
                                @error('examenes_complementarios')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- DIAGNÓSTICO Y PLAN --}}
                        <h5 class="text-primary fw-semibold mb-3">
                            <i class="fa-solid fa-clipboard-check me-2"></i> Diagnóstico y Plan de Tratamiento
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="diagnostico_kinesico" class="form-label fw-semibold">Diagnóstico
                                    Kinésico</label>
                                <textarea name="diagnostico_kinesico" id="diagnostico_kinesico"
                                    class="form-control rounded-3 @error('diagnostico_kinesico') is-invalid @enderror" rows="3"
                                    placeholder="Describa el diagnóstico kinésico...">{{ old('diagnostico_kinesico') }}</textarea>
                                @error('diagnostico_kinesico')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="objetivos_tratamiento" class="form-label fw-semibold">Objetivos del
                                    Tratamiento</label>
                                <textarea name="objetivos_tratamiento" id="objetivos_tratamiento"
                                    class="form-control rounded-3 @error('objetivos_tratamiento') is-invalid @enderror" rows="3"
                                    placeholder="Metas terapéuticas a corto y largo plazo...">{{ old('objetivos_tratamiento') }}</textarea>
                                @error('objetivos_tratamiento')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="plan_tratamiento" class="form-label fw-semibold">Plan de Tratamiento</label>
                                <textarea name="plan_tratamiento" id="plan_tratamiento"
                                    class="form-control rounded-3 @error('plan_tratamiento') is-invalid @enderror" rows="3"
                                    placeholder="Descripción detallada del plan kinésico...">{{ old('plan_tratamiento') }}</textarea>
                                @error('plan_tratamiento')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="card-footer bg-white border-0 text-end mt-3">
                        <a href="{{ route('patients.work', $patient->id) }}" class="btn btn-outline-danger px-4">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-outline-success px-4">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('diagnostico_medico').addEventListener('input', function() {
            const campoOtro = document.getElementById('div_otro_diagnostico');

            // Usamos trim() por si quedan espacios accidentales
            if (this.value.trim().toLowerCase() === 'otro') {
                // CORRECCIÓN AQUÍ: Usar 'block', no 'flex'
                campoOtro.style.display = 'block';
            } else {
                campoOtro.style.display = 'none';
            }
        });
    </script>
@endsection

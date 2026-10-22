            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white fw-semibold py-3">
                    <i class="fa-solid fa-file-medical me-2"></i> Detalles de la Evaluación
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-file-lines"></i>
                            Datos de Referencia
                        </h5>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">fecha</p>
                                <h6 class="fw-semibold mb-0">
                                    {{ $evaluation->fecha ? \Carbon\Carbon::parse($evaluation->fecha)->format('d/m/Y') : '—' }}
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Medico de Referencia</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->medico_referencia ?? '—' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Centro de Referencia</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->centro_referencia ?? '—' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Diagnóstico Médico</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->diagnostico_medico ?? '—' }}</h6>
                            </div>
                        </div>
                        <!-- Diagnóstico Médico Otro (solo si existe) -->
                        @if ($evaluation->diagnostico_medico == 'Otro' && $evaluation->diagnostico_medico_otro)
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Otro Diagnóstico Médico</p>
                                    <h6 class="fw-semibold mb-0">{{ $evaluation->diagnostico_medico_otro }}</h6>
                                </div>
                            </div>
                        @endif

                        <!-- Costo de la Evaluación -->
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Costo Evaluación</p>
                                <h6 class="fw-semibold mb-0">
                                    {{ $evaluation->costo_evaluacion ? number_format($evaluation->costo_evaluacion, 2) . ' Bs.' : '—' }}
                                </h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Responsable Evaluación</p>
                                <h6 class="fw-semibold mb-0">{{ $nombreResponsable }}</h6>
                            </div>
                        </div>
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-user-pen"></i> Anamnesis
                        </h5>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Motivo de Consulta</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->motivo_consulta ?? '—' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Historia de la Enfermedad Actual</p>
                                <h6 class="fw-semibold mb-0 text-justify">
                                    {{ $evaluation->historia_enfermedad_actual ?? '—' }}</h6>
                            </div>
                        </div>
                        {{-- EXAMEN FÍSICO --}}
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-heart-pulse"></i> Examen Físico
                        </h5>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Frecuencia Cardíaca (lpm)</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->frecuencia_cardiaca ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Frecuencia Respiratoria (rpm)</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->frecuencia_respiratoria ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Presión Arterial</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->presion_arterial ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Temperatura (°C)</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->temperatura ?? '—' }}</h6>
                            </div>
                        </div>
                        {{-- EVALUACIÓN KINÉSICA ESPECÍFICA --}}
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-person-walking"></i> Evaluación Kinésica Específica
                        </h5>


                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Características del Dolor</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->caracteristicas_dolor ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Observaciones</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->inspeccion ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Evaluación Postural</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->evaluacion_postural ?? '—' }}</h6>
                            </div>
                        </div>

                        {{-- PALPACIÓN / MOVILIZACIÓN --}}
                        <div class="col-12 mt-0">
                            <fieldset class="border rounded-3 p-4 position-relative mt-4 shadow-sm"
                                style="border-width: 2px !important;">

                                <legend class="float-none w-auto px-3 fw-semibold mb-0" style="font-size: 1rem;">
                                    Palpación / Movilización
                                </legend>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Contractura Muscular</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->contractura_muscular ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Puntos Dolorosos</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->puntos_dolorosos ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Puntos Gatillo</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->puntos_gatillo ?? '—' }}</h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Retracción / Acortamiento</p>
                                            <h6 class="fw-semibold mb-0">
                                                {{ $evaluation->retreaccion_acortamiento ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Tono Muscular</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->tono ?? '—' }}: {{ $evaluation->tono_detalle ?? '—' }}</h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Trofismo</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->trofismo ?? '—' }}: {{ $evaluation->trofismo_detalle ?? '—' }}</h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Evaluación de Marcha</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->evaluacion_marcha ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Balance Articular</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->balance_articular ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Balance Muscular</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->balance_muscular ?? '—' }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <p class="mb-1 text-muted small">Sensibilidad</p>
                                            <h6 class="fw-semibold mb-0">{{ $evaluation->sensibilidad ?? '—' }}</h6>
                                        </div>
                                    </div>
                                    
                                </div>
                            </fieldset>
                        </div>

                        {{-- INSPECCIÓN Y COMPLEMENTARIOS --}}
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-eye"></i> Exámenes Complementarios
                        </h5>
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Exámenes Complementarios</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->examenes_complementarios ?? '—' }}</h6>
                            </div>
                        </div>
                        {{-- DIAGNÓSTICO Y PLAN --}}
                        <h5 class="text-primary fw-semibold">
                            <i class="fa-solid fa-clipboard-check"></i> Diagnóstico y Plan de Tratamiento
                        </h5>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Diagnóstico Kinésico</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->diagnostico_kinesico ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Objetivos del Tratamiento</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->objetivos_tratamiento ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Plan de Tratamiento</p>
                                <h6 class="fw-semibold mb-0">{{ $evaluation->plan_tratamiento ?? '—' }}</h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

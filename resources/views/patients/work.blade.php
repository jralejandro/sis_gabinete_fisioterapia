@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            <!-- MENSAJE DE ÉXITO -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                <script>
                    // Recargar la página después de 1 segundo
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                </script>
            @endif

            <!-- ENCABEZADO -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-stethoscope me-2"></i> Evaluación del Paciente
                </h2>
                <div class="d-flex gap-2">
                    <!-- Botón para abrir modal -->

                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-left me-2"></i> Volver
                    </a>
                    <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#antecedentesModal">
                        <i class="fa-solid fa-notes-medical me-2"></i> Antecedentes
                    </button>
                    <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-outline-warning shadow-sm"
                        title="Editar Paciente">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Paciente
                    </a>
                </div>
            </div>

            <!-- DATOS DEL PACIENTE -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white fw-semibold py-3">
                    <i class="fa-solid fa-user me-2"></i> Datos Generales del Paciente
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Nombre completo</p>
                                <h6 class="fw-semibold mb-0">
                                    {{ $patient->nombres }} {{ $patient->apellido_paterno }}
                                    {{ $patient->apellido_materno }}
                                </h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Cédula de Identidad</p>
                                <h6 class="fw-semibold mb-0">
                                    {{ $patient->ci }}
                                    @if ($patient->complemento)
                                        - {{ $patient->complemento }}
                                    @endif
                                </h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Sexo</p>
                                <h6 class="fw-semibold mb-0">{{ ucfirst($patient->sexo) }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Fecha de nacimiento</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->fecha_nacimiento }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Edad</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->edad ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Estado civil</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->estado_civil ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Ocupación</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->ocupacion ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Teléfono</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->celular ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Domicilio</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->domicilio ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Persona de referencia</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->persona_referencia ?? '—' }}</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Teléfono de referencia</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->celular_referencia ?? '—' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ANTECEDENTES -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white fw-semibold py-3">
                    <i class="fa-solid fa-file-medical me-2"></i> Antecedentes Personales del Paciente
                </div>
                <div class="card-body">
                    @if ($patient->antecedent)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Patológicos</p>
                                    <h6 class="fw-semibold mb-0">{{ $patient->antecedent->patologicos ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Familiares</p>
                                    <h6 class="fw-semibold mb-0">{{ $patient->antecedent->familiares ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">No patológicos</p>
                                    <h6 class="fw-semibold mb-0">{{ $patient->antecedent->no_patologicos ?? '—' }}</h6>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="bi bi-folder2-open me-2"></i> No hay antecedentes registrados.
                        </div>
                    @endif
                </div>
            </div>

            <!-- HISTORIA CLÍNICA -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white fw-semibold py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa-solid fa-book-medical me-2"></i> Historia Clínica del Paciente
                        </div>
                        <a href="{{ route('evaluations.create', $patient->id) }}"
                            class="btn btn-light text-primary border-0 fw-semibold shadow-sm ms-auto">
                            <i class="fa-solid fa-notes-medical me-2"></i> Nueva Evaluación
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($patient->evaluations->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover border rounded-3 overflow-hidden">
                                <thead class="table-primary text-primary bg-opacity-10">
                                    <tr>
                                        <th scope="col" class="fw-semibold">#</th>
                                        <th scope="col" class="fw-semibold">Motivo de Consulta</th>
                                        <th scope="col" class="fw-semibold">Diagnóstico Kinésico</th>
                                        <th scope="col" class="fw-semibold">Fecha</th>
                                        <th scope="col" class="fw-semibold text-center">Sesiones</th>
                                        <th scope="col" class="fw-semibold text-center">Estado</th>
                                        <th scope="col" class="text-end fw-semibold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patient->evaluations as $index => $evaluation)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ Str::limit($evaluation->motivo_consulta, 60) ?? '—' }}</td>
                                            <td>{{ Str::limit($evaluation->diagnostico_kinesico, 60) ?? '—' }}</td>
                                            <td>{{ $evaluation->fecha->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary fw-semibold px-3">
                                                    {{ $evaluation->therapySessions->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $estado = $evaluation->estado;
                                                    $badgeClass = match ($estado) {
                                                        'ACTIVO' => 'bg-success text-white', // verde
                                                        'ABANDONO' => 'bg-warning text-dark', // amarillo
                                                        'ALTA' => 'bg-info text-white', // azul/celeste
                                                        default => 'bg-secondary text-white', // gris para otros
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }} fw-semibold px-3">
                                                    {{ $estado }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('evaluations.edit', ['patient' => $patient->id, 'evaluation' => $evaluation->id]) }}"
                                                        class="btn btn-outline-warning btn-sm" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                                    </a>
                                                    <a href="{{ route('evaluations.therapy-sessions.index', ['patient' => $patient->id, 'evaluation' => $evaluation->id]) }}"
                                                        class="btn btn-outline-success btn-sm" title="Sesiones">
                                                        <i class="fa-solid fa-hospital"></i> Sesiones
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="fa-regular fa-folder-open me-2"></i> No hay evaluaciones registradas aún.
                        </div>
                    @endif
                </div>
            </div>



        </div>
    </main>
    <!-- MODAL DE ANTECEDENTES -->
    <div class="modal fade" id="antecedentesModal" tabindex="-1" aria-labelledby="antecedentesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">

                <!-- ENCABEZADO -->
                <div class="modal-header bg-secondary text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold" id="antecedentesModalLabel">
                        <i class="fa-solid fa-file-waveform me-2"></i>
                        {{ $patient->antecedent ? 'Editar Antecedentes del Paciente' : 'Registrar Antecedentes del Paciente' }}
                    </h5>
                </div>

                <!-- CUERPO -->
                <form method="POST"
                    action="{{ $patient->antecedent ? route('antecedents.update', $patient->antecedent->id) : route('antecedents.store') }}">
                    @csrf
                    @if ($patient->antecedent)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    <div class="modal-body bg-light">
                        <div class="row g-4">
                            <!-- ANTECEDENTES PATOLOGICOS -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Antecedentes Patológicos</label>
                                <textarea class="form-control rounded-3 shadow-sm" name="patologicos" id="patologicos" rows="4">{{ old('patologicos', $patient->antecedent->patologicos ?? '') }}</textarea>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="noRefierePatologicos">
                                    <label class="form-check-label small text-muted" for="noRefierePatologicos">No
                                        refiere</label>
                                </div>
                            </div>

                            <!-- ANTECEDENTES FAMILIARES -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Antecedentes Familiares</label>
                                <textarea class="form-control rounded-3 shadow-sm" name="familiares" id="familiares" rows="4">{{ old('familiares', $patient->antecedent->familiares ?? '') }}</textarea>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="noRefiereFamiliares">
                                    <label class="form-check-label small text-muted" for="noRefiereFamiliares">No
                                        refiere</label>
                                </div>
                            </div>

                            <!-- ANTECEDENTES NO PATOLÓGICOS -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Antecedentes No Patológicos</label>
                                <textarea class="form-control rounded-3 shadow-sm" name="no_patologicos" id="no_patologicos" rows="4">{{ old('no_patologicos', $patient->antecedent->no_patologicos ?? '') }}</textarea>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="noRefiereNoPatologicos">
                                    <label class="form-check-label small text-muted" for="noRefiereNoPatologicos">No
                                        refiere</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PIE DEL MODAL -->
                    <div class="modal-footer bg-white rounded-bottom-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger rounded-3" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-outline-success shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const campos = [{
                        textarea: "patologicos",
                        checkbox: "noRefierePatologicos"
                    },
                    {
                        textarea: "familiares",
                        checkbox: "noRefiereFamiliares"
                    },
                    {
                        textarea: "no_patologicos",
                        checkbox: "noRefiereNoPatologicos"
                    },
                ];

                campos.forEach(({
                    textarea,
                    checkbox
                }) => {
                    const txt = document.getElementById(textarea);
                    const chk = document.getElementById(checkbox);
                    if (!txt || !chk) return;

                    chk.addEventListener("change", () => {
                        if (chk.checked) {
                            txt.value = "No refiere";
                            txt.setAttribute("readonly", true);
                            txt.classList.add("bg-light");
                        } else {
                            txt.removeAttribute("readonly");
                            txt.classList.remove("bg-light");
                            txt.value = "";
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

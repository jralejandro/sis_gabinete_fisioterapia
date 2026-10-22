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
                                <h6 class="fw-semibold mb-0">{{ $patient->ci }}
                                    @if ($patient->complemento)
                                        - {{ $patient->complemento }}
                                    @endif
                                </h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Edad</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->edad }}
                                    años</h6>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-1 text-muted small">Teléfono</p>
                                <h6 class="fw-semibold mb-0">{{ $patient->celular ?? '—' }}</h6>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
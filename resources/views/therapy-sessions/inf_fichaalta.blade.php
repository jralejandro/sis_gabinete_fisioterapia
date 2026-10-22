            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white fw-semibold py-3">
                    <i class="fa-solid fa-file-circle-check me-1"></i> Ficha de Alta
                </div>
                <div class="card-body">
                    @if ($record)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Fecha</p>
                                    <h6 class="fw-semibold mb-0">{{ $record->fecha_alta ?? '—' }}</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Responsable</p>
                                    <h6 class="fw-semibold mb-0">
                                        @if ($record && $record->responsableUser)
                                            <h6 class="fw-semibold mb-0">
                                                {{ $record->responsableUser->nombres }}
                                                {{ $record->responsableUser->apellido_paterno }}
                                                {{ $record->responsableUser->apellido_materno }}
                                                <span
                                                    class="text-muted small">({{ $record->responsableUser->cargo }})</span>
                                            </h6>
                                        @else
                                            <h6 class="fw-semibold mb-0">—</h6>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Evolución</p>
                                    <h6 class="fw-semibold mb-0">{{ $record->evolucion_alta ?? '—' }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Observaciones</p>
                                    <h6 class="fw-semibold mb-0">{{ $record->observaciones_alta ?? '—' }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <p class="mb-1 text-muted small">Recomendaciones</p>
                                    <h6 class="fw-semibold mb-0">{{ $record->recomendaciones_alta ?? '—' }}</h6>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="bi bi-folder2-open me-2"></i> No hay ficha de alta registrada.
                        </div>
                    @endif
                </div>
            </div>
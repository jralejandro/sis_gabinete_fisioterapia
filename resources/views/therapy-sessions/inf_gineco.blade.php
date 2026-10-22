            @if ($evaluation->gynecoObstetricHistory)
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-white fw-semibold py-3" style="background-color:#d63384 !important;">
                        <i class="fa-solid fa-venus me-2"></i> Antecedentes Gineco-Obstétricos
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                $g = $evaluation->gynecoObstetricHistory;
                            @endphp

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Gestaciones</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->gestaciones ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Partos</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->partos ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Abortos</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->abortos ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Cesáreas</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->cesareas ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Menarca</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->edad_menarca ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Catamenio</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->patron_catamenial ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">Ciclo</p>
                                    <h6 class="fw-semibold mb-0">{{ $g->ciclo_menstrual ?? '—' }}</h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">F. Última Menstruación</p>
                                    <h6 class="fw-semibold mb-0">
                                        {{ $g->fecha_ultima_menstruacion ? \Carbon\Carbon::parse($g->fecha_ultima_menstruacion)->format('d/m/Y') : '—' }}
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">F. Último Parto</p>
                                    <h6 class="fw-semibold mb-0">
                                        {{ $g->fecha_ultimo_parto ? \Carbon\Carbon::parse($g->fecha_ultimo_parto)->format('d/m/Y') : '—' }}
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">F. Última Cesárea</p>
                                    <h6 class="fw-semibold mb-0">
                                        {{ $g->fecha_ultima_cesarea ? \Carbon\Carbon::parse($g->fecha_ultima_cesarea)->format('d/m/Y') : '—' }}
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-1 text-muted small">F. Último Aborto</p>
                                    <h6 class="fw-semibold mb-0">
                                        {{ $g->fecha_ultimo_aborto ? \Carbon\Carbon::parse($g->fecha_ultimo_aborto)->format('d/m/Y') : '—' }}
                                    </h6>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
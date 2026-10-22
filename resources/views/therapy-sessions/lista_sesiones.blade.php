            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div
                    class="card-header bg-primary text-white fw-semibold py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-book-medical me-2"></i> Historial de Sesiones de Fisioterapia
                    </div>
                    <span class="text-white small fw-semibold">Total: {{ $sessions->count() }}</span>
                </div>

                <div class="card-body">
                    @if ($sessions->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover border rounded-3 overflow-hidden">
                                <thead class="table-primary text-primary bg-opacity-10">
                                    <tr>
                                        <th scope="col" class="fw-semibold">#</th>
                                        <th scope="col" class="fw-semibold">Fecha</th>
                                        <th scope="col" class="fw-semibold small text-center">Costo</th>
                                        <!-- NUEVA COLUMNA -->
                                        <th scope="col" class="fw-semibold">Responsable</th>
                                        <th scope="col" class="fw-semibold">Qué se trabajó</th>
                                        <th scope="col" class="fw-semibold">Evolución</th>
                                        <th scope="col" class="fw-semibold">Observaciones</th>
                                        <th scope="col" class="text-end fw-semibold">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($sessions as $index => $session)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td>{{ \Carbon\Carbon::parse($session->fecha)->format('d/m/Y') }}</td>

                                            <!-- COLUMNA COSTO -->
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill shadow-sm">
                                                    {{ number_format($session->costo_sesion, 2) }} Bs.
                                                </span>
                                            </td>

                                            <td>
                                                {{ $session->responsable
                                                    ? "{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno} - {$session->responsable->cargo}"
                                                    : '—' }}
                                            </td>

                                            <td>{{ Str::limit($session->que_se_trabajo, 60) }}</td>
                                            <td>{{ Str::limit($session->evolucion, 60) }}</td>
                                            <td>{{ Str::limit($session->observaciones, 60) }}</td>

                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <!-- Editar -->
                                                    <button type="button"
                                                        class="btn btn-outline-warning btn-sm px-3 d-flex align-items-center gap-1 rounded-start-3 shadow-sm"
                                                        title="Editar" data-bs-toggle="modal"
                                                        data-bs-target="#editSessionModal{{ $session->id }}">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                        <span class="d-none d-md-inline">Editar</span>
                                                    </button>

                                                    <!-- Eliminar -->
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm px-3 d-flex align-items-center gap-1 rounded-end-3 shadow-sm"
                                                        onclick="confirmDelete('{{ route('evaluations.therapy-sessions.destroy', ['patient' => $patient->id, 'evaluation' => $evaluation->id, 'session' => $session->id]) }}')">
                                                        <i class="fa-solid fa-trash"></i>
                                                        <span class="d-none d-md-inline">Eliminar</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">
                            <i class="fa-regular fa-folder-open me-2"></i> No hay sesiones registradas aún.
                        </div>
                    @endif
                </div>
            </div>

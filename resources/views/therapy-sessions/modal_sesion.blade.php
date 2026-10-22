    <!-- ========================= -->
    <!-- MODAL CREAR SESIÓN -->
    <!-- ========================= -->
    <div class="modal fade" id="therapy-sessionsModal" tabindex="-1" aria-labelledby="therapySessionsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">

                <!-- ENCABEZADO -->
                <div class="modal-header bg-secondary text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold" id="therapySessionsModalLabel">
                        <i class="fa-solid fa-circle-plus me-2"></i> Registrar Nueva Sesión
                    </h5>
                </div>

                <!-- FORMULARIO -->
                <form method="POST"
                    action="{{ route('evaluations.therapy-sessions.store', ['patient' => $patient->id, 'evaluation' => $evaluation->id]) }}">
                    @csrf
                    <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

                    <div class="modal-body bg-light">
                        <div class="row g-3">

                            <!-- Fecha -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Fecha de la Sesión <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="fecha"
                                    class="form-control rounded-3 @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha', now()->format('Y-m-d')) }}">
                                @error('fecha')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Costo de la Sesión -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Costo de la Sesión (Bs.) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="costo_sesion"
                                    class="form-control rounded-3 @error('costo_sesion') is-invalid @enderror"
                                    value="{{ old('costo_sesion', '30.00') }}" placeholder="Ej. 30.00">
                                @error('costo_sesion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Responsable de la sesión -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Responsable de la Sesión <span
                                        class="text-danger">*</span></label>
                                <select name="responsable_sesion"
                                    class="form-select rounded-3 @error('responsable_sesion') is-invalid @enderror">
                                    <option value="">Selecciona un responsable</option>
                                    @foreach ($responsables as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('responsable_sesion') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nombres }} {{ $user->apellido_paterno }}
                                            {{ $user->apellido_materno }} - {{ $user->cargo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsable_sesion')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Qué se trabajó -->
                            <div class="col-12">
                                <label class="fw-semibold mb-2">Qué se trabajó <span
                                        class="text-danger">*</span></label>
                                <textarea name="que_se_trabajo" rows="3"
                                    class="form-control rounded-3 @error('que_se_trabajo') is-invalid @enderror">{{ old('que_se_trabajo') }}</textarea>
                                @error('que_se_trabajo')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Evolución -->
                            <div class="col-md-6">
                                <label class="fw-semibold mb-2">Evolución</label>
                                <textarea name="evolucion" rows="3" class="form-control rounded-3 @error('evolucion') is-invalid @enderror">{{ old('evolucion') }}</textarea>
                                @error('evolucion')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-6">
                                <label class="fw-semibold mb-2">Observaciones</label>
                                <textarea name="observaciones" rows="3"
                                    class="form-control rounded-3 @error('observaciones') is-invalid @enderror">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- PIE DEL MODAL -->
                    <div class="modal-footer bg-white rounded-bottom-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger rounded-3" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-outline-success rounded-3">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- ========================= -->
    <!-- MODALES EDITAR SESIÓN -->
    <!-- ========================= -->
    @foreach ($sessions as $session)
        <div class="modal fade" id="editSessionModal{{ $session->id }}" tabindex="-1"
            aria-labelledby="editSessionModalLabel{{ $session->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">

                    <!-- ENCABEZADO -->
                    <div class="modal-header bg-secondary text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold" id="editSessionModalLabel{{ $session->id }}">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Editar Sesión
                        </h5>
                    </div>

                    <!-- FORMULARIO -->
                    <form method="POST"
                        action="{{ route('evaluations.therapy-sessions.update', ['patient' => $patient->id, 'evaluation' => $evaluation->id, 'session' => $session->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

                        <div class="modal-body bg-light">
                            <div class="row g-3">

                                <!-- Fecha -->
                                <div class="col-md-4">
                                    <label class="fw-semibold mb-2">Fecha de la Sesión <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="fecha"
                                        class="form-control rounded-3 @error('fecha') is-invalid @enderror"
                                        value="{{ old('fecha', $session->fecha->format('Y-m-d')) }}">
                                    @error('fecha')
                                        <div class="text-danger mt-1"><i
                                                class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Costo de la Sesión -->
                                <div class="col-md-4">
                                    <label class="fw-semibold mb-2">Costo de la Sesión (Bs.) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="costo_sesion"
                                        class="form-control rounded-3 @error('costo_sesion') is-invalid @enderror"
                                        value="{{ old('costo_sesion', $session->costo_sesion ?? '25.00') }}"
                                        placeholder="Ej. 25.00">
                                    @error('costo_sesion')
                                        <div class="text-danger mt-1">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- Responsable de la sesión -->
                                <div class="col-md-4">
                                    <label class="fw-semibold mb-2">Responsable de la Sesión <span
                                            class="text-danger">*</span></label>
                                    <select name="responsable_sesion"
                                        class="form-select rounded-3 @error('responsable_sesion') is-invalid @enderror">
                                        <option value="">Selecciona un responsable</option>
                                        @foreach ($responsables as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('responsable_sesion', $session->responsable_sesion) == $user->id ? 'selected' : '' }}>
                                                {{ $user->nombres }} {{ $user->apellido_paterno }}
                                                {{ $user->apellido_materno }} - {{ $user->cargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsable_sesion')
                                        <div class="text-danger mt-1"><i
                                                class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Qué se trabajó -->
                                <div class="col-12">
                                    <label class="fw-semibold mb-2">Qué se trabajó <span
                                            class="text-danger">*</span></label>
                                    <textarea name="que_se_trabajo" rows="3"
                                        class="form-control rounded-3 @error('que_se_trabajo') is-invalid @enderror">{{ old('que_se_trabajo', $session->que_se_trabajo) }}</textarea>
                                    @error('que_se_trabajo')
                                        <div class="text-danger mt-1"><i
                                                class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Evolución -->
                                <div class="col-md-6">
                                    <label class="fw-semibold mb-2">Evolución</label>
                                    <textarea name="evolucion" rows="3" class="form-control rounded-3 @error('evolucion') is-invalid @enderror">{{ old('evolucion', $session->evolucion) }}</textarea>
                                    @error('evolucion')
                                        <div class="text-danger mt-1"><i
                                                class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Observaciones -->
                                <div class="col-md-6">
                                    <label class="fw-semibold mb-2">Observaciones</label>
                                    <textarea name="observaciones" rows="3"
                                        class="form-control rounded-3 @error('observaciones') is-invalid @enderror">{{ old('observaciones', $session->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="text-danger mt-1"><i
                                                class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <!-- PIE DEL MODAL -->
                        <div class="modal-footer bg-white rounded-bottom-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-danger rounded-3" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-outline-success rounded-3">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endforeach

@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            {{-- 🔹 Encabezado --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                <div class="d-flex align-items-center gap-3">

                    <h2 class="fw-bold mb-0">
                        <i class="fa-solid fa-heart-pulse me-2"></i> Sesiones Terapéuticas
                    </h2>

                    {{-- 🔥 Estado de la Evaluación --}}
                    @php
                        $estado = $evaluation->estado;

                        $estilos = [
                            'ACTIVO' => ['bg' => 'bg-success', 'icon' => 'fa-face-smile', 'text' => 'Activo'],
                            'ALTA' => ['bg' => 'bg-primary', 'icon' => 'fa-circle-check', 'text' => 'Alta'],
                            'ABANDONO' => [
                                'bg' => 'bg-warning',
                                'icon' => 'fa-triangle-exclamation',
                                'text' => 'Abandono',
                            ],
                        ];

                        $e = $estilos[$estado] ?? ['bg' => 'bg-secondary', 'icon' => 'fa-question', 'text' => $estado];
                    @endphp

                    <span class="badge {{ $e['bg'] }} text-white px-3 py-2 rounded-pill d-flex align-items-center"
                        style="font-size: 0.9rem;">
                        <i class="fa-solid {{ $e['icon'] }} me-2"></i>
                        {{ $e['text'] }}
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('patients.work', $patient->id) }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-left me-2"></i> Volver al Paciente
                    </a>

                    <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#therapy-sessionsModal">
                        <i class="fa-solid fa-circle-plus me-1"></i> Nueva Sesión
                    </button>

                    <button class="btn btn-outline-success shadow-sm" data-bs-toggle="modal" data-bs-target="#altaModal">
                        <i class="fa-solid fa-file-circle-check me-1"></i>
                        {{ $record ? 'Editar Ficha de Alta' : 'Registrar Ficha de Alta' }}
                    </button>
                </div>
            </div>


            {{-- 🧍 Información del Paciente --}}
            @include('therapy-sessions.inf_paciente')

            @include('therapy-sessions.inf_fichaalta')

            {{-- 🩺 Información de la Evaluación --}}
            @include('therapy-sessions.inf_evaluacion')

            {{-- 🌸 Antecedentes Gineco-Obstétricos --}}
            @include('therapy-sessions.inf_gineco')

            {{-- 📋 Historial de Sesiones de Fisioterapia --}}
            @include('therapy-sessions.lista_sesiones')

        </div>
    </main>

    <div class="modal fade" id="altaModal" tabindex="-1" aria-labelledby="altaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">

                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold" id="altaModalLabel">
                        <i class="fa-solid fa-file-circle-check me-2"></i>
                        {{ $record ? 'Editar Ficha de Alta' : 'Registrar Ficha de Alta' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="altaForm" method="POST"
                    action="{{ $record ? route('discharge.update', $record->id) : route('discharge.store') }}">
                    @csrf
                    @if ($record)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

                    <div class="modal-body bg-light">
                        <div class="row g-4">

                            <!-- FECHA -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_alta"
                                    class="form-control rounded-3 shadow-sm @error('fecha_alta') is-invalid @enderror"
                                    value="{{ old('fecha_alta', $record->fecha_alta ?? now()->format('Y-m-d')) }}">
                                @error('fecha_alta')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- RESPONSABLE -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Responsable <span class="text-danger">*</span></label>
                                <select name="responsable_alta"
                                    class="form-control rounded-3 shadow-sm @error('responsable_alta') is-invalid @enderror">
                                    <option value="">Seleccione…</option>
                                    @foreach ($responsables as $r)
                                        <option value="{{ $r->id }}"
                                            {{ old('responsable_alta', $record->responsable_alta ?? '') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nombres }} {{ $r->apellido_paterno }} {{ $r->apellido_materno }} —
                                            {{ $r->cargo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsable_alta')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ESTADO -->
                            <div class="col-md-4">
                                <label class="fw-semibold mb-2">Estado <span class="text-danger">*</span></label>

                                @if ($record)
                                    {{-- Mostrar el estado actual de la evaluación --}}
                                    <input type="text" class="form-control rounded-3 shadow-sm"
                                        value="{{ $record->evaluation->estado ?? '—' }}" disabled>
                                    {{-- Enviar el estado actual como hidden para que no se sobrescriba en blanco --}}
                                    <input type="hidden" name="estado" value="{{ $record->evaluation->estado ?? '' }}">
                                @else
                                    {{-- Crear nueva ficha: permitir seleccionar el estado --}}
                                    <select name="estado"
                                        class="form-control rounded-3 shadow-sm @error('estado') is-invalid @enderror">
                                        <option value="">Seleccione…</option>
                                        @foreach (['ALTA', 'ABANDONO'] as $estado)
                                            <option value="{{ $estado }}"
                                                {{ old('estado') == $estado ? 'selected' : '' }}>
                                                {{ $estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('estado')
                                        <div class="text-danger mt-1">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                @endif
                            </div>


                            <!-- EVOLUCIÓN -->
                            <div class="col-md-12">
                                <label class="fw-semibold mb-2">Evolución <span class="text-danger">*</span></label>
                                <textarea name="evolucion_alta" class="form-control rounded-3 shadow-sm @error('evolucion_alta') is-invalid @enderror"
                                    rows="3">{{ old('evolucion_alta', $record->evolucion_alta ?? '') }}</textarea>
                                @error('evolucion_alta')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- OBSERVACIONES -->
                            <div class="col-md-6">
                                <label class="fw-semibold mb-2">Observaciones <span class="text-danger">*</span></label>
                                <textarea name="observaciones_alta"
                                    class="form-control rounded-3 shadow-sm @error('observaciones_alta') is-invalid @enderror" rows="3">{{ old('observaciones_alta', $record->observaciones_alta ?? '') }}</textarea>
                                @error('observaciones_alta')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- RECOMENDACIONES -->
                            <div class="col-md-6">
                                <label class="fw-semibold mb-2">Recomendaciones <span class="text-danger">*</span></label>
                                <textarea name="recomendaciones_alta"
                                    class="form-control rounded-3 shadow-sm @error('recomendaciones_alta') is-invalid @enderror" rows="3">{{ old('recomendaciones_alta', $record->recomendaciones_alta ?? '') }}</textarea>
                                @error('recomendaciones_alta')
                                    <div class="text-danger mt-1"><i
                                            class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-white rounded-bottom-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger rounded-3" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar
                        </button>

                        <button type="button" class="btn btn-outline-success shadow-sm" onclick="confirmAlta()">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>



    @include('therapy-sessions.modal_sesion')

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-danger text-white rounded-top-3">
                    <h5 class="modal-title fw-semibold">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="fs-6 mb-0">
                        ¿Estás seguro de que deseas eliminar esta sesión?
                        <br><strong>Esta acción no se puede deshacer.</strong>
                    </p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold" id="confirmModalLabel">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> Confirmación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0" id="confirmModalMessage">
                        ¿Está seguro de realizar esta acción?
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary rounded-3" id="confirmModalAccept">
                        Confirmar
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        // =======================================================
        // LÓGICA DE MODALES DE CONFIRMACIÓN
        // =======================================================

        // Mostrar modal de confirmación genérico
        function showConfirmModal(message, onConfirm) {
            const modalEl = document.getElementById('confirmModal');
            const modal = new bootstrap.Modal(modalEl);
            document.getElementById('confirmModalMessage').textContent = message;

            const confirmBtn = document.getElementById('confirmModalAccept');

            // Limpiar listeners anteriores y adjuntar el nuevo
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
            const newConfirmBtn = document.getElementById('confirmModalAccept');

            newConfirmBtn.addEventListener('click', function() {
                modal.hide();
                if (onConfirm) onConfirm();
            });

            modal.show();
        }

        // Confirmación antes de guardar ficha de alta
        function confirmAlta() {
            showConfirmModal(
                "Está a punto de registrar la ficha de alta. No podrá eliminarla, solo editarla.",
                function() {
                    document.getElementById('altaForm').submit();
                }
            );
        }

        // Confirmación antes de eliminar sesión
        function confirmDelete(actionUrl) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = actionUrl;
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        }
    </script>


    {{-- LÓGICA CLAVE DE APERTURA DE MODALES POR ERRORES (CORREGIDA) --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // 1. Manejar errores del modal de FICHA DE ALTA (ALTA MODAL)
                @if (session('show_alta_modal'))
                    // Abrir el modal de alta si el controlador lo indicó
                    new bootstrap.Modal(document.getElementById('altaModal')).show();
                @endif

                // 2. Manejar errores del modal de SESIONES (NUEVA O EDICIÓN)

                // Si editing viene como false → error en modal NUEVO (Sesión)
                @if (session('editing') === false)
                    // Solo abrir si NO se está abriendo el modal de Alta
                    if (!{{ session('show_alta_modal', 'false') }}) {
                        new bootstrap.Modal(document.getElementById('therapy-sessionsModal')).show();
                    }
                @endif

                // Si editing trae un ID → error en modal EDITAR (Sesión)
                @if (session('editing') && session('editing') !== false)
                    // Solo abrir si NO se está abriendo el modal de Alta
                    if (!{{ session('show_alta_modal', 'false') }}) {
                        new bootstrap.Modal(document.getElementById('editSessionModal{{ session('editing') }}'))
                            .show();
                    }
                @endif

            });
        </script>
    @endif
@endsection

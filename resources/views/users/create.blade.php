@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            {{-- Encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario
                </h2>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fa-solid fa-arrow-left me-2"></i> Regresar
                </a>
            </div>

            {{-- Tarjeta principal --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title fw-semibold text-secondary mb-0">
                        <i class="fa-solid fa-file-lines me-2"></i> Ingrese los datos solicitados
                    </h5>
                </div>

                <form action="{{ route('users.store') }}" method="POST" autocomplete="off" class="pb-3">
                    @csrf
                    <div class="card-body">
                        <div class="row g-4">

                            <!-- Nombres -->
                            <div class="col-md-4">
                                <label for="nombres" class="form-label fw-semibold">Nombres *</label>
                                <input type="text" name="nombres" id="nombres"
                                    class="form-control rounded-3 @error('nombres') is-invalid @enderror"
                                    value="{{ old('nombres') }}" placeholder="Ingrese nombres">
                                @error('nombres')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Apellido Paterno -->
                            <div class="col-md-4">
                                <label for="apellido_paterno" class="form-label fw-semibold">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" id="apellido_paterno"
                                    class="form-control rounded-3 @error('apellido_paterno') is-invalid @enderror"
                                    value="{{ old('apellido_paterno') }}" placeholder="Ingrese apellido paterno">
                                @error('apellido_paterno')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Apellido Materno -->
                            <div class="col-md-4">
                                <label for="apellido_materno" class="form-label fw-semibold">Apellido Materno</label>
                                <input type="text" name="apellido_materno" id="apellido_materno"
                                    class="form-control rounded-3 @error('apellido_materno') is-invalid @enderror"
                                    value="{{ old('apellido_materno') }}" placeholder="Ingrese apellido materno">
                                @error('apellido_materno')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- CI -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cédula de Identidad *</label>
                                <input type="text" name="ci" id="ci"
                                    class="form-control rounded-3 @error('ci') is-invalid @enderror"
                                    value="{{ old('ci') }}" placeholder="Ej. 12345678"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                                @error('ci')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Complemento CI -->
                            <div class="col-md-2">
                                <label for="complemento" class="form-label fw-semibold">Complemento CI</label>
                                <input type="text" name="complemento" id="complemento"
                                    class="form-control rounded-3 @error('complemento') is-invalid @enderror"
                                    value="{{ old('complemento') }}" placeholder="Ej. 1B" maxlength="3"
                                    oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();">

                                @error('complemento')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                <small class="text-muted">
                                    Solo si tu CI tiene complemento (ej: duplicidad). Caso contrario, dejar vacío.
                                </small>
                            </div>

                            <!-- Celular -->
                            <div class="col-md-2">
                                <label for="nro_celular" class="form-label fw-semibold">Celular *</label>
                                <input type="text" name="nro_celular" id="nro_celular"
                                    class="form-control rounded-3 @error('nro_celular') is-invalid @enderror"
                                    value="{{ old('nro_celular') }}" placeholder="Ej. 70000000">
                                @error('nro_celular')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="col-md-4">
                                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                                <input type="text" name="direccion" id="direccion"
                                    class="form-control rounded-3 @error('direccion') is-invalid @enderror"
                                    value="{{ old('direccion') }}" placeholder="Ingrese dirección">
                                @error('direccion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-4">
                                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" name="email" id="email"
                                    class="form-control rounded-3 @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Cargo -->
                            <div class="col-md-4">
                                <label for="cargo" class="form-label fw-semibold">Cargo *</label>
                                <select name="cargo" id="cargo"
                                    class="form-select rounded-3 @error('cargo') is-invalid @enderror">
                                    <option value="" disabled selected>Seleccione un cargo</option>
                                    <option value="Administrador" {{ old('cargo') == 'Administrador' ? 'selected' : '' }}>
                                        Administrador</option>
                                    <option value="Fisioterapeuta"
                                        {{ old('cargo') == 'Fisioterapeuta' ? 'selected' : '' }}>Fisioterapeuta</option>
                                    <option value="Interno" {{ old('cargo') == 'Interno' ? 'selected' : '' }}>Interno
                                    </option>
                                </select>
                                @error('cargo')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Nivel académico -->
                            <div class="col-md-4">
                                <label for="nivel_academico" class="form-label fw-semibold">Nivel Académico</label>
                                <select name="nivel_academico" id="nivel_academico"
                                    class="form-select rounded-3 @error('nivel_academico') is-invalid @enderror">
                                    <option value="" disabled {{ old('nivel_academico') ? '' : 'selected' }}>
                                        Seleccione</option>
                                    <option>Bachiller</option>
                                    <option>Universitario</option>
                                    <option>Egresado</option>
                                    <option>Licenciado</option>
                                    <option>Doctor</option>
                                    <option>Técnico Medio</option>
                                    <option>Técnico Superior</option>
                                    <option>Especialista</option>
                                    <option>Magíster</option>
                                    <option>PhD</option>
                                </select>
                                @error('nivel_academico')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Profesión -->
                            <div class="col-md-4">
                                <label for="profesion" class="form-label fw-semibold">Profesión</label>
                                <input type="text" name="profesion" id="profesion"
                                    class="form-control rounded-3 @error('profesion') is-invalid @enderror"
                                    value="{{ old('profesion') }}" placeholder="Ej. Médico, Ingeniero">
                                @error('profesion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-4">
                                <label for="password" class="form-label fw-semibold">Contraseña *</label>
                                <input type="password" name="password" id="password"
                                    class="form-control rounded-3 @error('password') is-invalid @enderror"
                                    placeholder="Ingrese contraseña">

                                {{-- Mensaje de error de Laravel para password --}}
                                @error('password')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Contraseña
                                    *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control rounded-3 @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Repita la contraseña">

                                {{-- Mensaje de error de Laravel para confirmación de password --}}
                                @error('password_confirmation')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 text-end mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-danger px-4">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>

                        <button type="submit" class="btn btn-outline-success shadow-sm px-4">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- JS para convertir automáticamente complemento a mayúsculas --}}
    <script>
        document.getElementById('complemento').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection

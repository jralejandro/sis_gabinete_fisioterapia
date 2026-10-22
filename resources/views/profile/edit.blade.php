@extends('layout.admin')

@section('content')
<main class="app-main bg-light py-4">
    <div class="container-fluid">

        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="fw-bold mb-0">
                <i class="fa-solid fa-id-card me-2"></i> Editar Perfil
            </h2>
            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> Regresar
            </a>
        </div>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Tarjeta principal --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-semibold text-secondary mb-0">
                    <i class="fa-solid fa-user-pen me-2"></i> Actualizar datos personales
                </h5>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" autocomplete="off" class="pb-3">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row g-4">

                        <!-- Nombres -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nombres *</label>
                            <input type="text" name="nombres"
                                class="form-control rounded-3 @error('nombres') is-invalid @enderror"
                                value="{{ old('nombres', auth()->user()->nombres) }}" disabled >
                            {{-- Mensaje de error debajo del input con icono --}}
                            @error('nombres')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno"
                                class="form-control rounded-3 @error('apellido_paterno') is-invalid @enderror"
                                value="{{ old('apellido_paterno', auth()->user()->apellido_paterno) }}" disabled>
                            @error('apellido_paterno')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Apellido Materno -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" name="apellido_materno"
                                class="form-control rounded-3 @error('apellido_materno') is-invalid @enderror"
                                value="{{ old('apellido_materno', auth()->user()->apellido_materno) }}" disabled>
                            @error('apellido_materno')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- CI -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cédula de Identidad *</label>
                            <input type="text" name="ci" class="form-control rounded-3"
                                value="{{ old('ci', auth()->user()->ci) }}" disabled>
                        </div>

                        <!-- Complemento -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Complemento</label>
                            <input type="text" name="complemento" class="form-control rounded-3"
                                style="text-transform: uppercase"
                                value="{{ old('complemento', auth()->user()->complemento) }}" disabled>
                        </div>

                        <!-- Celular -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Celular *</label>
                            <input type="text" name="nro_celular"
                                class="form-control rounded-3 @error('nro_celular') is-invalid @enderror"
                                value="{{ old('nro_celular', auth()->user()->nro_celular) }}" >
                            @error('nro_celular')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Dirección</label>
                            <input type="text" name="direccion"
                                class="form-control rounded-3 @error('direccion') is-invalid @enderror"
                                value="{{ old('direccion', auth()->user()->direccion) }}">
                            @error('direccion')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="email"
                                class="form-control rounded-3 @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}" >
                            @error('email')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Cargo -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo</label>
                            <input type="text" class="form-control rounded-3" value="{{ auth()->user()->cargo }}" disabled>
                        </div>

                        <!-- Nivel académico -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nivel Académico</label>
                            <select name="nivel_academico"
                                class="form-select rounded-3 @error('nivel_academico') is-invalid @enderror">
                                <option value="">Seleccione</option>
                                @foreach (['Bachiller', 'Universitario', 'Egresado', 'Licenciado', 'Doctor', 'Técnico Medio', 'Técnico Superior', 'Especialista', 'Magíster', 'PhD'] as $nivel)
                                    <option value="{{ $nivel }}"
                                        {{ old('nivel_academico', auth()->user()->nivel_academico) == $nivel ? 'selected' : '' }}>
                                        {{ $nivel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nivel_academico')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Profesión -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Profesión</label>
                            <input type="text" name="profesion"
                                class="form-control rounded-3 @error('profesion') is-invalid @enderror"
                                value="{{ old('profesion', auth()->user()->profesion) }}">
                            @error('profesion')
                                <div class="text-danger mt-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="card-footer bg-white border-0 text-end mt-3">
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-danger px-4">
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

{{-- Auto uppercase --}}
<script>
    document.querySelector("input[name='complemento']").addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>

@endsection

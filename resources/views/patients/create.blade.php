@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            {{-- Encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-user-plus me-2"></i> Nuevo Paciente
                </h2>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary shadow-sm">
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

                <form action="{{ route('patients.store') }}" method="POST" autocomplete="off" class="pb-3">
                    @csrf

                    <div class="card-body">
                        <div class="row g-4">

                            <!-- N° de Carnet -->
                            <div class="col-md-4">
                                <label for="ci" class="form-label fw-semibold">N° de Carnet</label>
                                <input type="text" name="ci" id="ci"
                                    class="form-control rounded-3 @error('ci') is-invalid @enderror"
                                    value="{{ old('ci') }}" placeholder="Ej. 12345678">

                                @error('ci')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Complemento -->
                            <div class="col-md-4">
                                <label for="complemento" class="form-label fw-semibold">Complemento Carnet</label>
                                <input type="text" name="complemento" id="complemento"
                                    class="form-control rounded-3 @error('complemento') is-invalid @enderror"
                                    value="{{ old('complemento') }}" placeholder="Ej. 1B">

                                @error('complemento')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Nombres -->
                            <div class="col-md-4">
                                <label for="nombres" class="form-label fw-semibold">Nombres</label>
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

                            <!-- Sexo -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold d-block">Sexo</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('sexo') is-invalid @enderror" type="radio"
                                        name="sexo" id="sexoM" value="Masculino"
                                        {{ old('sexo') == 'Masculino' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sexoM">Masculino</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('sexo') is-invalid @enderror" type="radio"
                                        name="sexo" id="sexoF" value="Femenino"
                                        {{ old('sexo') == 'Femenino' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sexoF">Femenino</label>
                                </div>

                                @error('sexo')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div class="col-md-4">
                                <label for="fecha_nacimiento" class="form-label fw-semibold">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                    class="form-control rounded-3 @error('fecha_nacimiento') is-invalid @enderror"
                                    value="{{ old('fecha_nacimiento') }}">

                                @error('fecha_nacimiento')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Ocupación -->
                            <div class="col-md-4">
                                <label for="ocupacion" class="form-label fw-semibold">Ocupación</label>

                                <input list="lista_ocupaciones" name="ocupacion" id="ocupacion"
                                    class="form-control rounded-3 @error('ocupacion') is-invalid @enderror"
                                    value="{{ old('ocupacion') }}" placeholder="Seleccione o escriba una profesión...">

                                <datalist id="lista_ocupaciones">
                                    @foreach ($profesiones as $profesion)
                                        <option value="{{ $profesion }}"></option>
                                    @endforeach
                                </datalist>

                                @error('ocupacion')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Domicilio -->
                            <div class="col-md-4">
                                <label for="domicilio" class="form-label fw-semibold">Domicilio</label>
                                <input type="text" name="domicilio" id="domicilio"
                                    class="form-control rounded-3 @error('domicilio') is-invalid @enderror"
                                    value="{{ old('domicilio') }}" placeholder="Ingrese domicilio">

                                @error('domicilio')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Estado Civil -->
                            <div class="col-md-4">
                                <label for="estado_civil" class="form-label fw-semibold">Estado Civil</label>
                                <select name="estado_civil" id="estado_civil"
                                    class="form-select rounded-3 @error('estado_civil') is-invalid @enderror">

                                    <option value="" disabled {{ old('estado_civil') ? '' : 'selected' }}>Seleccione
                                    </option>
                                    <option value="Soltero(a)"
                                        {{ old('estado_civil') == 'Soltero(a)' ? 'selected' : '' }}>Soltero(a)</option>
                                    <option value="Casado(a)" {{ old('estado_civil') == 'Casado(a)' ? 'selected' : '' }}>
                                        Casado(a)</option>
                                    <option value="Viudo(a)" {{ old('estado_civil') == 'Viudo(a)' ? 'selected' : '' }}>
                                        Viudo(a)</option>
                                    <option value="Divorciado(a)"
                                        {{ old('estado_civil') == 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)
                                    </option>
                                    <option value="Unión Libre"
                                        {{ old('estado_civil') == 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
                                </select>

                                @error('estado_civil')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Celular -->
                            <div class="col-md-4">
                                <label for="celular" class="form-label fw-semibold">Celular</label>
                                <input type="number" name="celular" id="celular"
                                    class="form-control rounded-3 @error('celular') is-invalid @enderror"
                                    value="{{ old('celular') }}" placeholder="Ej. 70000000">

                                @error('celular')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Persona de Referencia -->
                            <div class="col-md-4">
                                <label for="persona_referencia" class="form-label fw-semibold">Persona de
                                    Referencia</label>
                                <input type="text" name="persona_referencia" id="persona_referencia"
                                    class="form-control rounded-3 @error('persona_referencia') is-invalid @enderror"
                                    value="{{ old('persona_referencia') }}" placeholder="Nombre de referencia">

                                @error('persona_referencia')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Celular de Referencia -->
                            <div class="col-md-4">
                                <label for="celular_referencia" class="form-label fw-semibold">Celular de
                                    Referencia</label>
                                <input type="number" name="celular_referencia" id="celular_referencia"
                                    class="form-control rounded-3 @error('celular_referencia') is-invalid @enderror"
                                    value="{{ old('celular_referencia') }}" placeholder="Ej. 70000000">

                                @error('celular_referencia')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-end mt-3">
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-danger px-4">
                            <i class="fa-solid fa-xmark me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-outline-success px-4">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

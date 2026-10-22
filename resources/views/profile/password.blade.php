@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="container-fluid">

            {{-- Encabezado --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-key me-2"></i> Cambiar Contraseña
                </h2>

                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fa-solid fa-arrow-left me-2"></i> Regresar
                </a>
            </div>

            {{-- Mensajes generales --}}
            @if (session('success'))
                <div class="alert alert-success shadow-sm">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Tarjeta --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title fw-semibold text-secondary mb-0">
                        <i class="fa-solid fa-lock me-2"></i> Actualizar contraseña
                    </h5>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="pb-3">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row g-4">

                            <!-- Nueva contraseña -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nueva contraseña *</label>
                                <input type="password" name="password"
                                    placeholder="Mínimo 5 caracteres, mayúscula, minúscula y número"
                                    class="form-control rounded-3 @error('password') is-invalid @enderror">
                                {{-- Mensaje de error para nueva contraseña --}}
                                @error('password')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirmar nueva contraseña -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar nueva contraseña *</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control rounded-3 @error('password_confirmation') is-invalid @enderror">
                                {{-- Mensaje de error para confirmación de contraseña --}}
                                @error('password_confirmation')
                                    <div class="text-danger mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    </div>

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
@endsection

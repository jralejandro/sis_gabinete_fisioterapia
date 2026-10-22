@extends('layout.admin')

@section('content')

{{-- Estilos personalizados para consistencia visual --}}
<style>
    .form-select option {
        padding: 10px 15px;
        font-size: 14px;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #6c757d;
    }
    .form-select {
        border-left: none;
        font-weight: 500;
        color: #333;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-radius: 0.375rem;
    }
    .input-group:focus-within .form-select, 
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
    }
</style>

<main class="app-main bg-light py-4">
    <div class="app-content-header mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-file-medical me-2"></i> Reporte Diagnóstico Médico
                </h2>
            </div>

            @if (session('warning'))
                <div class="alert alert-warning mt-3 mb-0 shadow-sm rounded-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('warning') }}
                </div>
            @endif
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <h5 class="card-title mb-0 text-secondary fw-bold">
                                <i class="fa-solid fa-filter me-2"></i> Filtros del Reporte
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('statistics.diagnosticos.pdf') }}" method="GET">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="year" class="form-label fw-semibold text-secondary">Año de Gestión</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-regular fa-calendar"></i>
                                            </span>
                                            <select name="year" id="year" class="form-select ps-3" required>
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($years as $y)
                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                                        {{ $y }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="mes" class="form-label fw-semibold text-secondary">Mes (Opcional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-regular fa-calendar-days"></i>
                                            </span>
                                            <select name="mes" id="mes" class="form-select ps-3">
                                                <option value="">-- Todo el año (Anual) --</option>
                                                @php
                                                    $meses = [
                                                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                                    ];
                                                @endphp
                                                @foreach ($meses as $num => $nom)
                                                    <option value="{{ $num }}">{{ $nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light mt-4 mb-4 border d-flex align-items-start gap-3" role="alert">
                                    <i class="fa-solid fa-circle-info text-primary mt-1 fs-5"></i>
                                    <div style="font-size: 13px; color: #555;">
                                        <strong>Opciones de Generación:</strong><br>
                                        • Deje el mes en <em>"Todo el año"</em> para generar el consolidado anual.<br>
                                        • Seleccione un mes específico para ver el detalle de pacientes de ese periodo.
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm hover-scale">
                                    <i class="fa-solid fa-file-pdf me-2"></i> Generar Reporte PDF
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
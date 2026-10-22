@extends('layout.admin')

@section('content')

{{-- Estilos personalizados para mejorar el Select --}}
<style>
    /* Dar espacio y altura a las opciones desplegables */
    .form-select option {
        padding: 10px 15px; /* Espacio interno en cada opción */
        font-size: 14px;
    }
    
    /* Input Group más elegante */
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #6c757d;
    }
    
    /* El Select en sí mismo */
    .form-select {
        border-left: none; /* Quitar borde izquierdo para unir con el icono */
        font-weight: 500;
        color: #333;
    }
    
    /* Foco: Borde azul suave y sombra */
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
                    <i class="fa-solid fa-chart-line me-2"></i> Reporte Financiero
                </h2>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0 shadow-sm rounded-3">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
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
                            <form action="{{ route('statistics.costos') }}" method="GET">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="year" class="form-label fw-semibold text-secondary">Año Fiscal</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-regular fa-calendar"></i>
                                            </span>
                                            <select name="year" id="year" class="form-select ps-3" required>
                                                @foreach($years as $y)
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
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light mt-4 mb-4 border d-flex align-items-start gap-3" role="alert">
                                    <i class="fa-solid fa-circle-info text-primary mt-1 fs-5"></i>
                                    <div style="font-size: 13px; color: #555;">
                                        <strong>Modo de Reporte:</strong><br>
                                        • Seleccione solo el <strong>Año</strong> para ver el rendimiento mensual (Ene-Dic).<br>
                                        • Seleccione un <strong>Mes</strong> para ver el detalle diario (Día 1-31).
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
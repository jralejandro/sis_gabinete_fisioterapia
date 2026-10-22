@extends('layout.admin')

@section('content')
    <main class="app-main">

        <!-- CABECERA -->
        <div class="app-content-header">
            <div class="container-fluid">
                <h3 class="mb-0 fw-bold">Panel General</h3>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="app-content">
            <div class="container-fluid">

                <!-- ======= Fila 1: TARJETAS RESUMEN ======= -->
                <div class="row g-3 mb-3">
                    @php $cardHeight = '260px'; @endphp

                    <div class="col-lg-3 col-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h5 class="fw-bold mb-0">{{ $totalPacientes ?? 0 }}</h5>
                                    <small class="text-muted">Pacientes</small>
                                </div>
                                <i class="fa-solid fa-users fa-2x text-primary opacity-85"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h5 class="fw-bold mb-0">{{ $totalEvaluaciones ?? 0 }}</h5>
                                    <small class="text-muted">Evaluaciones</small>
                                </div>
                                <i class="fa-solid fa-clipboard-list fa-2x text-success opacity-85"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h5 class="fw-bold mb-0">{{ $totalSesiones ?? 0 }}</h5>
                                    <small class="text-muted">Sesiones</small>
                                </div>
                                <i class="fa-solid fa-heart-pulse fa-2x text-warning opacity-85"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h5 class="fw-bold mb-0">Bs {{ number_format($gananciasPorMes->last(), 2) }}</h5>
                                    <small class="text-muted">Ganancias</small>
                                </div>
                                <i class="fa-solid fa-coins fa-2x text-info opacity-85"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======= Fila 2: GRÁFICAS PRINCIPALES ======= -->
                <div class="row g-3 mb-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Pacientes por Sexo</h6>
                                <canvas id="graficoSexo"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Evaluaciones por Mes</h6>
                                <canvas id="graficoEvaluaciones"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Sesiones por Mes</h6>
                                <canvas id="graficoSesionesMes"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======= Fila 3: GRÁFICAS SECUNDARIAS Y PROMEDIOS ======= -->
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Distribución por Edad</h6>
                                <canvas id="graficoEdad"></canvas>
                            </div>
                        </div>
                    </div>

{{--                     <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Ganancias Últimos 5 meses</h6>
                                <canvas id="graficoGanancias"></canvas>
                            </div>
                        </div>
                    </div> --}}

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Top 6 Ocupaciones</h6>
                                <canvas id="graficoOcupaciones"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-lg border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-center"
                                style="height: {{ $cardHeight }};">
                                <h6 class="text-center fw-bold text-secondary mt-3">Estado de Evaluaciones</h6>
                                <canvas id="graficoEstadoEvaluaciones"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-lg-4 col-md-12">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card shadow-lg border-0 rounded-3">
                                    <div class="card-body text-center d-flex flex-column justify-content-center"
                                        style="height: {{ $cardHeight }};">
                                        <h6 class="mb-3 fw-bold text-secondary">Promedio Sesiones/Evaluación</h6>
                                        <h3 class="fw-bold text-primary">{{ $promedioSesionesPorEvaluacion }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card shadow-lg border-0 rounded-3">
                                    <div class="card-body text-center d-flex flex-column justify-content-center"
                                        style="height: {{ $cardHeight }};">
                                        <h6 class="mb-3 fw-bold text-secondary">Promedio Evaluaciones/Paciente</h6>
                                        <h3 class="fw-bold text-success">{{ $promedioEvaluacionesPorPaciente }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

            </div>
        </div>
    </main>

    <!-- Chart.js local -->
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
            },
            layout: {
                padding: 15
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            },
            hover: {
                mode: 'nearest',
                intersect: true,
                animationDuration: 400
            }
        };

        function createGradient(ctx, colorStart, colorEnd, vertical = true) {
            let gradient = vertical ? ctx.createLinearGradient(0, 0, 0, 250) : ctx.createLinearGradient(0, 0, 300, 0);
            gradient.addColorStop(0, colorStart);
            gradient.addColorStop(1, colorEnd);
            return gradient;
        }

        // Pacientes por Sexo
        const ctxSexo = document.getElementById('graficoSexo').getContext('2d');
        new Chart(ctxSexo, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($pacientesPorSexo)),
                datasets: [{
                    data: @json(array_values($pacientesPorSexo)),
                    backgroundColor: [createGradient(ctxSexo, '#007bff', '#66b2ff'), '#dc3545'],
                    borderWidth: 1
                }]
            },
            options: commonOptions
        });

        // Evaluaciones por Mes
        const ctxEval = document.getElementById('graficoEvaluaciones').getContext('2d');
        new Chart(ctxEval, {
            type: 'bar',
            data: {
                labels: @json($ultimos5MesesLegibles),
                datasets: [{
                    label: 'Evaluaciones',
                    data: @json($evaluacionesPorMes),
                    backgroundColor: createGradient(ctxEval, '#17a2b8', '#7ad8e6')
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Sesiones por Mes
        const ctxSesiones = document.getElementById('graficoSesionesMes').getContext('2d');
        new Chart(ctxSesiones, {
            type: 'bar',
            data: {
                labels: @json($ultimos5MesesLegibles),
                datasets: [{
                    label: 'Sesiones',
                    data: @json($sesionesPorMes),
                    backgroundColor: createGradient(ctxSesiones, '#28a745', '#82e4a9')
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Ganancias por Mes
/*         const ctxGanancias = document.getElementById('graficoGanancias').getContext('2d');
        new Chart(ctxGanancias, {
            type: 'line',
            data: {
                labels: @json($ultimos5MesesLegibles),
                datasets: [{
                    label: 'Bs Ganados',
                    data: @json($gananciasPorMes),
                    borderWidth: 2,
                    tension: 0.3,
                    borderColor: '#ff8c00',
                    backgroundColor: createGradient(ctxGanancias, '#ff8c00', '#ffc966'),
                    fill: true,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#ff6600'
                }]
            },
            options: commonOptions
        }); */

        // Distribución por Edad
        const ctxEdad = document.getElementById('graficoEdad').getContext('2d');
        new Chart(ctxEdad, {
            type: 'bar',
            data: {
                labels: @json($rangosEdad),
                datasets: [{
                    label: 'Pacientes',
                    data: @json($pacientesPorRangoEdad),
                    backgroundColor: createGradient(ctxEdad, '#6f42c1', '#b98ff3', false)
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });


        // Estado de Evaluaciones
        const ctxEstado = document.getElementById('graficoEstadoEvaluaciones').getContext('2d');
        new Chart(ctxEstado, {
            type: 'pie',
            data: {
                labels: ['Activos', 'Abandono', 'Alta'],
                datasets: [{
                    data: [
                        {{ $cantidadActivos }},
                        {{ $cantidadAbandono }},
                        {{ $cantidadAlta }}
                    ],
                    backgroundColor: [
                        createGradient(ctxEstado, '#007bff', '#66b2ff'),
                        createGradient(ctxEstado, '#dc3545', '#ff9fa8'),
                        createGradient(ctxEstado, '#198754', '#7ee3ac')
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = {{ $cantidadActivos + $cantidadAbandono + $cantidadAlta }};
                                const value = context.raw || 0;
                                const porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${value} (${porcentaje}%)`;
                            }
                        }
                    }
                }
            }
        });

        // NUEVO: Top 6 Ocupaciones
        const ctxOcupaciones = document.getElementById('graficoOcupaciones').getContext('2d');
        new Chart(ctxOcupaciones, {
            type: 'bar',
            data: {
                labels: @json($ocupacionesLabels),
                datasets: [{
                    label: 'Pacientes',
                    data: @json($ocupacionesData),
                    // Usamos un gradiente morado/rosa para diferenciarlo
                    backgroundColor: createGradient(ctxOcupaciones, '#e83e8c', '#ff99cc', false),
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y', // Hace que las barras sean horizontales para leer mejor los nombres
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // Para que no muestre decimales (ej: 1.5 pacientes)
                        }
                    }
                }
            }
        });
    </script>
@endsection

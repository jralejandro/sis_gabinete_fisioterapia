@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="app-content-header mb-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="fw-bold mb-0">
                        <i class="fa-solid fa-clipboard-list me-2"></i>
                        Reporte de Evaluaciones de Pacientes
                    </h2>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-secondary fw-semibold">
                            <i class="fa-solid fa-filter me-2"></i> Filtros
                        </h5>
                    </div>

                    {{-- 🔍 FILTROS --}}
                    <div class="card-body border-bottom">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buscar por CI</label>
                                <input type="text" id="filterCI" class="form-control" placeholder="Ej: 7894561">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Desde</label>
                                <input type="date" id="filterFrom" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hasta</label>
                                <input type="date" id="filterTo" class="form-control">
                            </div>
                            <div class="col-md-3 text-end">
                                <button id="clearFilters" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 🧾 TABLA --}}
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="evaluationsTable" class="table table-striped table-hover align-middle text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th>CI</th>
                                        <th>Paciente</th>
                                        <th>Fecha</th>
                                        <th>Diagnóstico</th>
                                        <th>Tratamiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->patient->ci ?? '—' }}</td>
                                            <td>{{ $evaluation->patient->nombres }}
                                                {{ $evaluation->patient->apellido_paterno }}</td>
                                            <td>{{ \Carbon\Carbon::parse($evaluation->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $evaluation->diagnostico_kinesico }}</td>
                                            <td>{{ $evaluation->plan_tratamiento }}</td>
                                            <td>
                                                <a href="{{ route('reports.evaluation.view', $evaluation) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>

                                                <a href="{{ route('reports.evaluation.download', $evaluation) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-muted text-end small px-4 py-3">
                        Total de evaluaciones: <strong>{{ $evaluations->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#evaluationsTable').DataTable({
                pageLength: 20,
                order: [
                    [2, 'desc']
                ],
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }
            });

            // 🔍 FILTROS PERSONALIZADOS
            $('#filterCI').on('keyup', function() {
                table.column(0).search(this.value).draw();
            });

            function filterByDate() {
                const from = $('#filterFrom').val();
                const to = $('#filterTo').val();

                $.fn.dataTable.ext.search.push(function(settings, data) {
                    const date = data[2];
                    const dateObj = new Date(date.split('/').reverse().join('-'));
                    const fromDate = from ? new Date(from) : null;
                    const toDate = to ? new Date(to) : null;

                    if ((!fromDate || dateObj >= fromDate) && (!toDate || dateObj <= toDate)) {
                        return true;
                    }
                    return false;
                });

                table.draw();
                $.fn.dataTable.ext.search.pop();
            }

            $('#filterFrom, #filterTo').on('change', filterByDate);

            $('#clearFilters').on('click', function() {
                $('#filterCI, #filterFrom, #filterTo').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endpush

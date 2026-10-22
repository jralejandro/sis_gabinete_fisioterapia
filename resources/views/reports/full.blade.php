@extends('layout.admin')

@section('content')
    <main class="app-main bg-light py-4">
        <div class="app-content-header mb-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="fw-bold mb-0">
                        <i class="fa-solid fa-file-medical me-2"></i> Historial Completo de Pacientes

                    </h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mt-3 mb-0 shadow-sm">
                        <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-secondary fw-semibold">
                            <i class="fa-solid fa-table-list me-2"></i> Lista de Pacientes
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="patientsTable" class="table table-striped table-hover align-middle text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th>N° CI</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Edad</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        <tr>
                                            <td>{{ $patient->ci }}</td>
                                            <td>{{ $patient->nombres }}</td>
                                            <td>{{ $patient->apellido_paterno }} {{ $patient->apellido_materno }}</td>
                                            <td>{{ $patient->edad ?? '—' }}</td>
                                            <td>{{ $patient->celular ?? '—' }}</td>
                                            <td>{{ $patient->domicilio ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('reports.patients.view', $patient) }}"
                                                    class="btn btn-outline-primary px-4"> <i class="fa-solid fa-eye"></i> Ver PDF
                                                </a> <a href="{{ route('reports.patients.download', $patient) }}"
                                                    class="btn btn-outline-success px-4"> <i class="fa-solid fa-download"></i>
                                                    Descargar </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-muted text-end small px-4 py-3">
                        Total de pacientes: <strong>{{ $patients->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#patientsTable').DataTable({
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                order: [
                    [1, 'asc']
                ],
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }
            });
        });
    </script>
@endpush

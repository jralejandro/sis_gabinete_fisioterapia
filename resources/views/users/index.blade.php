@extends('layout.admin')

@section('content')
<main class="app-main bg-light py-4">

    <!-- CONTENEDOR DE TOASTS -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

    <!-- ENCABEZADO -->
    <div class="app-content-header mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="fw-bold mb-0">
                    <i class="fa-solid fa-users me-2"></i> Gestión de Usuarios
                </h2>

                <a href="{{ route('users.create') }}" class="btn btn-outline-primary shadow-sm rounded-3">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Usuario
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0 shadow-sm">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card shadow-sm border-0 rounded-4">

                <!-- CARD HEADER -->
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 text-secondary fw-semibold">
                        <i class="fa-solid fa-table-list me-2"></i> Lista de Usuarios
                    </h5>
                </div>

                <!-- CARD BODY -->
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped table-hover align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre Completo</th>
                                    <th>CI</th>
                                    <th>Celular</th>
                                    <th>Email</th>
                                    <th>Cargo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->id }}</td>

                                        <td>
                                            <strong>{{ $usuario->nombres }}</strong><br>
                                            <small>{{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</small>
                                        </td>

                                        <td>{{ $usuario->ci }} {{ $usuario->complemento }}</td>
                                        <td>{{ $usuario->nro_celular ?? '—' }}</td>
                                        <td>{{ $usuario->email ?? '—' }}</td>
                                        <td>{{ $usuario->cargo }}</td>

                                        <td>
                                            @if ($usuario->estado)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>

                                        <td class="text-center">

                                            <a href="{{ route('users.edit', $usuario) }}"
                                                class="btn btn-outline-warning shadow-sm"> 
                                                <i class="fa-solid fa-pen-to-square"></i> Editar
                                            </a>

                                            <!-- BOTÓN HABILITAR / INHABILITAR AJAX -->
                                            <button type="button" class="btn btn-toggle shadow-sm"
                                                    data-id="{{ $usuario->id }}" data-estado="{{ $usuario->estado }}">
                                                @if ($usuario->estado)
                                                    <i class="fa-solid fa-user-slash"></i> Inhabilitar
                                                @else
                                                    <i class="fa-solid fa-user-check"></i> Habilitar
                                                @endif
                                            </button>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fa-solid fa-circle-info me-2"></i>
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="card-footer text-muted text-end small px-4 py-3">
                    Total de usuarios: <strong>{{ $usuarios->count() }}</strong>
                </div>

            </div>

        </div>
    </div>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Inicializar DataTable
    $('#usersTable').DataTable({
        pageLength: 20,
        lengthMenu: [10, 20, 50, 100],
        order: [[1, 'asc']],
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

    // Función para mostrar toast
    function showToast(message, type = 'success') {
        let toastHTML = `
        <div class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
        let toastContainer = $('#toastContainer');
        let $toast = $(toastHTML);
        toastContainer.append($toast);
        let toast = new bootstrap.Toast($toast[0]);
        toast.show();
        setTimeout(() => { $toast.remove(); }, 5000);
    }

    // AJAX Habilitar/Inhabilitar
    $('.btn-toggle').click(function() {
        let button = $(this);
        let userId = button.data('id');

        $.ajax({
            url: '/users/' + userId + '/toggle',
            type: 'PATCH',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                let badge = button.closest('tr').find('td:nth-child(7) span');
                if(response.estado) {
                    badge.removeClass('bg-danger').addClass('bg-success').text('Activo');
                    button.removeClass('btn-outline-success').addClass('btn-outline-danger')
                          .html('<i class="fa-solid fa-user-slash"></i> Inhabilitar');
                    showToast('Usuario habilitado correctamente.', 'success');
                } else {
                    badge.removeClass('bg-success').addClass('bg-danger').text('Inactivo');
                    button.removeClass('btn-outline-danger').addClass('btn-outline-success')
                          .html('<i class="fa-solid fa-user-check"></i> Habilitar');
                    showToast('Usuario inhabilitado correctamente.', 'danger');
                }
            },
            error: function() {
                showToast('Ocurrió un error al cambiar el estado.', 'danger');
            }
        });
    });

});
</script>
@endpush

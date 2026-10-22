<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gabinete Fisio</title>

    <!-- Iconos y estilos base -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}" />
    <link rel="icon" href="{{ asset('img/fisio.png') }}" type="image/png">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        main {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }

        .navbar {
            background-color: #024966 !important;
        }

        .navbar-brand img {
            height: 32px;
            margin-right: 8px;
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }

        .nav-link {
            color: #fff !important;
        }

        .nav-link.active {
            font-weight: 600;
            border-bottom: 2px solid #fff;
        }

        footer {
            background-color: #024966;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid">
            <!-- Logo a la izquierda -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard.index') }}">
                <img src="{{ asset('img/fisio.png') }}" alt="Gabinete FISIO">
                Gabinete FISIO
            </a>

            <!-- Botón responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú alineado a la derecha -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                    <!-- Inicio -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard.index') ? 'active fw-semibold text-light' : '' }}"
                            href="{{ route('dashboard.index') }}">
                            <i class="fa-solid fa-house fs-5"></i>
                            <span>Inicio</span>
                        </a>
                    </li>


                    <!-- Usuarios -->
                    @cargo('Administrador')
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('users.index') ? 'active fw-semibold text-light' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="fa-solid fa-users-gear fs-5"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                    @endcargo

                    <!-- Pacientes -->
                    @cargo(['Administrador', 'Fisioterapeuta', 'Interno'])
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('patients.index') ? 'active fw-semibold text-light' : '' }}"
                                href="{{ route('patients.index') }}">
                                <i class="fa-solid fa-user-group fs-5"></i>
                                <span>Pacientes</span>
                            </a>
                        </li>
                    @endcargo

                    <!-- 🔹 Reportes -->
                    @cargo(['Administrador', 'Fisioterapeuta'])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 {{ request()->routeIs('reports.*') ? 'active fw-semibold text-light' : '' }}"
                                href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-file-waveform"></i>
                                <span>Reportes</span>
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 rounded-3" aria-labelledby="reportsDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('reports.full') }}">
                                        <i class="fa-solid fa-file-medical"></i>
                                        Historial Clínico Completo
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('reports.evaluations') }}">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                        Reporte de Evaluaciones
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('reports.sessions') }}">
                                        <i class="fa-solid fa-user-nurse"></i>
                                        Reporte de Sesiones
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcargo

                    @cargo(['Administrador', 'Fisioterapeuta'])
                        <li class="nav-item dropdown mt-1">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 {{ request()->routeIs('statistics.*') ? 'active fw-semibold text-light' : '' }}"
                                href="#" id="statisticsDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-chart-line"></i>
                                <span>Estadísticas</span>
                            </a>

                            <ul class="dropdown-menu shadow-sm border-0 rounded-3" aria-labelledby="statisticsDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('statistics.costos.form') }}">
                                        <i class="fa-solid fa-coins"></i>
                                        Reporte Ingresos
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="{{ route('statistics.diagnosticos.form') }}">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        Reporte Diagnósticos
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endcargo



                    <!-- Separador visual -->
                    <li class="nav-item mx-2 d-none d-lg-block">
                        <div class="vr text-white opacity-50"></div>
                    </li>

                    <!-- Usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex flex-column flex-lg-row align-items-center text-center text-lg-start gap-2 {{ request()->routeIs('profile.*') ? 'active fw-semibold text-light' : '' }}"
                            href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-circle-user fs-4 text-white"></i>

                            <div class="text-white lh-sm">
                                <span class="fw-semibold d-block">
                                    {{ explode(' ', trim(Auth::user()->nombres))[0] }}
                                </span>
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                            aria-labelledby="profileDropdown">

                            <li>
                                <h6 class="dropdown-header">Cuenta</h6>
                            </li>

                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="dropdown-item d-flex align-items-center gap-2">
                                    <i class="fa-regular fa-address-card"></i> Datos personales
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('profile.password') }}"
                                    class="dropdown-item d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-key"></i> Cambiar contraseña
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a href="{{ route('logout') }}"
                                    class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold">
                                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- CONTENIDO PRINCIPAL -->
    <main class="py-4">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-white py-2 mt-auto">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>Sistema del Gabinete de Fisioterapia</div>
            <div class="d-none d-sm-inline">Versión 1.0</div>
        </div>
    </footer>

    <!-- JS base -->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>








    @stack('scripts')


</body>

</html>

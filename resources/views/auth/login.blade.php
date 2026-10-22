<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>

    {{-- AdminLTE (si usas) --}}
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">

    <style>
        /* 🎨 Colores institucionales */
        :root {
            --azul: #004c6d;
            --azul-oscuro: #00364d;
            --rojo: #c62828;
            --gris: #4a4a4a;
        }

        /* 🌌 Fondo completo */
        body {
            background: radial-gradient(circle at center, #005b80 0%, #003f5c 100%);
            height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* 📌 Contenedor principal */
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeIn 0.6s ease-out;
            display: flex;
            justify-content: center;
        }

        /* 🧊 Tarjeta del login */
        .login-card {
            background: #ffffff;
            border-radius: 22px;
            padding: 40px 30px;
            width: 100%;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
        }

        /* 🟦 Logo redondo */
        .logo-circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 2px solid var(--azul);
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.16);
        }

        /* 🎯 Título mejorado */
        .login-title {
            font-size: 1.6rem;
            font-weight: 900;
            text-align: center;
        }

        .login-title .azul {
            color: var(--azul-oscuro);
        }

        .login-title .rojo {
            color: var(--rojo);
        }

        /* Inputs */
        .input-group-text {
            background: var(--azul);
            color: #fff;
            border: none;
        }

        .form-control {
            border-radius: 10px;
            border: 1.7px solid #d0d0d0;
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 6px rgba(0, 76, 109, 0.4);
        }

        /* 🔥 Botón */
        .btn-login {
            background: linear-gradient(45deg, var(--azul), var(--azul-oscuro));
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, var(--azul-oscuro), var(--azul));
        }

        /* ❗ Errores */
        .error-text {
            color: var(--rojo);
            font-size: 0.9rem;
            font-weight: 700;
            margin-top: 5px;
            padding-left: 5px;
        }

        /* Alertas globales */
        .alert-danger {
            border-left: 5px solid var(--rojo);
            font-weight: 700;
        }

        /* Animación */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 📱 Mobile corregido: ocupa TODA la pantalla */
        @media (max-width: 600px) {
            body {
                padding: 0;
                height: 100vh;
            }

            .login-wrapper {
                width: 100%;
                height: 100%;
                padding: 0;
                display: flex;
                align-items: center;
            }

            .login-card {
                border-radius: 0;
                height: 100%;
                max-height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-card">

            {{-- ALERTAS GLOBALES --}}
            @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif

            {{-- LOGO --}}
            <div class="logo-circle">
                <img src="{{ asset('img/fisio.png') }}" alt="Logo" class="img-fluid" style="width:100%; border-radius: 50%;">
            </div>

            {{-- TITULO --}}
            <h3 class="login-title">
                <span class="rojo">Gabinete</span> <span class="azul">FISIO TecMed</span>
            </h3>
            <p class="text-center text-muted mb-4">Ingresa tus credenciales para continuar</p>

            {{-- FORMULARIO --}}
            <form action="{{ route('login.access') }}" method="POST">
                @csrf

                {{-- CI --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                        <input type="number" name="ci" class="form-control" placeholder="Carnet de Identidad" value="{{ old('ci') }}">
                    </div>
                    @error('ci') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                {{-- COMPLEMENTO --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                        <input type="text" name="complemento" class="form-control" maxlength="2" placeholder="Complemento (opcional)" value="{{ old('complemento') }}">
                    </div>
                    @error('complemento') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                {{-- CONTRASEÑA --}}
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Contraseña">
                    </div>
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                {{-- BOTÓN --}}
                <button type="submit" class="btn btn-login w-100 text-white">
                    <i class="fa-solid fa-sign-in-alt"></i> Iniciar Sesión
                </button>


            </form>

        </div>

    </div>

</body>

</html>
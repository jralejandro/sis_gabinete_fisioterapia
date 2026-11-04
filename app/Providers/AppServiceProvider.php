<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\Cargo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Normaliza el cargo del usuario de forma segura (soporta Enum y valores heredados en BD).
     */
    public static function normalizeCargo(?User $user): string
    {
        if (!$user) {
            return '';
        }

        return $user->cargo instanceof Cargo
            ? strtolower(trim($user->cargo->value))
            : strtolower(trim((string) $user->cargo));
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ==========================================
        // 0. PROTECCIÓN CONTRA FUERZA BRUTA (Login)
        // ==========================================
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));
            return Limit::perMinute(5)
                ->by($email . '|' . $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Demasiados intentos de inicio de sesión. Por seguridad, espere 1 minuto antes de volver a intentarlo.',
                    ], 429);
                });
        });

        // ==========================================
        // 1. DIRECTIVA BLADE (Compatibilidad Web)
        // ==========================================
        Blade::if('cargo', function ($roles) {
            $user = auth()->user();
            if (!$user || $user->estado != 1) {
                return false;
            }

            $roles = is_array($roles) ? $roles : [$roles];
            $rolesNormalizados = array_map(fn($r) => strtolower(trim((string) $r)), $roles);
            $cargoActual = self::normalizeCargo($user);

            return in_array($cargoActual, $rolesNormalizados, true);
        });

        // ==========================================
        // 2. SISTEMA DE PERMISOS RBAC (Gates)
        // ==========================================

        // Si el usuario está inactivo, se bloquea cualquier acción.
        // Si es Administrador activo, tiene acceso total.
        Gate::before(function (User $user, string $ability) {
            if ($user->estado != 1) {
                return false;
            }

            if (in_array(self::normalizeCargo($user), [Cargo::ADMIN->value, 'administrador'], true)) {
                return true;
            }

            return null;
        });

        // Gestión de usuarios (Crear, editar, activar/desactivar cuentas): Exclusivo Admin
        Gate::define('gestionar-usuarios', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
            ], true);
        });

        // Respaldos de BD y archivos: Exclusivo Admin
        Gate::define('gestionar-backups', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
            ], true);
        });

        // Acceso a módulo de Reportes y Estadísticas BI: Admin y Fisioterapeuta/Kinesiólogo
        Gate::define('ver-reportes', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
            ], true);
        });

        // Registrar/editar evaluaciones clínicas: Personal clínico y transcripción
        Gate::define('evaluar-paciente', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
                Cargo::INTERNO->value,
                Cargo::ESTUDIANTE->value,
            ], true);
        });

        // Eliminar pacientes y sus registros asociados: Solo Admin y Fisioterapeuta/Kinesiólogo
        Gate::define('eliminar-paciente', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
            ], true);
        });

        // Eliminar evaluaciones clínicas: Admin, Fisioterapeuta e Interno (Estudiante no elimina)
        Gate::define('eliminar-evaluacion', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
                Cargo::INTERNO->value,
            ], true);
        });

        // Eliminar sesiones terapéuticas: Admin, Fisioterapeuta e Interno (Estudiante no elimina)
        Gate::define('eliminar-sesion', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
                Cargo::INTERNO->value,
            ], true);
        });

        // ==========================================
        // 3. CLUB DEL ADULTO MAYOR
        // ==========================================

        // Inscribir, editar, dar de baja y cobrar mensualidades: Admin, Fisioterapeuta e Interno
        Gate::define('gestionar-club', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
                Cargo::INTERNO->value,
            ], true);
        });

        // Anular pagos y eliminar inscripciones registradas por error: Admin y Fisioterapeuta
        Gate::define('anular-club', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
                Cargo::KINESIOLOGO->value,
                'fisioterapeuta',
            ], true);
        });

        // Crear/editar grupos y horarios del club: Exclusivo Admin
        Gate::define('gestionar-grupos-club', function (User $user) {
            return in_array(self::normalizeCargo($user), [
                Cargo::ADMIN->value,
                'administrador',
            ], true);
        });
    }
}

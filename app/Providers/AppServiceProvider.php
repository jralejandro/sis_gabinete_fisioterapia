<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate; // 🔥 1. Importamos la fachada Gate
use App\Models\User;                 // 🔥 2. Importamos el modelo User
use App\Enums\Cargo;                 // 🔥 3. Importamos el Enum que creamos

class AppServiceProvider extends ServiceProvider
{
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
        // 1. TU LÓGICA DE BLADE (Mejorada para Enums)
        // ==========================================
        Blade::if('cargo', function ($roles) {
            $user = auth()->user();
            if (!$user) return false;

            $roles = is_array($roles) ? $roles : [$roles];

            // Si el cargo ya es un Enum, sacamos su valor string para comparar
            $cargoActual = $user->cargo instanceof Cargo ? $user->cargo->value : $user->cargo;

            return in_array($cargoActual, $roles);
        });

        // ==========================================
        // 2. SISTEMA DE PERMISOS "SENIOR" (Gates)
        // ==========================================

        // El ADMIN tiene poder absoluto. Laravel ni siquiera lee lo demás si eres Admin.
        Gate::before(function (User $user, string $ability) {
            if ($user->cargo === Cargo::ADMIN) {
                return true;
            }
        });

        // Permisos específicos por acción
        Gate::define('evaluar-paciente', function (User $user) {
            // Solo los Kinesiólogos pueden evaluar (el Admin también entra por el Gate::before)
            return $user->cargo->in([Cargo::KINESIOLOGO]);
        });

        // Ejemplo extra:
        Gate::define('ver-reportes', function (User $user) {
            return $user->cargo->in([Cargo::KINESIOLOGO, Cargo::ESTUDIANTE]);
        });
    }
}

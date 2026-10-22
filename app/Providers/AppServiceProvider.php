<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::if('cargo', function ($roles) {
            $user = auth()->user();
            if (!$user) return false;

            $roles = is_array($roles) ? $roles : [$roles];
            return in_array($user->cargo, $roles);
        });
    }
}

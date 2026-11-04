<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\AppServiceProvider;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->estado != 1) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'El usuario está inactivo.'], 403);
            }
            abort(403, 'El usuario está inactivo.');
        }

        $userRole = AppServiceProvider::normalizeCargo($user);
        $roles = array_map(fn($r) => strtolower(trim((string) $r)), $roles);

        if (!in_array($userRole, $roles, true)) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}

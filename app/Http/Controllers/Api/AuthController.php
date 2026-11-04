<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validación estricta de formato de correo y contraseña
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un formato de correo válido (ejemplo@correo.com).',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // 2. Protección contra fuerza bruta: solo bloquea tras 5 intentos FALLIDOS en 1 minuto
        $throttleKey = 'login:' . strtolower(trim((string) $request->email)) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Demasiados intentos fallidos. Por seguridad, espere {$seconds} segundos antes de volver a intentarlo.",
            ], 429);
        }

        // 3. Buscar usuario directamente por su email
        $user = User::where('email', $request->email)->first();

        // 4. Verificar existencia de usuario y coincidencia de contraseña
        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'message' => 'El correo electrónico o la contraseña son incorrectos.',
            ], 401);
        }

        // 5. Verificar si el usuario está inactivo
        if ($user->estado != 1) {
            return response()->json([
                'message' => 'El usuario está inactivo. Contacte con el administrador.',
            ], 403);
        }

        // 6. Inicio exitoso: limpiamos el contador de intentos fallidos
        RateLimiter::clear($throttleKey);

        // 7. Generar el Token de Sanctum
        $token = $user->createToken('react-frontend')->plainTextToken;

        // 8. Retornar respuesta exitosa al frontend
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}

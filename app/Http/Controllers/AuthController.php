<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }


    public function login(Request $request)
    {
        $request->validate([
            'ci' => ['required', 'digits_between:5,15'],
            'complemento' => ['nullable', 'alpha_num', 'max:3'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.digits_between' => 'El CI debe tener entre 5 y 15 dígitos.',
            'complemento.alpha_num' => 'El complemento solo puede contener letras o números.',
            'complemento.max' => 'El complemento no puede exceder 3 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $ci = preg_replace('/[^0-9]/', '', $request->ci);
        $complemento = $request->complemento
            ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->complemento))
            : null;


        // Buscar usuario con CI + complemento compuesto
        $user = User::where('ci', $ci)
            ->where(function ($q) use ($complemento) {
                if ($complemento === null) {
                    $q->whereNull('complemento')->orWhere('complemento', '');
                } else {
                    $q->where('complemento', $complemento);
                }
            })
            ->first();

        // Usuario inactivo
        if ($user && $user->estado != 1) {
            return back()->withErrors([
                'error' => 'El usuario está inactivo. Contacte con el administrador.',
            ])->withInput();
        }

        // Intento de login usando el ID del usuario encontrado
        if ($user && Auth::attempt(['id' => $user->id, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'error' => 'CI, complemento o contraseña incorrectos.',
        ])->withInput();
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

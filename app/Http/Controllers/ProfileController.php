<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar formulario de edición de datos personales
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Actualizar datos personales
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nro_celular' => 'required|numeric|digits_between:7,15',
            'direccion' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'nivel_academico' => 'nullable|string|max:255',
            'profesion' => 'nullable|string|max:255',
        ]);

        $user->update($request->all());

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    /**
     * FORMULARIO para cambiar contraseña
     */
    public function passwordForm()
    {
        return view('profile.password');
    }

    /**
     * ACTUALIZAR contraseña
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password' => [
                'required',
                'confirmed',         // Verifica password_confirmation
                'min:5',             // Mínimo 5 caracteres
                'regex:/[a-z]/',     // Al menos una minúscula
                'regex:/[A-Z]/',     // Al menos una mayúscula
                'regex:/[0-9]/',     // Al menos un número
            ],
        ], [
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
            'password.min'   => 'La contraseña debe tener mínimo 5 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // Actualizar nueva contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña cambiada exitosamente.');
    }
}

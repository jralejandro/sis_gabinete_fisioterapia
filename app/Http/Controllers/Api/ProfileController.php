<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Actualizar datos personales (PUT /api/profile)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nro_celular'     => 'required|numeric|digits_between:7,15',
            'direccion'       => 'nullable|string|max:255',
            'email'           => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'nivel_academico' => 'nullable|string|max:255',
            'profesion'       => 'nullable|string|max:255',
        ], [
            'nro_celular.required'       => 'El número de celular es obligatorio.',
            'nro_celular.numeric'        => 'El celular solo debe contener números.',
            'nro_celular.digits_between' => 'El celular debe tener entre 7 y 15 dígitos.',
            'email.email'                => 'El formato del correo no es válido.',
            'email.unique'               => 'Este correo ya está registrado en otra cuenta.',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Datos personales actualizados correctamente.',
            'user'    => $user
        ], 200);
    }

    /**
     * ACTUALIZAR contraseña (PUT /api/profile/password)
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // 🔥 Seguridad Extra: Pedimos la contraseña actual para verificar que es el dueño de la cuenta
        $request->validate([
            'current_password' => 'required',
            'password'         => [
                'required',
                'confirmed',         // Verifica password_confirmation
                'min:6',             // Mínimo 6 caracteres (alineado con Login y Usuarios)
                'regex:/[a-z]/',     // Al menos una minúscula
                'regex:/[A-Z]/',     // Al menos una mayúscula
                'regex:/[0-9]/',     // Al menos un número
            ],
        ], [
            'current_password.required' => 'Debe ingresar su contraseña actual.',
            'password.required'         => 'Debe ingresar una nueva contraseña.',
            'password.regex'            => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
            'password.min'              => 'La contraseña debe tener mínimo 6 caracteres.',
            'password.confirmed'        => 'La confirmación de la contraseña no coincide.',
        ]);

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => ['current_password' => ['La contraseña actual es incorrecta.']]
            ], 422);
        }

        // Actualizar nueva contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña cambiada exitosamente.'
        ], 200);
    }
}

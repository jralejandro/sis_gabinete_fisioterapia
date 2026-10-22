<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('id', 'DESC')->get();
        return view('users.index', compact('usuarios'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres'           => 'required|string|max:255',
            'apellido_paterno'  => 'nullable|string|max:255',
            'apellido_materno'  => 'nullable|string|max:255',

            // CI con validación compuesta
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('users')->where(function ($query) use ($request) {

                    $complemento = $request->complemento ?: null;

                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                // Si complemento está vacío → validar null o ''
                                $q->whereNull('complemento')
                                    ->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                }),
            ],

            'complemento' => [
                'nullable',
                'string',
                'max:3',
                'regex:/^[A-Za-z0-9]{0,3}$/',
            ],

            'nro_celular'       => 'required|numeric|digits_between:7,15',
            'direccion'         => 'nullable|string|max:255',

            'email'             => 'nullable|email|unique:users,email',

            'cargo'             => 'required|string|max:255',
            'nivel_academico'   => 'nullable|string|max:255',
            'profesion'         => 'nullable|string|max:255',

            'password'          => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],

        ], [
            'ci.unique'             => 'Ya existe un usuario con este CI y complemento.',
            'complemento.regex'     => 'El complemento solo puede contener hasta 3 caracteres alfanuméricos.',
            'password.min'          => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex'        => 'La contraseña debe contener mayúscula, minúscula y número.',
            'password.confirmed'    => 'La confirmación no coincide.',
        ]);

        // Normalizar complemento
        $data['complemento'] = !empty($data['complemento'])
            ? strtoupper($data['complemento'])
            : null;

        // Estado por defecto
        $data['estado'] = 1;

        // Encriptar contraseña
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }




    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nombres'           => 'required|string|max:255',
            'apellido_paterno'  => 'nullable|string|max:255',
            'apellido_materno'  => 'nullable|string|max:255',

            // CI con validación compuesta igual que STORE
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('users')->where(function ($query) use ($request, $user) {

                    $complemento = $request->complemento ?: null;

                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                // Si viene vacío → validar null o ''
                                $q->whereNull('complemento')
                                    ->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                })->ignore($user->id),
            ],

            'complemento' => [
                'nullable',
                'string',
                'max:3',
                'regex:/^[A-Za-z0-9]{0,3}$/',
            ],

            'nro_celular'       => 'required|numeric|digits_between:7,15',
            'direccion'         => 'nullable|string|max:255',

            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],

            'cargo'             => 'required|string|max:255',
            'nivel_academico'   => 'nullable|string|max:255',
            'profesion'         => 'nullable|string|max:255',

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'ci.unique'             => 'Ya existe un usuario con este CI y complemento.',
            'complemento.regex'     => 'El complemento solo puede contener hasta 3 caracteres alfanuméricos.',
            'password.min'          => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex'        => 'La contraseña debe contener una mayúscula, una minúscula y un número.',
            'password.confirmed'    => 'La confirmación de la contraseña no coincide.',
        ]);

        // Normalizar complemento
        $data['complemento'] = !empty($data['complemento'])
            ? strtoupper($data['complemento'])
            : null;

        // Actualizar contraseña solo si fue escrita
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }



    public function toggle(User $user)
    {
        $user->estado = $user->estado ? 0 : 1;
        $user->save();

        return response()->json([
            'estado' => $user->estado
        ]);
    }
}

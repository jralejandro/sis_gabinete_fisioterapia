<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('id', 'DESC')->get();
        return response()->json($usuarios);
    }

    /**
     * Diccionario centralizado de mensajes en español
     */
    private function mensajesValidacion(): array
    {
        return [
            'nombres.required'          => 'El nombre del usuario es obligatorio.',
            'nombres.max'               => 'El nombre no debe exceder los 255 caracteres.',
            'ci.required'               => 'La Cédula de Identidad es obligatoria.',
            'ci.numeric'                => 'El CI solo debe contener números.',
            'ci.digits_between'         => 'El CI debe tener entre 5 y 15 dígitos.',
            'ci.unique'                 => 'Ya existe un usuario registrado con este CI y complemento.',
            'complemento.max'           => 'El complemento no puede tener más de 3 caracteres.',
            'complemento.regex'         => 'El complemento solo acepta letras y números.',
            'nro_celular.required'      => 'El número de celular es obligatorio.',
            'nro_celular.numeric'       => 'El celular solo debe contener números.',
            'nro_celular.digits_between' => 'El celular debe tener entre 7 y 15 dígitos.',
            'email.required'            => 'El correo electrónico es obligatorio para el acceso.',
            'email.email'               => 'Ingrese un formato de correo electrónico válido.',
            'email.unique'              => 'Este correo electrónico ya pertenece a otro usuario.',
            'cargo.required'            => 'Debe asignar un cargo o rol del sistema.',
            'password.required'         => 'Debe establecer una contraseña de acceso.',
            'password.min'              => 'La contraseña debe tener al menos 6 caracteres.',
            'password.regex'            => 'La contraseña debe incluir mayúscula, minúscula y número.',
            'password.confirmed'        => 'Las contraseñas ingresadas no coinciden.',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres'           => 'required|string|max:255',
            'apellido_paterno'  => 'nullable|string|max:255',
            'apellido_materno'  => 'nullable|string|max:255',
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('users')->where(function ($query) use ($request) {
                    $complemento = $request->complemento ?: null;
                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                $q->whereNull('complemento')->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                }),
            ],
            'complemento'       => ['nullable', 'string', 'max:3', 'regex:/^[A-Za-z0-9]{0,3}$/'],
            'nro_celular'       => 'required|numeric|digits_between:7,15',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'cargo'             => 'required|string|max:255',
            'nivel_academico'   => 'nullable|string|max:255',
            'profesion'         => 'nullable|string|max:255',
            'password'          => ['required', 'string', 'min:6', 'confirmed', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ], $this->mensajesValidacion());

        $data['complemento'] = !empty($data['complemento']) ? strtoupper($data['complemento']) : null;
        $data['estado'] = 1;
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuario creado correctamente.',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nombres'           => 'required|string|max:255',
            'apellido_paterno'  => 'nullable|string|max:255',
            'apellido_materno'  => 'nullable|string|max:255',
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('users')->where(function ($query) use ($request, $user) {
                    $complemento = $request->complemento ?: null;
                    return $query->where('ci', $request->ci)
                        ->where(function ($q) use ($complemento) {
                            if ($complemento === null) {
                                $q->whereNull('complemento')->orWhere('complemento', '');
                            } else {
                                $q->where('complemento', strtoupper($complemento));
                            }
                        });
                })->ignore($user->id),
            ],
            'complemento'       => ['nullable', 'string', 'max:3', 'regex:/^[A-Za-z0-9]{0,3}$/'],
            'nro_celular'       => 'required|numeric|digits_between:7,15',
            'direccion'         => 'nullable|string|max:255',
            'email'             => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'cargo'             => 'required|string|max:255',
            'nivel_academico'   => 'nullable|string|max:255',
            'profesion'         => 'nullable|string|max:255',
            'password'          => ['nullable', 'string', 'min:6', 'confirmed', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ], $this->mensajesValidacion());

        $data['complemento'] = !empty($data['complemento']) ? strtoupper($data['complemento']) : null;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'user' => $user
        ]);
    }

    public function toggle(User $user)
    {
        $user->estado = $user->estado ? 0 : 1;
        $user->save();

        return response()->json([
            'message' => $user->estado ? 'Usuario activado' : 'Usuario desactivado',
            'estado' => $user->estado
        ]);
    }
}

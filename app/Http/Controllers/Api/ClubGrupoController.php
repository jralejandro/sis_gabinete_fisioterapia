<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClubGrupo;
use App\Models\ClubMiembro;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClubGrupoController extends Controller
{
    private function mensajes(): array
    {
        return [
            'nombre.required'      => 'El nombre del grupo es obligatorio.',
            'nombre.unique'        => 'Ya existe un grupo con ese nombre.',
            'horario.required'     => 'El horario es obligatorio (ej. 08:30 - 09:30).',
            'cupo_maximo.integer'  => 'El cupo debe ser un número entero.',
            'cupo_maximo.min'      => 'El cupo debe ser al menos 1.',
        ];
    }

    /**
     * Listar grupos con la cantidad de participantes activos.
     */
    public function index(Request $request)
    {
        $query = ClubGrupo::withCount('miembrosActivos')
            ->orderBy('orden')
            ->orderBy('id');

        if ($request->boolean('activos')) {
            $query->where('estado', true);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:club_grupos,nombre',
            'horario'     => 'required|string|max:50',
            'dias'        => 'nullable|string|max:100',
            'cupo_maximo' => 'nullable|integer|min:1|max:500',
            'orden'       => 'nullable|integer|min:0|max:999',
            'estado'      => 'nullable|boolean',
        ], $this->mensajes());

        $data['orden'] = $data['orden'] ?? ((int) ClubGrupo::max('orden') + 1);
        $data['estado'] = $data['estado'] ?? true;

        $grupo = ClubGrupo::create($data);

        return response()->json([
            'message' => 'Grupo creado correctamente.',
            'grupo'   => $grupo->loadCount('miembrosActivos'),
        ], 201);
    }

    public function update(Request $request, ClubGrupo $grupo)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', Rule::unique('club_grupos', 'nombre')->ignore($grupo->id)],
            'horario'     => 'required|string|max:50',
            'dias'        => 'nullable|string|max:100',
            'cupo_maximo' => 'nullable|integer|min:1|max:500',
            'orden'       => 'nullable|integer|min:0|max:999',
            'estado'      => 'nullable|boolean',
        ], $this->mensajes());

        $activos = $grupo->miembrosActivos()->count();

        if (!empty($data['cupo_maximo']) && $data['cupo_maximo'] < $activos) {
            return response()->json([
                'message' => "El grupo ya tiene {$activos} participantes activos; el cupo no puede ser menor.",
                'errors'  => ['cupo_maximo' => ["Mínimo {$activos} (participantes activos actuales)."]],
            ], 422);
        }

        if (array_key_exists('estado', $data) && $data['estado'] === false && $activos > 0) {
            return response()->json([
                'message' => "No se puede desactivar: el grupo tiene {$activos} participantes activos. Cámbielos de grupo primero.",
                'errors'  => ['estado' => ['El grupo tiene participantes activos.']],
            ], 422);
        }

        $grupo->update($data);

        return response()->json([
            'message' => 'Grupo actualizado correctamente.',
            'grupo'   => $grupo->loadCount('miembrosActivos'),
        ]);
    }

    public function destroy(ClubGrupo $grupo)
    {
        if (ClubMiembro::where('club_grupo_id', $grupo->id)->exists()) {
            return response()->json([
                'message' => 'Este grupo tiene historial de participantes. En lugar de eliminarlo, desactívelo.',
            ], 422);
        }

        $grupo->delete();

        return response()->json(['message' => 'Grupo eliminado correctamente.']);
    }
}


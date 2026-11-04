<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClubMiembro;
use App\Models\ClubTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubTestController extends Controller
{
    /**
     * Subir un test/evaluación manual del participante.
     * POST /api/club/miembros/{miembro}/tests (multipart/form-data)
     */
    public function store(Request $request, ClubMiembro $miembro)
    {
        $data = $request->validate([
            'fecha'       => 'required|date|before_or_equal:today',
            'tipo_test'   => 'required|string|max:100',
            'descripcion' => 'required|string|max:5000',
            'archivo'     => 'required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:5120',
        ], [
            'fecha.required'       => 'La fecha del test es obligatoria.',
            'fecha.before_or_equal' => 'La fecha del test no puede ser futura.',
            'tipo_test.required'   => 'Seleccione el tipo de test.',
            'descripcion.required' => 'La descripción o resultado del test es obligatorio.',
            'archivo.required'     => 'Debe adjuntar el archivo del test.',
            'archivo.mimes'        => 'El archivo debe ser PDF, JPG o PNG.',
            'archivo.mimetypes'    => 'El contenido del archivo no corresponde a un PDF o imagen válida.',
            'archivo.max'          => 'El archivo no debe pesar más de 5 MB.',
        ]);

        $file = $request->file('archivo');
        $path = $file->store(ClubTest::DIRECTORIO . '/' . $miembro->id, ClubTest::DISK);

        $test = ClubTest::create([
            'club_miembro_id'         => $miembro->id,
            'fecha'                   => $data['fecha'],
            'tipo_test'               => $data['tipo_test'],
            'descripcion'             => $data['descripcion'],
            'archivo'                 => $path,
            'archivo_nombre_original' => Str::limit($file->getClientOriginalName(), 250, ''),
            'created_by'              => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Test registrado correctamente.',
            'test'    => $test->load('creador:id,nombres,apellido_paterno'),
        ], 201);
    }

    /**
     * Descarga autenticada del archivo (nunca por URL pública).
     */
    public function archivo(ClubTest $test)
    {
        if (!$test->archivo || !Storage::disk(ClubTest::DISK)->exists($test->archivo)) {
            return response()->json(['message' => 'El archivo no se encuentra en el servidor.'], 404);
        }

        $extension = pathinfo($test->archivo, PATHINFO_EXTENSION);
        $nombre = $test->archivo_nombre_original
            ?: ('test_' . $test->id . '.' . $extension);

        return Storage::disk(ClubTest::DISK)->download($test->archivo, $nombre);
    }

    public function destroy(ClubTest $test)
    {
        if ($test->archivo) {
            Storage::disk(ClubTest::DISK)->delete($test->archivo);
        }
        $test->delete();

        return response()->json(['message' => 'Test eliminado correctamente.']);
    }
}


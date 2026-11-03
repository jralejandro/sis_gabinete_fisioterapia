<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups)
    {
    }

    /** Lista de respaldos disponibles + configuración. */
    public function index(): JsonResponse
    {
        return response()->json([
            'backups' => $this->backups->list(),
            'config' => [
                'keep_days' => (int) config('backup.keep_days'),
                'schedule_time' => config('backup.schedule_time'),
                'timezone' => config('app.timezone'),
            ],
        ]);
    }

    /** Genera un respaldo manual en el momento. */
    public function store(): JsonResponse
    {
        try {
            $backup = $this->backups->create('manual');
        } catch (Throwable $e) {
            Log::error('Respaldo manual fallido: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'No se pudo generar el respaldo: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Respaldo generado correctamente.',
            'backup' => $backup,
        ], 201);
    }

    /** Descarga un respaldo existente. */
    public function download(string $filename): BinaryFileResponse|JsonResponse
    {
        $path = $this->backups->path($filename);
        if (! $path) {
            return response()->json(['message' => 'Respaldo no encontrado.'], 404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /** Elimina un respaldo. */
    public function destroy(string $filename): JsonResponse
    {
        if (! $this->backups->delete($filename)) {
            return response()->json(['message' => 'Respaldo no encontrado.'], 404);
        }

        return response()->json(['message' => 'Respaldo eliminado correctamente.']);
    }
}


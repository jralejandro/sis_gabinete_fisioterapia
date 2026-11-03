<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class BackupRun extends Command
{
    protected $signature = 'backup:run
                            {--manual : Marca el respaldo como manual en lugar de automático}
                            {--no-prune : No eliminar respaldos antiguos}';

    protected $description = 'Genera un respaldo (base de datos + archivos adicionales) y elimina los antiguos';

    public function handle(BackupService $backups): int
    {
        $type = $this->option('manual') ? 'manual' : 'auto';
        $this->info("Generando respaldo ({$type})...");

        try {
            $backup = $backups->create($type);
        } catch (Throwable $e) {
            Log::error('Respaldo fallido: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Error al generar el respaldo: ' . $e->getMessage());

            return self::FAILURE;
        }

        $sizeMb = number_format($backup['size'] / 1048576, 2);
        $this->info("Respaldo creado: {$backup['name']} ({$sizeMb} MB)");
        Log::info("Respaldo creado: {$backup['name']} ({$sizeMb} MB)");

        if (! $this->option('no-prune')) {
            $deleted = $backups->prune();
            if ($deleted > 0) {
                $this->info("Respaldos antiguos eliminados: {$deleted}");
            }
        }

        return self::SUCCESS;
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Support\Arr;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES'); // Español

        $responsables = User::whereIn('cargo', ['Fisioterapeuta', 'Interno'])
            ->where('estado', 1)
            ->get();

        if ($responsables->isEmpty()) {
            return;
        }

        $patients = Patient::all();

        foreach ($patients as $patient) {
            $numEvaluations = rand(1, 5);

            for ($i = 0; $i < $numEvaluations; $i++) {
                $responsable = $responsables->random();

                Evaluation::create([
                    'patient_id'                 => $patient->id,
                    'motivo_consulta'            => $faker->sentence(),
                    'medico_referencia'          => $faker->name(),
                    'centro_referencia'          => $faker->company(),
                    'diagnostico_medico'         => $faker->sentence(),
                    'responsable_evaluacion'     => $responsable->id,
                    'historia_enfermedad_actual' => $faker->paragraph(2),
                    'frecuencia_cardiaca'        => $faker->numberBetween(60, 120),
                    'frecuencia_respiratoria'    => $faker->numberBetween(12, 25),
                    'presion_arterial'           => $faker->numberBetween(90, 140) . '/' . $faker->numberBetween(60, 90),
                    'temperatura'                => $faker->randomFloat(1, 36.0, 38.5),
                    'caracteristicas_dolor'      => $faker->sentence(),
                    'inspeccion'                 => $faker->sentence(),
                    'evaluacion_postural'        => $faker->sentence(),
                    'costo_evaluacion'          => '25.00',
                    'contractura_muscular'       => $faker->sentence(),
                    'puntos_dolorosos'           => $faker->sentence(),
                    'puntos_gatillo'             => $faker->sentence(),
                    'retreaccion_acortamiento'   => $faker->sentence(),
                    'tono'                       => Arr::random(['Hipotono', 'Hipertono', 'Normal']),
                    'tono_detalle'               => $faker->sentence(),
                    'trofismo'                   => Arr::random(['Hipertrofia', 'Hipotrofia', 'Normal']),
                    'trofismo_detalle'           => $faker->sentence(),
                    'evaluacion_marcha'          => $faker->sentence(),
                    'balance_articular'          => $faker->sentence(),
                    'balance_muscular'           => $faker->sentence(),
                    'sensibilidad'               => $faker->sentence(),
                    'examenes_complementarios'   => $faker->paragraph(),
                    'diagnostico_kinesico'       => $faker->paragraph(),
                    'objetivos_tratamiento'      => $faker->paragraph(),
                    'plan_tratamiento'           => $faker->paragraph(),
                    'fecha'                      => $faker->dateTimeBetween('-5 year', 'now'),
                    'estado'                     => 'ACTIVO',
                    'created_by'                 => 1,
                ]);
            }
        }
    }
}

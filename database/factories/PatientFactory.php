<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * Define los valores por defecto para el modelo (todos obligatorios).
     */
    public function definition(): array
    {
        return [
            'ci' => $this->faker->unique()->numerify('########'), // cédula única
            'complemento' => $this->faker->optional()->regexify('[0-9][A-Z]'),
            'nombres' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'sexo' => $this->faker->randomElement(['Masculino', 'Femenino']),
            'ocupacion' => $this->faker->jobTitle(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '2005-01-01'), // Pacientes mayores de 18 años
            'domicilio' => $this->faker->address(),
            'estado_civil' => $this->faker->randomElement(['Soltero', 'Casado', 'Divorciado', 'Viudo']),
            'celular' => $this->faker->numerify('7########'),
            'persona_referencia' => $this->faker->name(),
            'celular_referencia' => $this->faker->numerify('7########'),
            'estado' => 1, // todos activos
            // 'user_id' se asigna en el seeder dinámicamente
        ];
    }
}

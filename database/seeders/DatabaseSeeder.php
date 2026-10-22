<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USUARIOS BASE DEL SISTEMA
        |--------------------------------------------------------------------------
        */

        // 🔹 ADMINISTRADOR
        User::firstOrCreate(
            ['ci' => '10000001'],
            [
                'nombres'           => 'Administrador',
                'apellido_paterno'  => 'Principal',
                'apellido_materno'  => null,
                'ci'                => '9875528',
                'complemento'       => null,
                'nro_celular'       => '70000000',
                'direccion'         => 'Oficina Central',
                'email'             => 'admin@sistema.com',
                'cargo'             => 'Administrador',
                'nivel_academico'   => 'Licenciado',
                'profesion'         => 'Administrador del Sistema',
                'estado'            => true,
                'password'          => Hash::make('Admin123'), // Cumple reglas
            ]
        );

        // 🔹 FISIOTERAPEUTA
        User::firstOrCreate(
            ['ci' => '20000002'],
            [
                'nombres'           => 'Nicole Gorgette',
                'apellido_paterno'  => 'Castillo',
                'apellido_materno'  => 'Gallardo',
                'ci'                => '8437317',
                'complemento'       =>  null,
                'nro_celular'       => '61116754',
                'direccion'         => 'Zona Vino Tinto',
                'email'             => 'gorgettegorte@gmail.com',
                'cargo'             => 'Fisioterapeuta',
                'nivel_academico'   => 'Licenciado',
                'profesion'         => 'Fisioterapeuta',
                'estado'            => true,
                'password'          => Hash::make('Fisio123'),
            ]
        );

/*         // 🔹 INTERNO
        User::firstOrCreate(
            ['ci' => '30000003'],
            [
                'nombres'           => 'María',
                'apellido_paterno'  => 'Quispe',
                'apellido_materno'  => 'Choque',
                'ci'                => '9875529',
                'complemento'       => null,
                'nro_celular'       => '70987654',
                'direccion'         => 'Villa Victoria',
                'email'             => 'interno@sistema.com',
                'cargo'             => 'Interno',
                'nivel_academico'   => 'Universitario',
                'profesion'         => 'Estudiante de Fisioterapia',
                'estado'            => true,
                'password'          => Hash::make('Interno123'),
            ]
        ); */

        /*
        |--------------------------------------------------------------------------
        | OTROS SEEDERS DEL SISTEMA
        |--------------------------------------------------------------------------
        */

        /* $this->call(PatientSeeder::class);
        $this->call(EvaluationSeeder::class); */
    }
}

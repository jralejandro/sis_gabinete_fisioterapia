<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;

class PatientSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        // Obtener el usuario admin
        $admin = User::where('ci', '9875528')->first();

        Patient::factory()->count(100)->create([
            'user_id' => $admin->id
        ]);
    }
}

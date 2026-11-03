<?php

namespace App\Enums;

enum Cargo: string
{
    case ADMIN = 'admin';
    case KINESIOLOGO = 'kinesiologo';
    case ESTUDIANTE = 'estudiante';

    // Un método helper muy pro para revisar múltiples roles
    public function in(array $cargos): bool
    {
        return in_array($this, $cargos);
    }
}

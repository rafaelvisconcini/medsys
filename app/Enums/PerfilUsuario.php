<?php

namespace App\Enums;

enum PerfilUsuario: string
{
    case Admin         = 'admin';
    case Profissional  = 'profissional';
    case Recepcionista = 'recepcionista';
    case Financeiro    = 'financeiro';

    public function label(): string
    {
        return match($this) {
            self::Admin         => 'Administrador',
            self::Profissional  => 'Profissional',
            self::Recepcionista => 'Recepcionista',
            self::Financeiro    => 'Financeiro',
        };
    }
}

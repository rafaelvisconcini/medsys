<?php

namespace App\Enums;

enum PerfilUsuario: string
{
    case Proprietario  = 'proprietario';
    case Admin         = 'admin';
    case Profissional  = 'profissional';
    case Recepcionista = 'recepcionista';
    case Financeiro    = 'financeiro';

    public function label(): string
    {
        return match($this) {
            self::Proprietario  => 'Proprietário',
            self::Admin         => 'Administrador',
            self::Profissional  => 'Profissional',
            self::Recepcionista => 'Recepcionista',
            self::Financeiro    => 'Financeiro',
        };
    }

    public function temAcessoAdmin(): bool
    {
        return in_array($this, [self::Proprietario, self::Admin]);
    }
}

<?php

namespace App\Policies;

use App\Enums\PerfilUsuario;
use App\Models\Paciente;
use App\Models\User;

class PacientePolicy
{
    public function viewAny(User $user): bool
    {
        // Financeiro pode listar apenas para associar cobranças
        return $user->ativo;
    }

    public function view(User $user, Paciente $paciente): bool
    {
        if ($user->perfil === PerfilUsuario::Financeiro) {
            return true;
        }

        return $user->perfil->temAcessoAdmin() || in_array($user->perfil, [
            PerfilUsuario::Profissional,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->perfil->temAcessoAdmin() || $user->perfil === PerfilUsuario::Recepcionista;
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $user->perfil->temAcessoAdmin() || $user->perfil === PerfilUsuario::Recepcionista;
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->perfil->temAcessoAdmin();
    }
}

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
            // Financeiro vê apenas nome + contato do responsável (filtrado na view)
            return true;
        }

        return in_array($user->perfil, [
            PerfilUsuario::Admin,
            PerfilUsuario::Profissional,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function create(User $user): bool
    {
        return in_array($user->perfil, [
            PerfilUsuario::Admin,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return in_array($user->perfil, [
            PerfilUsuario::Admin,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        // Apenas admin pode desativar paciente
        return $user->perfil === PerfilUsuario::Admin;
    }
}

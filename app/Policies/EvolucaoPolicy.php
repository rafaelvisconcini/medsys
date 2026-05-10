<?php

namespace App\Policies;

use App\Enums\PerfilUsuario;
use App\Models\Evolucao;
use App\Models\User;

class EvolucaoPolicy
{
    // Profissional só lê e escreve evoluções onde é o autor
    public function viewAny(User $user): bool
    {
        return in_array($user->perfil, [PerfilUsuario::Admin, PerfilUsuario::Profissional]);
    }

    public function view(User $user, Evolucao $evolucao): bool
    {
        if ($user->perfil === PerfilUsuario::Admin) return true;

        // Profissional só vê sua própria especialidade
        return $user->profissional?->id === $evolucao->profissional_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->perfil, [PerfilUsuario::Admin, PerfilUsuario::Profissional]);
    }

    public function update(User $user, Evolucao $evolucao): bool
    {
        if ($user->perfil === PerfilUsuario::Admin) return true;

        // Só o autor pode editar
        return $user->profissional?->id === $evolucao->profissional_id;
    }

    public function delete(User $user, Evolucao $evolucao): bool
    {
        // Apenas admin pode remover evoluções (dado clínico sensível)
        return $user->perfil === PerfilUsuario::Admin;
    }
}

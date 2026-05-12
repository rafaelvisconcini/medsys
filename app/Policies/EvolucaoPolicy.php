<?php

namespace App\Policies;

use App\Enums\PerfilUsuario;
use App\Models\Evolucao;
use App\Models\User;

class EvolucaoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->perfil->temAcessoAdmin() || $user->perfil === PerfilUsuario::Profissional;
    }

    public function view(User $user, Evolucao $evolucao): bool
    {
        if ($user->perfil->temAcessoAdmin()) return true;

        return $user->profissional?->id === $evolucao->profissional_id;
    }

    public function create(User $user): bool
    {
        return $user->perfil->temAcessoAdmin() || $user->perfil === PerfilUsuario::Profissional;
    }

    public function update(User $user, Evolucao $evolucao): bool
    {
        if ($user->perfil->temAcessoAdmin()) return true;

        return $user->profissional?->id === $evolucao->profissional_id;
    }

    public function delete(User $user, Evolucao $evolucao): bool
    {
        return $user->perfil->temAcessoAdmin();
    }
}

<?php

namespace App\Policies;

use App\Enums\PerfilUsuario;
use App\Models\Sessao;
use App\Models\User;

class SessaoPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->perfil, [
            PerfilUsuario::Admin,
            PerfilUsuario::Profissional,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function view(User $user, Sessao $sessao): bool
    {
        if (in_array($user->perfil, [PerfilUsuario::Admin, PerfilUsuario::Recepcionista])) return true;

        // Profissional só vê sessões onde é o responsável
        return $user->profissional?->id === $sessao->profissional_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->perfil, [
            PerfilUsuario::Admin,
            PerfilUsuario::Profissional,
            PerfilUsuario::Recepcionista,
        ]);
    }

    public function update(User $user, Sessao $sessao): bool
    {
        if (in_array($user->perfil, [PerfilUsuario::Admin, PerfilUsuario::Recepcionista])) return true;

        return $user->profissional?->id === $sessao->profissional_id;
    }

    public function delete(User $user, Sessao $sessao): bool
    {
        return in_array($user->perfil, [PerfilUsuario::Admin, PerfilUsuario::Recepcionista]);
    }
}

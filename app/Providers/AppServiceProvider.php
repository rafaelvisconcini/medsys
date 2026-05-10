<?php

namespace App\Providers;

use App\Enums\PerfilUsuario;
use App\Models\Evolucao;
use App\Models\Paciente;
use App\Models\Sessao;
use App\Models\User;
use App\Observers\SessaoObserver;
use App\Policies\EvolucaoPolicy;
use App\Policies\PacientePolicy;
use App\Policies\SessaoPolicy;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Carbon::setLocale('pt_BR');

        Sessao::observe(SessaoObserver::class);

        // 10 tentativas de login por minuto por IP
        RateLimiter::for('login', fn($req) =>
            Limit::perMinute(10)->by($req->ip())
        );

        // Policies de linha (row-level security)
        Gate::policy(Paciente::class, PacientePolicy::class);
        Gate::policy(Sessao::class, SessaoPolicy::class);
        Gate::policy(Evolucao::class, EvolucaoPolicy::class);

        // Gates de módulo
        Gate::define('admin', fn(User $u) =>
            $u->perfil === PerfilUsuario::Admin
        );

        Gate::define('acessar-financeiro', fn(User $u) =>
            in_array($u->perfil, [PerfilUsuario::Admin, PerfilUsuario::Financeiro])
        );

        // Prontuário: apenas admin e profissionais — cada um vê só o seu (Policy)
        Gate::define('ver-prontuario', fn(User $u) =>
            in_array($u->perfil, [PerfilUsuario::Admin, PerfilUsuario::Profissional])
        );

        // Agenda: admin, profissional e recepcionista
        Gate::define('agendar', fn(User $u) =>
            in_array($u->perfil, [PerfilUsuario::Admin, PerfilUsuario::Profissional, PerfilUsuario::Recepcionista])
        );

        // Pacientes: todos podem ver (com filtro por perfil), só admin/recep editam (Policy)
        Gate::define('gerenciar-pacientes', fn(User $u) => $u->ativo);
    }
}

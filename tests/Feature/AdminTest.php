<?php

namespace Tests\Feature;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\Profissional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'perfil'                => PerfilUsuario::Admin,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);
    }

    private function naoAdmins(): array
    {
        return [PerfilUsuario::Profissional, PerfilUsuario::Recepcionista, PerfilUsuario::Financeiro];
    }

    // ─── Controle de acesso ────────────────────────────────────────────────

    public function test_admin_acessa_lista_profissionais(): void
    {
        $this->actingAs($this->admin)
            ->get('/profissionais')
            ->assertOk();
    }

    public function test_admin_acessa_lista_usuarios(): void
    {
        $this->actingAs($this->admin)
            ->get('/usuarios')
            ->assertOk();
    }

    public function test_nao_admin_nao_acessa_profissionais(): void
    {
        foreach ($this->naoAdmins() as $perfil) {
            $user = User::factory()->create([
                'perfil'                => $perfil,
                'ativo'                 => true,
                'force_password_change' => false,
            ]);

            $this->actingAs($user)
                ->get('/profissionais')
                ->assertForbidden();
        }
    }

    public function test_nao_admin_nao_acessa_usuarios(): void
    {
        foreach ($this->naoAdmins() as $perfil) {
            $user = User::factory()->create([
                'perfil'                => $perfil,
                'ativo'                 => true,
                'force_password_change' => false,
            ]);

            $this->actingAs($user)
                ->get('/usuarios')
                ->assertForbidden();
        }
    }

    // ─── CRUD Profissional ─────────────────────────────────────────────────

    public function test_admin_cria_profissional(): void
    {
        $this->actingAs($this->admin)
            ->post('/profissionais', [
                'name'                   => 'Dra. Fernanda',
                'email'                  => 'fernanda@clinica.com',
                'especialidade'          => Especialidade::Psicologia->value,
                'registro_profissional'  => 'CRP 07/12345',
                'duracao_sessao_min'     => 50,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('profissionais', [
            'registro_profissional' => 'CRP 07/12345',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'fernanda@clinica.com']);
    }

    public function test_admin_atualiza_profissional(): void
    {
        $prof = Profissional::factory()->create(['ativo' => true]);

        $this->actingAs($this->admin)
            ->put('/profissionais/' . $prof->id, [
                'name'                   => $prof->user->name,
                'email'                  => $prof->user->email,
                'especialidade'          => $prof->especialidade->value,
                'registro_profissional'  => 'CRFa 9999-SP',
                'duracao_sessao_min'     => 45,
                'ativo'                  => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('profissionais', [
            'id'                    => $prof->id,
            'registro_profissional' => 'CRFa 9999-SP',
            'duracao_sessao_min'    => 45,
        ]);
    }

    // ─── CRUD Usuário ──────────────────────────────────────────────────────

    public function test_admin_cria_usuario(): void
    {
        $this->actingAs($this->admin)
            ->post('/usuarios', [
                'name'     => 'Nova Recepcionista',
                'email'    => 'nova@clinica.com',
                'perfil'   => PerfilUsuario::Recepcionista->value,
                'password' => 'Senha@2026',
                'password_confirmation' => 'Senha@2026',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email'  => 'nova@clinica.com',
            'perfil' => PerfilUsuario::Recepcionista->value,
        ]);
    }

    public function test_admin_desativa_usuario(): void
    {
        $user = User::factory()->create([
            'perfil'                => PerfilUsuario::Recepcionista,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        // Omitir 'ativo' equivale a desmarcar o checkbox — controller usa $request->has('ativo')
        $this->actingAs($this->admin)
            ->put('/usuarios/' . $user->id, [
                'name'   => $user->name,
                'email'  => $user->email,
                'perfil' => $user->perfil->value,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'ativo' => 0]);
    }

    public function test_email_duplicado_rejeitado_ao_criar_usuario(): void
    {
        $existing = User::factory()->create(['email' => 'existe@clinica.com']);

        $this->actingAs($this->admin)
            ->post('/usuarios', [
                'name'     => 'Outro',
                'email'    => 'existe@clinica.com',
                'perfil'   => PerfilUsuario::Recepcionista->value,
                'password' => 'Senha@2026',
                'password_confirmation' => 'Senha@2026',
            ])
            ->assertSessionHasErrors('email');
    }

    // ─── Agenda Config ─────────────────────────────────────────────────────

    public function test_admin_acessa_agenda_config(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/agenda-config')
            ->assertOk();
    }

    public function test_nao_admin_nao_acessa_agenda_config(): void
    {
        $user = User::factory()->create([
            'perfil'                => PerfilUsuario::Recepcionista,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin/agenda-config')
            ->assertForbidden();
    }

    // ─── Bloqueios ─────────────────────────────────────────────────────────

    public function test_admin_cria_bloqueio(): void
    {
        $prof = Profissional::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/bloqueios', [
                'profissional_id' => $prof->id,
                'data_inicio'     => now()->addDays(5)->format('Y-m-d'),
                'data_fim'        => now()->addDays(10)->format('Y-m-d'),
                'motivo'          => 'Férias',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('agenda_bloqueios', [
            'profissional_id' => $prof->id,
            'motivo'          => 'Férias',
        ]);
    }

    public function test_admin_remove_bloqueio(): void
    {
        $prof = Profissional::factory()->create();

        $bloqueio = \App\Models\AgendaBloqueio::create([
            'profissional_id' => $prof->id,
            'data_inicio'     => now()->addDays(5),
            'data_fim'        => now()->addDays(10),
            'motivo'          => 'Para deletar',
        ]);

        $this->actingAs($this->admin)
            ->delete('/admin/bloqueios/' . $bloqueio->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('agenda_bloqueios', ['id' => $bloqueio->id]);
    }
}

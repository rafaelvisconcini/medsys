<?php

namespace Tests\Feature;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\Evolucao;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Prontuario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProntuarioTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userProf;
    private Profissional $profissional;
    private Paciente $paciente;
    private Prontuario $prontuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'perfil'                => PerfilUsuario::Admin,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->userProf = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->profissional = Profissional::factory()->create([
            'user_id'       => $this->userProf->id,
            'especialidade' => Especialidade::Psicologia,
        ]);

        $this->paciente   = Paciente::factory()->create();
        $this->prontuario = $this->paciente->prontuario()->firstOrCreate([
            'numero' => '2026000001',
        ]);
    }

    // ─── Acesso ao prontuário ───────────────────────────────────────────────

    public function test_admin_acessa_prontuario(): void
    {
        $this->actingAs($this->admin)
            ->get('/prontuarios/' . $this->paciente->id)
            ->assertOk();
    }

    public function test_profissional_acessa_prontuario(): void
    {
        $this->actingAs($this->userProf)
            ->get('/prontuarios/' . $this->paciente->id)
            ->assertOk();
    }

    public function test_recepcionista_nao_acessa_prontuario(): void
    {
        $recep = User::factory()->create([
            'perfil'                => PerfilUsuario::Recepcionista,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($recep)
            ->get('/prontuarios/' . $this->paciente->id)
            ->assertForbidden();
    }

    // ─── Evoluções ─────────────────────────────────────────────────────────

    public function test_profissional_cria_evolucao(): void
    {
        $this->actingAs($this->userProf)
            ->post('/prontuarios/' . $this->prontuario->id . '/evolucoes', [
                'especialidade' => Especialidade::Psicologia->value,
                'data_hora'     => now()->format('Y-m-d H:i'),
                'descricao'     => 'Paciente demonstrou boa resposta.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('evolucoes', [
            'prontuario_id'  => $this->prontuario->id,
            'profissional_id' => $this->profissional->id,
            'descricao'      => 'Paciente demonstrou boa resposta.',
        ]);
    }

    private function evolucaoUrl(Evolucao $ev, string $suffix = ''): string
    {
        return '/prontuarios/' . $ev->prontuario_id . '/evolucoes/' . $ev->id . $suffix;
    }

    public function test_profissional_edita_propria_evolucao(): void
    {
        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $this->profissional->id,
            'especialidade'   => Especialidade::Psicologia,
            'data_hora'       => now(),
            'descricao'       => 'Texto original.',
        ]);

        $this->actingAs($this->userProf)
            ->put($this->evolucaoUrl($evolucao), [
                'especialidade' => Especialidade::Psicologia->value,
                'data_hora'     => now()->format('Y-m-d H:i'),
                'descricao'     => 'Texto corrigido.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('evolucoes', ['id' => $evolucao->id, 'descricao' => 'Texto corrigido.']);
    }

    public function test_profissional_nao_edita_evolucao_de_outro(): void
    {
        $outroUser = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);
        $outroProfissional = Profissional::factory()->create([
            'user_id'       => $outroUser->id,
            'especialidade' => Especialidade::Fonoaudiologia,
        ]);

        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $outroProfissional->id,
            'especialidade'   => Especialidade::Fonoaudiologia,
            'data_hora'       => now(),
            'descricao'       => 'Evolução do outro profissional.',
        ]);

        $this->actingAs($this->userProf)
            ->put($this->evolucaoUrl($evolucao), [
                'especialidade' => Especialidade::Psicologia->value,
                'data_hora'     => now()->format('Y-m-d H:i'),
                'descricao'     => 'Tentativa de invasão.',
            ])
            ->assertForbidden();
    }

    public function test_profissional_nao_deleta_evolucao(): void
    {
        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $this->profissional->id,
            'especialidade'   => Especialidade::Psicologia,
            'data_hora'       => now(),
            'descricao'       => 'Para deletar.',
        ]);

        $this->actingAs($this->userProf)
            ->delete($this->evolucaoUrl($evolucao))
            ->assertForbidden();
    }

    public function test_admin_deleta_evolucao(): void
    {
        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $this->profissional->id,
            'especialidade'   => Especialidade::Psicologia,
            'data_hora'       => now(),
            'descricao'       => 'Evolução a ser removida.',
        ]);

        $this->actingAs($this->admin)
            ->delete($this->evolucaoUrl($evolucao))
            ->assertRedirect();

        $this->assertDatabaseMissing('evolucoes', ['id' => $evolucao->id]);
    }

    public function test_profissional_vê_propria_evolucao(): void
    {
        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $this->profissional->id,
            'especialidade'   => Especialidade::Psicologia,
            'data_hora'       => now(),
            'descricao'       => 'Minha evolução.',
        ]);

        $this->actingAs($this->userProf)
            ->get($this->evolucaoUrl($evolucao))
            ->assertOk();
    }

    public function test_profissional_nao_ve_evolucao_de_outro(): void
    {
        $outroUser = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);
        $outroProfissional = Profissional::factory()->create([
            'user_id' => $outroUser->id,
        ]);

        $evolucao = Evolucao::create([
            'prontuario_id'   => $this->prontuario->id,
            'profissional_id' => $outroProfissional->id,
            'especialidade'   => Especialidade::Fonoaudiologia,
            'data_hora'       => now(),
            'descricao'       => 'Evolução do outro.',
        ]);

        $this->actingAs($this->userProf)
            ->get($this->evolucaoUrl($evolucao))
            ->assertForbidden();
    }
}

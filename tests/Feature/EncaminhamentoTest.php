<?php

namespace Tests\Feature;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\Encaminhamento;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Prontuario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncaminhamentoTest extends TestCase
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

    private function dadosEncaminhamento(array $extra = []): array
    {
        return array_merge([
            'para_especialidade' => 'Neuropediatria',
            'motivo'             => 'Avaliação neurológica complementar.',
            'data'               => now()->format('Y-m-d'),
        ], $extra);
    }

    public function test_profissional_cria_encaminhamento(): void
    {
        $this->actingAs($this->userProf)
            ->post('/prontuarios/' . $this->prontuario->id . '/encaminhamentos', $this->dadosEncaminhamento())
            ->assertRedirect();

        $this->assertDatabaseHas('encaminhamentos', [
            'prontuario_id'      => $this->prontuario->id,
            'profissional_id'    => $this->profissional->id,
            'para_especialidade' => 'Neuropediatria',
            'status'             => 'pendente',
        ]);
    }

    public function test_usuario_sem_profissional_nao_cria_encaminhamento(): void
    {
        // Admin não tem profissional vinculado
        $this->actingAs($this->admin)
            ->post('/prontuarios/' . $this->prontuario->id . '/encaminhamentos', $this->dadosEncaminhamento())
            ->assertForbidden();
    }

    public function test_status_atualizado_para_realizado(): void
    {
        $enc = Encaminhamento::create([
            'prontuario_id'      => $this->prontuario->id,
            'profissional_id'    => $this->profissional->id,
            'para_especialidade' => 'Fonoaudiologia',
            'motivo'             => 'Teste',
            'data'               => now(),
            'status'             => 'pendente',
        ]);

        $this->actingAs($this->userProf)
            ->patch('/prontuarios/encaminhamentos/' . $enc->id, ['status' => 'realizado'])
            ->assertRedirect();

        $this->assertDatabaseHas('encaminhamentos', ['id' => $enc->id, 'status' => 'realizado']);
    }

    public function test_encaminhamento_removido(): void
    {
        $enc = Encaminhamento::create([
            'prontuario_id'      => $this->prontuario->id,
            'profissional_id'    => $this->profissional->id,
            'para_especialidade' => 'Psiquiatria',
            'motivo'             => 'Para remover',
            'data'               => now(),
            'status'             => 'pendente',
        ]);

        $this->actingAs($this->admin)
            ->delete('/prontuarios/encaminhamentos/' . $enc->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('encaminhamentos', ['id' => $enc->id]);
    }

    public function test_validacao_campos_obrigatorios(): void
    {
        $this->actingAs($this->userProf)
            ->post('/prontuarios/' . $this->prontuario->id . '/encaminhamentos', [])
            ->assertSessionHasErrors(['para_especialidade', 'motivo', 'data']);
    }
}

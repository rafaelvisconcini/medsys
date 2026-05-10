<?php

namespace Tests\Feature;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Sessao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SessaoAgendamentoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Profissional $profissional;
    private Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'perfil'                => PerfilUsuario::Admin,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $userProf = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->profissional = Profissional::factory()->create([
            'user_id'            => $userProf->id,
            'especialidade'      => Especialidade::Fonoaudiologia,
            'duracao_sessao_min' => 50,
        ]);

        $this->paciente = Paciente::factory()->create(['responsavel_email' => 'resp@teste.com']);
    }

    public function test_criar_sessao_enfileira_email(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)
            ->post('/sessoes', [
                'paciente_id'     => $this->paciente->id,
                'profissional_id' => $this->profissional->id,
                'data_hora'       => now()->addDay()->format('Y-m-d H:i'),
                'duracao_min'     => 50,
                'especialidade'   => Especialidade::Fonoaudiologia->value,
                'tipo'            => 'avulsa',
            ])
            ->assertRedirect(route('agenda.index'));

        Mail::assertQueued(\App\Mail\SessaoConfirmadaMail::class);
    }

    public function test_conflito_de_horario_impede_agendamento(): void
    {
        $dataHora = now()->addDay()->format('Y-m-d') . ' 09:00';

        // Cria sessão existente
        Sessao::create([
            'paciente_id'    => $this->paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => $dataHora,
            'duracao_min'    => 50,
            'tipo'           => 'avulsa',
            'status'         => 'agendada',
            'agendado_por'   => $this->admin->id,
        ]);

        // Tenta criar sessão no mesmo horário
        $this->actingAs($this->admin)
            ->post('/sessoes', [
                'paciente_id'     => $this->paciente->id,
                'profissional_id' => $this->profissional->id,
                'data_hora'       => $dataHora,
                'duracao_min'     => 50,
                'especialidade'   => Especialidade::Fonoaudiologia->value,
                'tipo'            => 'avulsa',
            ])
            ->assertSessionHasErrors('data_hora');
    }

    public function test_financeiro_nao_pode_agendar(): void
    {
        $fin = User::factory()->create([
            'perfil'                => PerfilUsuario::Financeiro,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($fin)
            ->get('/sessoes/create')
            ->assertForbidden();
    }

    public function test_atualizar_status_para_confirmada_enfileira_email(): void
    {
        Mail::fake();

        $sessao = Sessao::create([
            'paciente_id'    => $this->paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => now()->addDay(),
            'duracao_min'    => 50,
            'tipo'           => 'avulsa',
            'status'         => 'agendada',
            'agendado_por'   => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->patch("/sessoes/{$sessao->id}/status", ['status' => 'confirmada'])
            ->assertJson(['ok' => true]);

        Mail::assertQueued(\App\Mail\SessaoConfirmadaMail::class);
    }
}

<?php

namespace Tests\Unit;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\AgendaConfiguracao;
use App\Models\Profissional;
use App\Models\Sessao;
use App\Models\User;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaServiceTest extends TestCase
{
    use RefreshDatabase;

    private AgendaService $service;
    private Profissional $profissional;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AgendaService();

        $user = User::factory()->create([
            'perfil' => PerfilUsuario::Profissional,
            'ativo'  => true,
        ]);

        $this->profissional = Profissional::factory()->create([
            'user_id'            => $user->id,
            'especialidade'      => Especialidade::Fonoaudiologia,
            'duracao_sessao_min' => 50,
            'ativo'              => true,
        ]);
    }

    public function test_gerar_slots_retorna_vazio_sem_configuracao(): void
    {
        $slots = $this->service->gerarSlots($this->profissional, Carbon::parse('2025-06-16')); // segunda
        $this->assertCount(0, $slots);
    }

    public function test_gerar_slots_com_configuracao_ativa(): void
    {
        // Segunda-feira = dia 1
        AgendaConfiguracao::create([
            'profissional_id'  => $this->profissional->id,
            'dia_semana'       => 1,
            'hora_inicio'      => '08:00',
            'hora_fim'         => '12:00',
            'duracao_slot_min' => 60,
            'ativo'            => true,
        ]);

        $slots = $this->service->gerarSlots($this->profissional, Carbon::parse('2025-06-16')); // segunda

        $this->assertCount(4, $slots); // 08:00, 09:00, 10:00, 11:00
        $this->assertTrue($slots->first()['disponivel']);
    }

    public function test_horario_livre_sem_conflito(): void
    {
        $livre = $this->service->horarioLivre(
            $this->profissional->id,
            Carbon::parse('2025-06-16 09:00'),
            50
        );

        $this->assertTrue($livre);
    }

    public function test_horario_livre_detecta_conflito(): void
    {
        // Cria uma paciente e sessao
        $paciente = \App\Models\Paciente::factory()->create();

        Sessao::create([
            'paciente_id'    => $paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => '2025-06-16 09:00:00',
            'duracao_min'    => 50,
            'tipo'           => 'avulsa',
            'status'         => 'agendada',
            'agendado_por'   => 1,
        ]);

        // Tenta agendar no mesmo horário
        $livre = $this->service->horarioLivre(
            $this->profissional->id,
            Carbon::parse('2025-06-16 09:00'),
            50
        );

        $this->assertFalse($livre);
    }

    public function test_horario_livre_ignora_sessao_cancelada(): void
    {
        $paciente = \App\Models\Paciente::factory()->create();

        Sessao::create([
            'paciente_id'    => $paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => '2025-06-16 09:00:00',
            'duracao_min'    => 50,
            'tipo'           => 'avulsa',
            'status'         => 'cancelada',
            'agendado_por'   => 1,
        ]);

        $livre = $this->service->horarioLivre(
            $this->profissional->id,
            Carbon::parse('2025-06-16 09:00'),
            50
        );

        $this->assertTrue($livre); // cancelada não bloqueia
    }

    public function test_horario_livre_exclui_sessao_ao_editar(): void
    {
        $paciente = \App\Models\Paciente::factory()->create();

        $sessao = Sessao::create([
            'paciente_id'    => $paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => '2025-06-16 09:00:00',
            'duracao_min'    => 50,
            'tipo'           => 'avulsa',
            'status'         => 'agendada',
            'agendado_por'   => 1,
        ]);

        // Editar a própria sessão deve retornar livre
        $livre = $this->service->horarioLivre(
            $this->profissional->id,
            Carbon::parse('2025-06-16 09:00'),
            50,
            $sessao->id
        );

        $this->assertTrue($livre);
    }

    public function test_slot_marcado_como_ocupado_quando_ha_sessao(): void
    {
        AgendaConfiguracao::create([
            'profissional_id'  => $this->profissional->id,
            'dia_semana'       => 1,
            'hora_inicio'      => '08:00',
            'hora_fim'         => '10:00',
            'duracao_slot_min' => 60,
            'ativo'            => true,
        ]);

        $paciente = \App\Models\Paciente::factory()->create();

        Sessao::create([
            'paciente_id'    => $paciente->id,
            'profissional_id'=> $this->profissional->id,
            'especialidade'  => Especialidade::Fonoaudiologia->value,
            'data_hora'      => '2025-06-16 08:00:00',
            'duracao_min'    => 60,
            'tipo'           => 'avulsa',
            'status'         => 'agendada',
            'agendado_por'   => 1,
        ]);

        $slots = $this->service->gerarSlots($this->profissional, Carbon::parse('2025-06-16'));

        $this->assertFalse($slots->firstWhere('hora', '08:00')['disponivel']);
        $this->assertTrue($slots->firstWhere('hora', '09:00')['disponivel']);
    }
}

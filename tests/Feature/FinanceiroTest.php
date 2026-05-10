<?php

namespace Tests\Feature;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\ContaReceber;
use App\Models\Contrato;
use App\Models\Paciente;
use App\Models\Parcela;
use App\Models\Profissional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $financeiro;
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

        $this->financeiro = User::factory()->create([
            'perfil'                => PerfilUsuario::Financeiro,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $userProf = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->profissional = Profissional::factory()->create([
            'user_id'       => $userProf->id,
            'especialidade' => Especialidade::Fonoaudiologia,
        ]);

        $this->paciente = Paciente::factory()->create();
    }

    // ─── Acesso ────────────────────────────────────────────────────────────

    public function test_financeiro_acessa_dashboard(): void
    {
        $this->actingAs($this->financeiro)
            ->get('/financeiro')
            ->assertOk();
    }

    public function test_profissional_nao_acessa_financeiro(): void
    {
        $prof = User::factory()->create([
            'perfil'                => PerfilUsuario::Profissional,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($prof)
            ->get('/financeiro')
            ->assertForbidden();
    }

    public function test_recepcionista_nao_acessa_financeiro(): void
    {
        $recep = User::factory()->create([
            'perfil'                => PerfilUsuario::Recepcionista,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($recep)
            ->get('/financeiro')
            ->assertForbidden();
    }

    // ─── Contratos ─────────────────────────────────────────────────────────

    public function test_criacao_contrato(): void
    {
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contratos', [
                'paciente_id'        => $this->paciente->id,
                'profissional_id'    => $this->profissional->id,
                'especialidade'      => Especialidade::Fonoaudiologia->value,
                'valor_mensal'       => '500.00',
                'dia_vencimento'     => 10,
                'sessoes_por_semana' => 2,
                'data_inicio'        => now()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contratos', [
            'paciente_id'     => $this->paciente->id,
            'profissional_id' => $this->profissional->id,
            'status'          => 'ativo',
        ]);
    }

    public function test_criacao_contrato_gera_primeira_cobranca(): void
    {
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contratos', [
                'paciente_id'              => $this->paciente->id,
                'profissional_id'          => $this->profissional->id,
                'especialidade'            => Especialidade::Fonoaudiologia->value,
                'valor_mensal'             => '600.00',
                'dia_vencimento'           => 5,
                'sessoes_por_semana'       => 1,
                'data_inicio'              => now()->format('Y-m-d'),
                'gerar_primeira_cobranca'  => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contas_receber', [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'mensalidade',
            'status'      => 'pendente',
        ]);

        $this->assertDatabaseHas('parcelas', [
            'status' => 'pendente',
        ]);
    }

    public function test_gerar_cobranca_duplicada_e_rejeitada(): void
    {
        $contrato = Contrato::create([
            'paciente_id'        => $this->paciente->id,
            'profissional_id'    => $this->profissional->id,
            'especialidade'      => Especialidade::Fonoaudiologia->value,
            'valor_mensal'       => 500,
            'dia_vencimento'     => 10,
            'sessoes_por_semana' => 2,
            'data_inicio'        => now(),
            'status'             => 'ativo',
        ]);

        $mes = now()->format('Y-m-01');

        // Primeira geração
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contratos/' . $contrato->id . '/gerar-cobranca', [
                'referencia_mes' => $mes,
            ])
            ->assertRedirect();

        // Segunda geração do mesmo mês deve falhar com erro
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contratos/' . $contrato->id . '/gerar-cobranca', [
                'referencia_mes' => $mes,
            ])
            ->assertSessionHasErrors('referencia_mes');
    }

    // ─── Contas a Receber ──────────────────────────────────────────────────

    public function test_criacao_conta_avulsa(): void
    {
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contas', [
                'paciente_id'     => $this->paciente->id,
                'descricao'       => 'Avaliação inicial',
                'valor_total'     => '250.00',
                'tipo'            => 'avulso',
                'data_vencimento' => now()->addDays(10)->format('Y-m-d'),
                'num_parcelas'    => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contas_receber', [
            'paciente_id' => $this->paciente->id,
            'descricao'   => 'Avaliação inicial',
            'tipo'        => 'avulso',
        ]);

        $this->assertDatabaseHas('parcelas', [
            'valor'  => '250.00',
            'status' => 'pendente',
        ]);
    }

    public function test_conta_dividida_em_parcelas(): void
    {
        $this->actingAs($this->financeiro)
            ->post('/financeiro/contas', [
                'paciente_id'     => $this->paciente->id,
                'descricao'       => 'Pagamento parcelado',
                'valor_total'     => '300.00',
                'tipo'            => 'avulso',
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
                'num_parcelas'    => 3,
            ])
            ->assertRedirect();

        $conta = ContaReceber::where('descricao', 'Pagamento parcelado')->first();
        $this->assertCount(3, $conta->parcelas);

        $soma = $conta->parcelas->sum('valor');
        $this->assertEquals('300.00', number_format($soma, 2, '.', ''));
    }

    // ─── Pagamento de Parcela ──────────────────────────────────────────────

    public function test_pagamento_de_parcela_atualiza_status_conta(): void
    {
        $conta = ContaReceber::create([
            'paciente_id'     => $this->paciente->id,
            'descricao'       => 'Conta teste',
            'valor_total'     => 100,
            'tipo'            => 'avulso',
            'status'          => 'pendente',
            'data_vencimento' => now()->addDays(5),
            'criado_por'      => $this->financeiro->id,
        ]);

        $parcela = Parcela::create([
            'conta_receber_id' => $conta->id,
            'numero_parcela'   => 1,
            'valor'            => 100,
            'data_vencimento'  => now()->addDays(5),
            'status'           => 'pendente',
        ]);

        $this->actingAs($this->financeiro)
            ->post('/financeiro/contas/' . $conta->id . '/parcelas/' . $parcela->id . '/pagar', [
                'forma_pagamento' => 'pix',
                'data_pagamento'  => now()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('parcelas', ['id' => $parcela->id, 'status' => 'pago']);
        $this->assertDatabaseHas('contas_receber', ['id' => $conta->id, 'status' => 'quitado']);
    }

    public function test_pagamento_parcial_deixa_conta_como_parcial(): void
    {
        $conta = ContaReceber::create([
            'paciente_id'     => $this->paciente->id,
            'descricao'       => 'Conta 2 parcelas',
            'valor_total'     => 200,
            'tipo'            => 'avulso',
            'status'          => 'pendente',
            'data_vencimento' => now()->addDays(5),
            'criado_por'      => $this->financeiro->id,
        ]);

        $p1 = Parcela::create([
            'conta_receber_id' => $conta->id,
            'numero_parcela'   => 1,
            'valor'            => 100,
            'data_vencimento'  => now()->addDays(5),
            'status'           => 'pendente',
        ]);

        Parcela::create([
            'conta_receber_id' => $conta->id,
            'numero_parcela'   => 2,
            'valor'            => 100,
            'data_vencimento'  => now()->addDays(35),
            'status'           => 'pendente',
        ]);

        $this->actingAs($this->financeiro)
            ->post('/financeiro/contas/' . $conta->id . '/parcelas/' . $p1->id . '/pagar', [
                'forma_pagamento' => 'dinheiro',
                'data_pagamento'  => now()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contas_receber', ['id' => $conta->id, 'status' => 'parcial']);
    }

    public function test_pagamento_futuro_e_rejeitado(): void
    {
        $conta = ContaReceber::create([
            'paciente_id'     => $this->paciente->id,
            'descricao'       => 'Conta',
            'valor_total'     => 100,
            'tipo'            => 'avulso',
            'status'          => 'pendente',
            'data_vencimento' => now()->addDays(5),
            'criado_por'      => $this->financeiro->id,
        ]);

        $parcela = Parcela::create([
            'conta_receber_id' => $conta->id,
            'numero_parcela'   => 1,
            'valor'            => 100,
            'data_vencimento'  => now()->addDays(5),
            'status'           => 'pendente',
        ]);

        $this->actingAs($this->financeiro)
            ->post('/financeiro/contas/' . $conta->id . '/parcelas/' . $parcela->id . '/pagar', [
                'forma_pagamento' => 'pix',
                'data_pagamento'  => now()->addDays(5)->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('data_pagamento');
    }

    // ─── Relatórios ────────────────────────────────────────────────────────

    public function test_relatorio_inadimplencia_gera_pdf(): void
    {
        $this->actingAs($this->financeiro)
            ->get('/financeiro/relatorios/inadimplencia')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_relatorio_extrato_gera_pdf(): void
    {
        $this->actingAs($this->financeiro)
            ->get('/financeiro/relatorios/extrato?de=' . now()->startOfMonth()->format('Y-m-d') . '&ate=' . now()->format('Y-m-d'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}

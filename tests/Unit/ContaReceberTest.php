<?php

namespace Tests\Unit;

use App\Models\ContaReceber;
use App\Models\Paciente;
use App\Models\Parcela;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContaReceberTest extends TestCase
{
    use RefreshDatabase;

    private function criarConta(int $numParcelas = 2): ContaReceber
    {
        $user     = User::factory()->create();
        $paciente = Paciente::factory()->create();

        $conta = ContaReceber::create([
            'paciente_id'     => $paciente->id,
            'descricao'       => 'Teste',
            'valor_total'     => 200.00,
            'tipo'            => 'avulso',
            'status'          => 'pendente',
            'data_vencimento' => today(),
            'criado_por'      => $user->id,
        ]);

        for ($i = 1; $i <= $numParcelas; $i++) {
            Parcela::create([
                'conta_receber_id' => $conta->id,
                'numero_parcela'   => $i,
                'valor'            => 100.00,
                'data_vencimento'  => today()->addMonths($i - 1),
                'status'           => 'pendente',
            ]);
        }

        return $conta;
    }

    public function test_status_parcial_quando_uma_parcela_paga(): void
    {
        $conta = $this->criarConta(2);

        $conta->parcelas()->first()->update([
            'status'         => 'pago',
            'data_pagamento' => today(),
        ]);

        $conta->recalcularStatus();

        $this->assertEquals('parcial', $conta->fresh()->status);
    }

    public function test_status_quitado_quando_todas_pagas(): void
    {
        $conta = $this->criarConta(2);

        $conta->parcelas()->update([
            'status'         => 'pago',
            'data_pagamento' => today(),
        ]);

        $conta->recalcularStatus();
        $conta->refresh();

        $this->assertEquals('quitado', $conta->status);
        $this->assertNotNull($conta->data_liquidacao);
    }

    public function test_status_pendente_sem_pagamentos(): void
    {
        $conta = $this->criarConta(1);
        $conta->recalcularStatus();

        $this->assertEquals('pendente', $conta->fresh()->status);
    }

    public function test_parcela_vencida(): void
    {
        $parcela = new Parcela([
            'status'          => 'pendente',
            'data_vencimento' => today()->subDay(),
        ]);

        $this->assertTrue($parcela->estaVencida());
    }

    public function test_parcela_paga_nao_e_vencida(): void
    {
        $parcela = new Parcela([
            'status'          => 'pago',
            'data_vencimento' => today()->subDay(),
        ]);

        $this->assertFalse($parcela->estaVencida());
    }
}

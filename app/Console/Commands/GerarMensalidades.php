<?php

namespace App\Console\Commands;

use App\Models\ContaReceber;
use App\Models\Contrato;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GerarMensalidades extends Command
{
    protected $signature   = 'financeiro:gerar-mensalidades {--mes= : Mês de referência (YYYY-MM), padrão: mês atual}';
    protected $description = 'Gera cobranças mensais para todos os contratos ativos';

    public function handle(): int
    {
        $mesRef = $this->option('mes')
            ? Carbon::parse($this->option('mes') . '-01')->startOfMonth()
            : Carbon::today()->startOfMonth();

        $this->info("Gerando mensalidades para: {$mesRef->translatedFormat('F/Y')}");

        $contratos = Contrato::where('status', 'ativo')
            ->where('data_inicio', '<=', $mesRef->copy()->endOfMonth())
            ->where(fn($q) => $q->whereNull('data_fim')->orWhere('data_fim', '>=', $mesRef))
            ->with('paciente')
            ->get();

        $geradas = 0;
        $puladas = 0;

        foreach ($contratos as $contrato) {
            $jaExiste = ContaReceber::where('contrato_id', $contrato->id)
                ->whereYear('referencia_mes', $mesRef->year)
                ->whereMonth('referencia_mes', $mesRef->month)
                ->exists();

            if ($jaExiste) {
                $puladas++;
                continue;
            }

            $vencimento = $mesRef->copy()->setDay(
                min($contrato->dia_vencimento, $mesRef->daysInMonth)
            );

            $conta = ContaReceber::create([
                'paciente_id'     => $contrato->paciente_id,
                'contrato_id'     => $contrato->id,
                'descricao'       => 'Mensalidade ' . $mesRef->translatedFormat('F/Y') . ' — ' . $contrato->especialidade->label(),
                'valor_total'     => $contrato->valor_mensal,
                'tipo'            => 'mensalidade',
                'status'          => 'pendente',
                'data_vencimento' => $vencimento,
                'referencia_mes'  => $mesRef,
                'criado_por'      => 1,
            ]);

            Parcela::create([
                'conta_receber_id' => $conta->id,
                'numero_parcela'   => 1,
                'valor'            => $contrato->valor_mensal,
                'data_vencimento'  => $vencimento,
                'status'           => 'pendente',
            ]);

            $geradas++;
            $this->line("  ✓ {$contrato->paciente->nome} — R$ {$contrato->valor_mensal}");
        }

        $this->info("Concluído: {$geradas} geradas, {$puladas} já existiam.");
        return self::SUCCESS;
    }
}

<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContaReceber;
use App\Models\Parcela;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    public function index()
    {
        $hoje    = Carbon::today();
        $mesInicio = $hoje->copy()->startOfMonth();
        $mesFim    = $hoje->copy()->endOfMonth();

        // Resumo do mês
        $recebidoMes = Parcela::where('status', 'pago')
            ->whereBetween('data_pagamento', [$mesInicio, $mesFim])
            ->sum('valor');

        $pendenteTotal = ContaReceber::whereIn('status', ['pendente', 'parcial'])->sum('valor_total');

        $atrasadasCount = ContaReceber::whereIn('status', ['pendente', 'parcial'])
            ->where('data_vencimento', '<', $hoje)
            ->count();

        $vencendoSemana = ContaReceber::where('status', 'pendente')
            ->whereBetween('data_vencimento', [$hoje, $hoje->copy()->addDays(7)])
            ->count();

        // Contas em atraso (top 10)
        $atrasadas = ContaReceber::with('paciente')
            ->whereIn('status', ['pendente', 'parcial'])
            ->where('data_vencimento', '<', $hoje)
            ->orderBy('data_vencimento')
            ->limit(10)
            ->get();

        // Próximos vencimentos (7 dias)
        $proximasVencer = ContaReceber::with('paciente')
            ->whereIn('status', ['pendente', 'parcial'])
            ->whereBetween('data_vencimento', [$hoje, $hoje->copy()->addDays(7)])
            ->orderBy('data_vencimento')
            ->limit(10)
            ->get();

        // Últimos pagamentos
        $ultimosPagamentos = Parcela::with('conta.paciente')
            ->where('status', 'pago')
            ->orderByDesc('data_pagamento')
            ->limit(8)
            ->get();

        return view('financeiro.index', compact(
            'recebidoMes', 'pendenteTotal', 'atrasadasCount', 'vencendoSemana',
            'atrasadas', 'proximasVencer', 'ultimosPagamentos'
        ));
    }
}

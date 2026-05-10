<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContaReceber;
use App\Models\Parcela;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function inadimplencia(Request $request)
    {
        $hoje = Carbon::today();

        $contas = ContaReceber::with(['paciente', 'parcelas'])
            ->whereIn('status', ['pendente', 'parcial'])
            ->where('data_vencimento', '<', $hoje)
            ->when($request->filled('ate'), fn($q) => $q->where('data_vencimento', '>=', $request->ate))
            ->orderBy('data_vencimento')
            ->get();

        $totalEmAberto = $contas->sum(fn($c) =>
            $c->parcelas->where('status', 'pendente')->sum('valor')
        );

        $pdf = Pdf::loadView('financeiro.relatorios.inadimplencia', compact('contas', 'totalEmAberto', 'hoje'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('inadimplencia_' . $hoje->format('Y-m-d') . '.pdf');
    }

    public function extrato(Request $request)
    {
        $de  = $request->filled('de')  ? Carbon::parse($request->de)->startOfDay()  : Carbon::today()->startOfMonth();
        $ate = $request->filled('ate') ? Carbon::parse($request->ate)->endOfDay()   : Carbon::today()->endOfDay();

        $pagamentos = Parcela::with(['conta.paciente'])
            ->where('status', 'pago')
            ->whereBetween('data_pagamento', [$de, $ate])
            ->orderBy('data_pagamento')
            ->get();

        $totalRecebido = $pagamentos->sum('valor');

        $porFormaPagamento = $pagamentos->groupBy('forma_pagamento')->map->sum('valor');

        $pdf = Pdf::loadView('financeiro.relatorios.extrato', compact('pagamentos', 'totalRecebido', 'porFormaPagamento', 'de', 'ate'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('extrato_' . $de->format('Y-m-d') . '_' . $ate->format('Y-m-d') . '.pdf');
    }
}

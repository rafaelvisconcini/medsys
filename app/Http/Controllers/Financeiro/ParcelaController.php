<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContaReceber;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParcelaController extends Controller
{
    public function pagar(Request $request, ContaReceber $conta, Parcela $parcela)
    {
        if ($parcela->conta_receber_id !== $conta->id) {
            abort(404);
        }

        if ($request->isMethod('GET')) {
            return view('financeiro.contas.pagar', compact('conta', 'parcela'));
        }

        $request->validate([
            'forma_pagamento' => 'required|in:dinheiro,pix,cartao_debito,cartao_credito,transferencia,outro',
            'data_pagamento'  => 'required|date|before_or_equal:today',
            'observacoes'     => 'nullable|string|max:200',
        ], [
            'data_pagamento.before_or_equal' => 'A data de pagamento não pode ser futura.',
        ]);

        $parcela->update([
            'status'          => 'pago',
            'data_pagamento'  => $request->data_pagamento,
            'forma_pagamento' => $request->forma_pagamento,
            'observacoes'     => $request->observacoes,
            'registrado_por'  => auth()->id(),
        ]);

        $conta->recalcularStatus();

        return redirect()->route('financeiro.contas.show', $conta)
            ->with('success', 'Pagamento registrado com sucesso.');
    }
}

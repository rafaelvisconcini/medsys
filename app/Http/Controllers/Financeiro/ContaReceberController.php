<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContaReceber;
use App\Models\Paciente;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContaReceberController extends Controller
{
    public function index(Request $request)
    {
        $contas = ContaReceber::with(['paciente', 'parcelas'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->tipo, fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->search, fn($q) => $q->whereHas('paciente', fn($p) =>
                $p->where('nome', 'like', '%' . $request->search . '%')
            ))
            ->when($request->vencimento_de, fn($q) => $q->where('data_vencimento', '>=', $request->vencimento_de))
            ->when($request->vencimento_ate, fn($q) => $q->where('data_vencimento', '<=', $request->vencimento_ate))
            ->orderBy('data_vencimento')
            ->paginate(25)
            ->withQueryString();

        return view('financeiro.contas.index', compact('contas'));
    }

    public function create()
    {
        $pacientes = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        return view('financeiro.contas.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'paciente_id'     => 'required|exists:pacientes,id',
            'descricao'       => 'required|string|max:200',
            'valor_total'     => 'required|numeric|min:0.01',
            'tipo'            => 'required|in:mensalidade,avulso',
            'data_vencimento' => 'required|date',
            'num_parcelas'    => 'required|integer|min:1|max:12',
            'observacoes'     => 'nullable|string|max:1000',
        ], [
            'valor_total.min' => 'O valor deve ser maior que zero.',
        ]);

        $conta = ContaReceber::create([
            'paciente_id'     => $dados['paciente_id'],
            'descricao'       => $dados['descricao'],
            'valor_total'     => $dados['valor_total'],
            'tipo'            => $dados['tipo'],
            'status'          => 'pendente',
            'data_vencimento' => $dados['data_vencimento'],
            'observacoes'     => $dados['observacoes'] ?? null,
            'criado_por'      => auth()->id(),
        ]);

        $numParcelas  = (int) $dados['num_parcelas'];
        $valorParcela = round($dados['valor_total'] / $numParcelas, 2);
        $vencimento   = Carbon::parse($dados['data_vencimento']);

        for ($i = 1; $i <= $numParcelas; $i++) {
            $valor = ($i === $numParcelas)
                ? $dados['valor_total'] - ($valorParcela * ($numParcelas - 1))
                : $valorParcela;

            Parcela::create([
                'conta_receber_id' => $conta->id,
                'numero_parcela'   => $i,
                'valor'            => $valor,
                'data_vencimento'  => $vencimento->copy()->addMonths($i - 1),
                'status'           => 'pendente',
            ]);
        }

        return redirect()->route('financeiro.contas.show', $conta)
            ->with('success', 'Cobrança criada com sucesso.');
    }

    public function show(ContaReceber $conta)
    {
        $conta->load(['paciente', 'parcelas.registrador', 'contrato', 'criador']);
        return view('financeiro.contas.show', compact('conta'));
    }

    public function edit(ContaReceber $conta)
    {
        if ($conta->status === 'quitado') {
            return back()->withErrors(['geral' => 'Não é possível editar uma conta quitada.']);
        }
        $pacientes = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        return view('financeiro.contas.edit', compact('conta', 'pacientes'));
    }

    public function update(Request $request, ContaReceber $conta)
    {
        if ($conta->status === 'quitado') {
            return back()->withErrors(['geral' => 'Não é possível editar uma conta quitada.']);
        }

        $dados = $request->validate([
            'descricao'       => 'required|string|max:200',
            'data_vencimento' => 'required|date',
            'status'          => 'required|in:pendente,parcial,quitado,cancelado',
            'observacoes'     => 'nullable|string|max:1000',
        ]);

        $conta->update($dados);

        return redirect()->route('financeiro.contas.show', $conta)
            ->with('success', 'Cobrança atualizada.');
    }

    public function destroy(ContaReceber $conta)
    {
        if ($conta->status === 'quitado') {
            return back()->withErrors(['geral' => 'Não é possível cancelar uma conta quitada.']);
        }
        $conta->update(['status' => 'cancelado']);
        $conta->delete();

        return redirect()->route('financeiro.contas.index')
            ->with('success', 'Cobrança cancelada.');
    }
}

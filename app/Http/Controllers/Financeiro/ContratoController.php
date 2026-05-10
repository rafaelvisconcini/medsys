<?php

namespace App\Http\Controllers\Financeiro;

use App\Enums\Especialidade;
use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\ContaReceber;
use App\Models\Paciente;
use App\Models\Parcela;
use App\Models\Profissional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $contratos = Contrato::with(['paciente', 'profissional.user'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->whereHas('paciente', fn($p) =>
                $p->where('nome', 'like', '%' . $request->search . '%')
            ))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('financeiro.contratos.index', compact('contratos'));
    }

    public function create()
    {
        $pacientes     = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        $profissionais = Profissional::with('user')->where('ativo', true)->get();
        $especialidades = Especialidade::cases();
        return view('financeiro.contratos.create', compact('pacientes', 'profissionais', 'especialidades'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'paciente_id'       => 'required|exists:pacientes,id',
            'profissional_id'   => 'required|exists:profissionais,id',
            'especialidade'     => 'required|string',
            'valor_mensal'      => 'required|numeric|min:1',
            'dia_vencimento'    => 'required|integer|min:1|max:28',
            'sessoes_por_semana'=> 'required|integer|min:1|max:7',
            'data_inicio'       => 'required|date',
            'data_fim'          => 'nullable|date|after:data_inicio',
            'observacoes'       => 'nullable|string|max:1000',
            'gerar_primeira_cobranca' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($dados, $request) {
            $contrato = Contrato::create([
                'paciente_id'        => $dados['paciente_id'],
                'profissional_id'    => $dados['profissional_id'],
                'especialidade'      => $dados['especialidade'],
                'valor_mensal'       => $dados['valor_mensal'],
                'dia_vencimento'     => $dados['dia_vencimento'],
                'sessoes_por_semana' => $dados['sessoes_por_semana'],
                'data_inicio'        => $dados['data_inicio'],
                'data_fim'           => $dados['data_fim'] ?? null,
                'status'             => 'ativo',
                'observacoes'        => $dados['observacoes'] ?? null,
            ]);

            if ($request->boolean('gerar_primeira_cobranca')) {
                $this->gerarCobrancaMes($contrato, Carbon::parse($dados['data_inicio']));
            }
        });

        return redirect()->route('financeiro.contratos.index')
            ->with('success', 'Contrato criado com sucesso.');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['paciente', 'profissional.user', 'contasReceber.parcelas']);
        return view('financeiro.contratos.show', compact('contrato'));
    }

    public function edit(Contrato $contrato)
    {
        $pacientes      = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        $profissionais  = Profissional::with('user')->where('ativo', true)->get();
        $especialidades = Especialidade::cases();
        return view('financeiro.contratos.edit', compact('contrato', 'pacientes', 'profissionais', 'especialidades'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $dados = $request->validate([
            'valor_mensal'       => 'required|numeric|min:1',
            'dia_vencimento'     => 'required|integer|min:1|max:28',
            'sessoes_por_semana' => 'required|integer|min:1|max:7',
            'data_fim'           => 'nullable|date',
            'status'             => 'required|in:ativo,suspenso,encerrado',
            'observacoes'        => 'nullable|string|max:1000',
        ]);

        $contrato->update($dados);

        return redirect()->route('financeiro.contratos.show', $contrato)
            ->with('success', 'Contrato atualizado.');
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->update(['status' => 'encerrado']);
        return redirect()->route('financeiro.contratos.index')
            ->with('success', 'Contrato encerrado.');
    }

    public function gerarCobranca(Request $request, Contrato $contrato)
    {
        $request->validate(['referencia_mes' => 'required|date']);
        $mes = Carbon::parse($request->referencia_mes)->startOfMonth();

        // Verifica se já existe para este mês
        $jaExiste = ContaReceber::where('contrato_id', $contrato->id)
            ->whereYear('referencia_mes', $mes->year)
            ->whereMonth('referencia_mes', $mes->month)
            ->exists();

        if ($jaExiste) {
            return back()->withErrors(['referencia_mes' => 'Já existe cobrança para este mês neste contrato.']);
        }

        $this->gerarCobrancaMes($contrato, $mes);

        return back()->with('success', 'Cobrança gerada para ' . $mes->translatedFormat('F/Y') . '.');
    }

    private function gerarCobrancaMes(Contrato $contrato, Carbon $mes): ContaReceber
    {
        $vencimento = $mes->copy()->setDay(
            min($contrato->dia_vencimento, $mes->daysInMonth)
        );

        $conta = ContaReceber::create([
            'paciente_id'     => $contrato->paciente_id,
            'contrato_id'     => $contrato->id,
            'descricao'       => 'Mensalidade ' . $mes->translatedFormat('F/Y') . ' — ' . $contrato->especialidade->label(),
            'valor_total'     => $contrato->valor_mensal,
            'tipo'            => 'mensalidade',
            'status'          => 'pendente',
            'data_vencimento' => $vencimento,
            'referencia_mes'  => $mes,
            'criado_por'      => auth()->id() ?? 1,
        ]);

        Parcela::create([
            'conta_receber_id' => $conta->id,
            'numero_parcela'   => 1,
            'valor'            => $contrato->valor_mensal,
            'data_vencimento'  => $vencimento,
            'status'           => 'pendente',
        ]);

        return $conta;
    }
}

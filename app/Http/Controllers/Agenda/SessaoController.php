<?php

namespace App\Http\Controllers\Agenda;

use App\Enums\Especialidade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agenda\StoreSessaoRequest;
use App\Http\Requests\Agenda\UpdateSessaoRequest;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Sessao;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessaoController extends Controller
{
    public function __construct(private AgendaService $agenda) {}

    public function index()
    {
        return redirect()->route('agenda.index');
    }

    public function create(Request $request)
    {
        $pacientes     = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        $profissionais = Profissional::with('user')->where('ativo', true)->get();
        $especialidades = Especialidade::cases();

        $dataSelecionada    = $request->query('data');
        $horaSelecionada    = $request->query('hora');
        $profissionalSelect = $request->query('profissional_id');

        return view('agenda.sessoes.create', compact(
            'pacientes', 'profissionais', 'especialidades',
            'dataSelecionada', 'horaSelecionada', 'profissionalSelect'
        ));
    }

    public function store(StoreSessaoRequest $request)
    {
        $dados = $request->validated();
        $dataHora = Carbon::parse($dados['data_hora']);

        if (! $this->agenda->horarioLivre(
            $dados['profissional_id'],
            $dataHora,
            $dados['duracao_min']
        )) {
            return back()->withInput()->withErrors([
                'data_hora' => 'Horário em conflito com outra sessão deste profissional.',
            ]);
        }

        Sessao::create([
            ...$dados,
            'status'       => 'agendada',
            'gera_cobranca' => $dados['tipo'] !== 'reposicao',
            'agendado_por' => auth()->id(),
        ]);

        return redirect()->route('agenda.index')
            ->with('success', 'Sessão agendada com sucesso.');
    }

    public function edit(Sessao $sessao)
    {
        $this->authorize('update', $sessao);

        $pacientes      = Paciente::where('ativo', true)->orderBy('nome')->get(['id', 'nome']);
        $profissionais  = Profissional::with('user')->where('ativo', true)->get();
        $especialidades = Especialidade::cases();

        return view('agenda.sessoes.edit', compact('sessao', 'pacientes', 'profissionais', 'especialidades'));
    }

    public function update(UpdateSessaoRequest $request, Sessao $sessao)
    {
        $dados = $request->validated();
        $dataHora = Carbon::parse($dados['data_hora']);

        if (! $this->agenda->horarioLivre(
            $dados['profissional_id'],
            $dataHora,
            $dados['duracao_min'],
            $sessao->id
        )) {
            return back()->withInput()->withErrors([
                'data_hora' => 'Horário em conflito com outra sessão deste profissional.',
            ]);
        }

        $sessao->update($dados);

        return redirect()->route('agenda.index')
            ->with('success', 'Sessão atualizada com sucesso.');
    }

    public function destroy(Sessao $sessao)
    {
        $this->authorize('delete', $sessao);
        $sessao->delete();

        return redirect()->route('agenda.index')
            ->with('success', 'Sessão removida.');
    }

    public function atualizarStatus(Request $request, Sessao $sessao)
    {
        $this->authorize('update', $sessao);

        $request->validate([
            'status'              => 'required|in:agendada,confirmada,realizada,cancelada,faltou,reposicao',
            'motivo_cancelamento' => 'nullable|string|max:500',
        ]);

        $sessao->update([
            'status'              => $request->status,
            'motivo_cancelamento' => $request->motivo_cancelamento,
        ]);

        return response()->json(['ok' => true, 'status' => $sessao->status]);
    }
}

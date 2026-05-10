<?php

namespace App\Http\Controllers\Prontuario;

use App\Enums\Especialidade;
use App\Http\Controllers\Controller;
use App\Models\Evolucao;
use App\Models\Prontuario;
use App\Models\Sessao;
use Illuminate\Http\Request;

class EvolucaoController extends Controller
{
    public function index(Prontuario $prontuario)
    {
        return redirect()->route('prontuarios.show', $prontuario->paciente_id);
    }

    public function create(Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $especialidades = Especialidade::cases();

        $sessoesDisponiveis = Sessao::where('paciente_id', $prontuario->paciente_id)
            ->where('status', 'realizada')
            ->whereDoesntHave('evolucoes')
            ->orderByDesc('data_hora')
            ->limit(10)
            ->get();

        return view('prontuarios.evolucoes.create', compact(
            'prontuario', 'especialidades', 'sessoesDisponiveis'
        ));
    }

    public function store(Request $request, Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $profissional = auth()->user()->profissional;
        if (! $profissional) {
            abort(403, 'Apenas profissionais podem registrar evoluções.');
        }

        $dados = $request->validate([
            'sessao_id'             => 'nullable|exists:sessoes,id',
            'especialidade'         => 'required|string',
            'data_hora'             => 'required|date',
            'descricao'             => 'required|string',
            'objetivos_trabalhados' => 'nullable|string',
            'resposta_paciente'     => 'nullable|string',
            'proximos_objetivos'    => 'nullable|string',
            'cids'                  => 'nullable|string',
        ]);

        $cids = array_filter(array_map('trim', explode(',', $dados['cids'] ?? '')));

        Evolucao::create([
            'prontuario_id'         => $prontuario->id,
            'sessao_id'             => $dados['sessao_id'] ?? null,
            'profissional_id'       => $profissional->id,
            'especialidade'         => $dados['especialidade'],
            'data_hora'             => $dados['data_hora'],
            'descricao'             => $dados['descricao'],
            'objetivos_trabalhados' => $dados['objetivos_trabalhados'] ?? null,
            'resposta_paciente'     => $dados['resposta_paciente'] ?? null,
            'proximos_objetivos'    => $dados['proximos_objetivos'] ?? null,
            'cids'                  => $cids ?: null,
        ]);

        return redirect()->route('prontuarios.show', $prontuario->paciente_id)
            ->with('success', 'Evolução registrada.');
    }

    // show, edit, update, destroy recebem {prontuario} e {evolucao} da rota aninhada.
    // Apenas $evolucao é usado na lógica; $prontuario satisfaz o route model binding.

    public function show(Prontuario $prontuario, Evolucao $evolucao)
    {
        $this->authorize('view', $evolucao);
        $evolucao->load(['profissional.user', 'sessao', 'prontuario.paciente']);
        return view('prontuarios.evolucoes.show', compact('evolucao'));
    }

    public function edit(Prontuario $prontuario, Evolucao $evolucao)
    {
        $this->authorize('update', $evolucao);
        $especialidades = Especialidade::cases();
        $evolucao->load(['prontuario.paciente', 'sessao']);
        return view('prontuarios.evolucoes.edit', compact('evolucao', 'especialidades'));
    }

    public function update(Request $request, Prontuario $prontuario, Evolucao $evolucao)
    {
        $this->authorize('update', $evolucao);

        $dados = $request->validate([
            'especialidade'         => 'required|string',
            'data_hora'             => 'required|date',
            'descricao'             => 'required|string',
            'objetivos_trabalhados' => 'nullable|string',
            'resposta_paciente'     => 'nullable|string',
            'proximos_objetivos'    => 'nullable|string',
            'cids'                  => 'nullable|string',
        ]);

        $cids = array_filter(array_map('trim', explode(',', $dados['cids'] ?? '')));

        $evolucao->update([
            ...$dados,
            'objetivos_trabalhados' => $dados['objetivos_trabalhados'] ?? null,
            'resposta_paciente'     => $dados['resposta_paciente'] ?? null,
            'proximos_objetivos'    => $dados['proximos_objetivos'] ?? null,
            'cids'                  => $cids ?: null,
        ]);

        return redirect()->route('prontuarios.show', $evolucao->prontuario->paciente_id)
            ->with('success', 'Evolução atualizada.');
    }

    public function destroy(Prontuario $prontuario, Evolucao $evolucao)
    {
        $this->authorize('delete', $evolucao);
        $pacienteId = $evolucao->prontuario->paciente_id;
        $evolucao->delete();

        return redirect()->route('prontuarios.show', $pacienteId)
            ->with('success', 'Evolução removida.');
    }
}

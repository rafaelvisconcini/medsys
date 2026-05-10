<?php

namespace App\Http\Controllers\Prontuario;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Http\Controllers\Controller;
use App\Models\PlanoTerapeutico;
use App\Models\Profissional;
use App\Models\Prontuario;
use Illuminate\Http\Request;

class PlanoTerapeuticoController extends Controller
{
    public function create(Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $profissionais  = Profissional::with('user')->where('ativo', true)->orderBy('id')->get();
        $especialidades = Especialidade::cases();

        return view('prontuarios.planos.create', compact('prontuario', 'profissionais', 'especialidades'));
    }

    public function store(Request $request, Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $dados = $request->validate([
            'titulo'                                   => 'required|string|max:150',
            'periodo_inicio'                           => 'required|date',
            'periodo_fim'                              => 'nullable|date|after_or_equal:periodo_inicio',
            'status'                                   => 'required|in:ativo,finalizado,suspenso',
            'especialidades'                           => 'nullable|array',
            'especialidades.*.profissional_id'         => 'required|exists:profissionais,id',
            'especialidades.*.especialidade'           => 'required|string',
            'especialidades.*.objetivos_gerais'        => 'nullable|string',
            'especialidades.*.objetivos_especificos'   => 'nullable|string',
            'especialidades.*.estrategias'             => 'nullable|string',
        ]);

        $plano = PlanoTerapeutico::create([
            'paciente_id'    => $prontuario->paciente_id,
            'titulo'         => $dados['titulo'],
            'periodo_inicio' => $dados['periodo_inicio'],
            'periodo_fim'    => $dados['periodo_fim'] ?? null,
            'status'         => $dados['status'],
            'criado_por'     => auth()->id(),
        ]);

        foreach ($dados['especialidades'] ?? [] as $esp) {
            $plano->especialidades()->create([
                'profissional_id'        => $esp['profissional_id'],
                'especialidade'          => $esp['especialidade'],
                'objetivos_gerais'       => $esp['objetivos_gerais'] ?? null,
                'objetivos_especificos'  => $esp['objetivos_especificos'] ?? null,
                'estrategias'            => $esp['estrategias'] ?? null,
                'atualizado_por'         => auth()->id(),
            ]);
        }

        return redirect()->route('prontuarios.show', $prontuario->paciente_id)
            ->with('success', 'Plano terapêutico cadastrado com sucesso.');
    }

    public function edit(PlanoTerapeutico $plano)
    {
        $this->authorize('view', $plano->paciente);
        $this->autorizarEdicao($plano);

        $prontuario     = $plano->paciente->prontuario;
        $profissionais  = Profissional::with('user')->where('ativo', true)->orderBy('id')->get();
        $especialidades = Especialidade::cases();

        $plano->load('especialidades.profissional.user');

        return view('prontuarios.planos.edit', compact('plano', 'prontuario', 'profissionais', 'especialidades'));
    }

    public function update(Request $request, PlanoTerapeutico $plano)
    {
        $this->authorize('view', $plano->paciente);
        $this->autorizarEdicao($plano);

        $dados = $request->validate([
            'titulo'                                   => 'required|string|max:150',
            'periodo_inicio'                           => 'required|date',
            'periodo_fim'                              => 'nullable|date|after_or_equal:periodo_inicio',
            'status'                                   => 'required|in:ativo,finalizado,suspenso',
            'especialidades'                           => 'nullable|array',
            'especialidades.*.profissional_id'         => 'required|exists:profissionais,id',
            'especialidades.*.especialidade'           => 'required|string',
            'especialidades.*.objetivos_gerais'        => 'nullable|string',
            'especialidades.*.objetivos_especificos'   => 'nullable|string',
            'especialidades.*.estrategias'             => 'nullable|string',
        ]);

        $plano->update([
            'titulo'         => $dados['titulo'],
            'periodo_inicio' => $dados['periodo_inicio'],
            'periodo_fim'    => $dados['periodo_fim'] ?? null,
            'status'         => $dados['status'],
        ]);

        $plano->especialidades()->delete();

        foreach ($dados['especialidades'] ?? [] as $esp) {
            $plano->especialidades()->create([
                'profissional_id'        => $esp['profissional_id'],
                'especialidade'          => $esp['especialidade'],
                'objetivos_gerais'       => $esp['objetivos_gerais'] ?? null,
                'objetivos_especificos'  => $esp['objetivos_especificos'] ?? null,
                'estrategias'            => $esp['estrategias'] ?? null,
                'atualizado_por'         => auth()->id(),
            ]);
        }

        return redirect()->route('prontuarios.show', $plano->paciente_id)
            ->with('success', 'Plano terapêutico atualizado com sucesso.');
    }

    public function destroy(PlanoTerapeutico $plano)
    {
        $this->authorize('view', $plano->paciente);
        $this->autorizarEdicao($plano);

        $pacienteId = $plano->paciente_id;

        $plano->especialidades()->delete();
        $plano->delete();

        return redirect()->route('prontuarios.show', $pacienteId)
            ->with('success', 'Plano terapêutico removido.');
    }

    private function autorizarEdicao(PlanoTerapeutico $plano): void
    {
        $user = auth()->user();

        if ($user->perfil === PerfilUsuario::Admin) {
            return;
        }

        if ($plano->criado_por !== $user->id) {
            abort(403, 'Apenas o criador do plano ou um administrador pode editá-lo.');
        }
    }
}

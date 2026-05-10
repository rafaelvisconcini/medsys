<?php

namespace App\Http\Controllers\Prontuario;

use App\Http\Controllers\Controller;
use App\Models\Paciente;

class ProntuarioController extends Controller
{
    public function show(Paciente $paciente)
    {
        $this->authorize('view', $paciente);

        $prontuario = $paciente->prontuario()->firstOrFail();

        $user = auth()->user();

        // Profissional vê apenas suas próprias evoluções; admin vê tudo
        $evolucoes = $prontuario->evolucoes()
            ->with(['profissional.user', 'sessao'])
            ->when($user->isProfissional(), function ($q) use ($user) {
                $q->where('profissional_id', $user->profissional->id);
            })
            ->paginate(15, ['*'], 'evolucoes_page');

        $encaminhamentos = $prontuario->encaminhamentos()
            ->with('profissional.user')
            ->when($user->isProfissional(), function ($q) use ($user) {
                $q->where('profissional_id', $user->profissional->id);
            })
            ->get();

        $planos = \App\Models\PlanoTerapeutico::where('paciente_id', $paciente->id)
            ->with(['especialidades.profissional.user', 'criador'])
            ->orderByDesc('periodo_inicio')
            ->get();

        $anexos = $prontuario->anexos()
            ->with('uploader')
            ->paginate(10, ['*'], 'anexos_page');

        return view('prontuarios.show', compact(
            'paciente', 'prontuario', 'evolucoes',
            'encaminhamentos', 'planos', 'anexos'
        ));
    }
}

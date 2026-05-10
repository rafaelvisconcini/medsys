<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaConfiguracao;
use App\Models\Profissional;
use Illuminate\Http\Request;

class AgendaConfiguracaoController extends Controller
{
    public function index()
    {
        $profissionais = Profissional::with(['user', 'agendaConfiguracoes'])
            ->where('ativo', true)
            ->get();

        return view('admin.agenda-config.index', compact('profissionais'));
    }

    public function edit(Profissional $profissional)
    {
        $dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
        $configs = AgendaConfiguracao::where('profissional_id', $profissional->id)
            ->orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        return view('admin.agenda-config.edit', compact('profissional', 'dias', 'configs'));
    }

    public function update(Request $request, Profissional $profissional)
    {
        $request->validate([
            'dias'                       => 'nullable|array',
            'dias.*.ativo'               => 'nullable|boolean',
            'dias.*.hora_inicio'         => 'required_with:dias.*.ativo|date_format:H:i',
            'dias.*.hora_fim'            => 'required_with:dias.*.ativo|date_format:H:i|after:dias.*.hora_inicio',
            'dias.*.duracao_slot_min'    => 'required_with:dias.*.ativo|integer|min:15|max:120',
        ]);

        foreach (range(0, 6) as $diaSemana) {
            $dia = $request->input("dias.{$diaSemana}");
            $ativo = isset($dia['ativo']);

            AgendaConfiguracao::updateOrCreate(
                ['profissional_id' => $profissional->id, 'dia_semana' => $diaSemana],
                [
                    'ativo'            => $ativo,
                    'hora_inicio'      => $ativo ? $dia['hora_inicio'] : '08:00',
                    'hora_fim'         => $ativo ? $dia['hora_fim']    : '18:00',
                    'duracao_slot_min' => $ativo ? $dia['duracao_slot_min'] : ($profissional->duracao_sessao_min ?? 50),
                ]
            );
        }

        return redirect()->route('admin.agenda-config.index')
            ->with('success', "Agenda de {$profissional->user->name} atualizada.");
    }
}

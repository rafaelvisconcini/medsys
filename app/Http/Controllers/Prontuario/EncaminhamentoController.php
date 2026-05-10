<?php

namespace App\Http\Controllers\Prontuario;

use App\Http\Controllers\Controller;
use App\Models\Encaminhamento;
use App\Models\Prontuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EncaminhamentoController extends Controller
{
    public function create(Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);
        return view('prontuarios.encaminhamentos.create', compact('prontuario'));
    }

    public function store(Request $request, Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $profissional = auth()->user()->profissional;
        if (! $profissional) {
            abort(403, 'Apenas profissionais podem registrar encaminhamentos.');
        }

        $dados = $request->validate([
            'para_especialidade' => 'required|string|max:100',
            'motivo'             => 'required|string',
            'data'               => 'required|date',
            'observacoes'        => 'nullable|string|max:1000',
        ]);

        Encaminhamento::create([
            ...$dados,
            'prontuario_id'  => $prontuario->id,
            'profissional_id' => $profissional->id,
            'status'         => 'pendente',
        ]);

        return redirect()->route('prontuarios.show', $prontuario->paciente_id)
            ->with('success', 'Encaminhamento registrado.');
    }

    public function update(Request $request, Encaminhamento $encaminhamento)
    {
        $this->authorize('view', $encaminhamento->prontuario->paciente);

        $request->validate(['status' => 'required|in:pendente,realizado,cancelado']);
        $encaminhamento->update(['status' => $request->status]);

        return back()->with('success', 'Status atualizado.');
    }

    public function destroy(Encaminhamento $encaminhamento)
    {
        $pacienteId = $encaminhamento->prontuario->paciente_id;
        $encaminhamento->delete();

        return redirect()->route('prontuarios.show', $pacienteId)
            ->with('success', 'Encaminhamento removido.');
    }

    public function pdf(Encaminhamento $encaminhamento)
    {
        $this->authorize('view', $encaminhamento->prontuario->paciente);

        $encaminhamento->load(['profissional.user', 'prontuario.paciente']);

        $pdf = Pdf::loadView('prontuarios.encaminhamentos.pdf', compact('encaminhamento'))
            ->setPaper('a4', 'portrait');

        $nome = 'encaminhamento_' . $encaminhamento->prontuario->paciente->nome . '_' . $encaminhamento->data->format('Ymd') . '.pdf';

        return $pdf->download(str_replace(' ', '_', $nome));
    }
}

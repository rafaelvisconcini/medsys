<?php

namespace App\Http\Controllers;

use App\Enums\Especialidade;
use App\Models\ContaReceber;
use App\Models\Paciente;
use App\Models\Sessao;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $hoje = Carbon::today();

        $proximasSessoes = Sessao::with(['paciente', 'profissional.user'])
            ->whereDate('data_hora', $hoje)
            ->whereNotIn('status', ['cancelada'])
            ->orderBy('data_hora')
            ->get();

        $especialidades = collect(Especialidade::cases())->map(fn($e) => [
            'label' => $e->label(),
            'total' => Sessao::where('especialidade', $e->value)
                ->whereDate('data_hora', '>=', $hoje->copy()->startOfMonth())
                ->distinct('paciente_id')
                ->count('paciente_id'),
        ])->filter(fn($e) => $e['total'] > 0);

        return view('dashboard', [
            'totalPacientes'  => Paciente::where('ativo', true)->count(),
            'sessoesHoje'     => $proximasSessoes->count(),
            'sessoesSemana'   => Sessao::whereBetween('data_hora', [$hoje->copy()->startOfWeek(), $hoje->copy()->endOfWeek()])->count(),
            'contasAbertas'   => ContaReceber::whereIn('status', ['pendente', 'parcial'])->count(),
            'proximasSessoes' => $proximasSessoes,
            'especialidades'  => $especialidades,
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\AgendaBloqueio;
use App\Models\AgendaConfiguracao;
use App\Models\Profissional;
use App\Models\Sessao;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AgendaService
{
    /**
     * Retorna array de horários disponíveis para um profissional em uma data.
     * Formato: [['hora' => '09:00', 'datetime' => Carbon, 'disponivel' => bool], ...]
     */
    public function gerarSlots(Profissional $profissional, Carbon $data): Collection
    {
        $diaSemana = $data->dayOfWeek; // 0=Dom...6=Sab

        $config = AgendaConfiguracao::where('profissional_id', $profissional->id)
            ->where('dia_semana', $diaSemana)
            ->where('ativo', true)
            ->first();

        if (! $config) {
            return collect();
        }

        // Verifica bloqueios no dia
        $bloqueado = AgendaBloqueio::where('profissional_id', $profissional->id)
            ->where('data_inicio', '<=', $data->copy()->endOfDay())
            ->where('data_fim', '>=', $data->copy()->startOfDay())
            ->exists();

        if ($bloqueado) {
            return collect();
        }

        // Gera os slots
        $slots = collect();
        $inicio = Carbon::parse($data->format('Y-m-d') . ' ' . $config->hora_inicio);
        $fim    = Carbon::parse($data->format('Y-m-d') . ' ' . $config->hora_fim);
        $duracao = $config->duracao_slot_min;

        // Sessões já agendadas no dia para este profissional
        $sessoesOcupadas = Sessao::where('profissional_id', $profissional->id)
            ->whereDate('data_hora', $data)
            ->whereNotIn('status', ['cancelada'])
            ->get(['data_hora', 'duracao_min']);

        $cursor = $inicio->copy();
        while ($cursor->copy()->addMinutes($duracao)->lte($fim)) {
            $slotFim = $cursor->copy()->addMinutes($duracao);

            $ocupado = $sessoesOcupadas->contains(function ($s) use ($cursor, $slotFim) {
                $sSt = Carbon::parse($s->data_hora);
                $sFm = $sSt->copy()->addMinutes($s->duracao_min);
                return $cursor->lt($sFm) && $slotFim->gt($sSt);
            });

            $slots->push([
                'hora'       => $cursor->format('H:i'),
                'datetime'   => $cursor->copy(),
                'disponivel' => ! $ocupado,
            ]);

            $cursor->addMinutes($duracao);
        }

        return $slots;
    }

    /**
     * Verifica se há conflito de horário para um profissional.
     * Retorna true se o horário está LIVRE, false se há conflito.
     */
    public function horarioLivre(
        int $profissional_id,
        Carbon $dataHora,
        int $duracao_min,
        ?int $excluir_sessao_id = null
    ): bool {
        $fim = $dataHora->copy()->addMinutes($duracao_min);

        $sessoes = Sessao::where('profissional_id', $profissional_id)
            ->whereNotIn('status', ['cancelada'])
            ->when($excluir_sessao_id, fn($q) => $q->where('id', '!=', $excluir_sessao_id))
            ->whereDate('data_hora', $dataHora->toDateString())
            ->get(['data_hora', 'duracao_min']);

        $conflito = $sessoes->contains(function ($s) use ($dataHora, $fim) {
            $sSt = Carbon::parse($s->data_hora);
            $sFm = $sSt->copy()->addMinutes($s->duracao_min);
            return $dataHora->lt($sFm) && $fim->gt($sSt);
        });

        return ! $conflito;
    }

    /**
     * Retorna eventos no formato FullCalendar para um período.
     */
    public function eventosParaCalendario(
        Carbon $inicio,
        Carbon $fim,
        ?int $profissional_id = null
    ): array {
        $cores = [
            'agendada'   => '#3b82f6',
            'confirmada' => '#10b981',
            'realizada'  => '#6b7280',
            'cancelada'  => '#ef4444',
            'faltou'     => '#f59e0b',
            'reposicao'  => '#8b5cf6',
        ];

        return Sessao::with(['paciente', 'profissional.user'])
            ->whereBetween('data_hora', [$inicio, $fim])
            ->when($profissional_id, fn($q) => $q->where('profissional_id', $profissional_id))
            ->get()
            ->map(function (Sessao $s) use ($cores) {
                return [
                    'id'    => $s->id,
                    'title' => $s->paciente->nome . "\n" . $s->especialidade->label(),
                    'start' => $s->data_hora->toIso8601String(),
                    'end'   => $s->data_hora->copy()->addMinutes($s->duracao_min)->toIso8601String(),
                    'color' => $cores[$s->status] ?? '#3b82f6',
                    'extendedProps' => [
                        'sessao_id'    => $s->id,
                        'paciente'     => $s->paciente->nome,
                        'profissional' => $s->profissional->user->name,
                        'especialidade'=> $s->especialidade->label(),
                        'status'       => $s->status,
                        'edit_url'     => route('sessoes.edit', $s->id),
                    ],
                ];
            })
            ->values()
            ->toArray();
    }
}

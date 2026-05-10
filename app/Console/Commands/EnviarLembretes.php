<?php

namespace App\Console\Commands;

use App\Mail\SessaoLembreteMail;
use App\Models\Sessao;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarLembretes extends Command
{
    protected $signature   = 'sessoes:lembretes';
    protected $description = 'Envia e-mails de lembrete para sessões do dia seguinte';

    public function handle(): int
    {
        $amanha = Carbon::tomorrow();

        $sessoes = Sessao::with(['paciente', 'profissional.user'])
            ->whereDate('data_hora', $amanha)
            ->whereIn('status', ['agendada', 'confirmada'])
            ->get();

        $enviados = 0;

        foreach ($sessoes as $sessao) {
            $email = $sessao->paciente->responsavel_email ?? null;
            if (! $email) {
                continue;
            }

            Mail::to($email)->queue(new SessaoLembreteMail($sessao));
            $enviados++;
        }

        $this->info("Lembretes enfileirados: {$enviados} de {$sessoes->count()} sessões.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Observers;

use App\Mail\SessaoConfirmadaMail;
use App\Models\Sessao;
use Illuminate\Support\Facades\Mail;

class SessaoObserver
{
    public function created(Sessao $sessao): void
    {
        $this->enviarConfirmacao($sessao);
    }

    public function updated(Sessao $sessao): void
    {
        // Dispara e-mail quando status muda para 'confirmada'
        if ($sessao->wasChanged('status') && $sessao->status === 'confirmada') {
            $this->enviarConfirmacao($sessao);
        }
    }

    private function enviarConfirmacao(Sessao $sessao): void
    {
        $sessao->loadMissing(['paciente', 'profissional.user']);

        $email = $sessao->paciente->responsavel_email ?? null;
        if (! $email) {
            return;
        }

        Mail::to($email)
            ->queue(new SessaoConfirmadaMail($sessao));
    }
}

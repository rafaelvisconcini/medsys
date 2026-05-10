<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Envia lembretes de sessão do dia seguinte todo dia às 18h
Schedule::command('sessoes:lembretes')->dailyAt('18:00');

// Gera mensalidades dos contratos ativos no 1º dia de cada mês às 6h
Schedule::command('financeiro:gerar-mensalidades')->monthlyOn(1, '06:00');

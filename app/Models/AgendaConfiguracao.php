<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaConfiguracao extends Model
{
    protected $table = 'agenda_configuracoes';

    protected $fillable = [
        'profissional_id', 'dia_semana', 'hora_inicio', 'hora_fim',
        'duracao_slot_min', 'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    public function diaSemanaLabel(): string
    {
        return ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'][$this->dia_semana] ?? '';
    }
}

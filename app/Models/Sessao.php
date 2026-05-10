<?php

namespace App\Models;

use App\Enums\Especialidade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sessao extends Model
{
    use SoftDeletes;

    protected $table = 'sessoes';

    protected $fillable = [
        'paciente_id', 'profissional_id', 'especialidade', 'data_hora',
        'duracao_min', 'tipo', 'status', 'motivo_cancelamento',
        'gera_cobranca', 'contrato_id', 'agendado_por', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_hora'      => 'datetime',
            'especialidade'  => Especialidade::class,
            'gera_cobranca'  => 'boolean',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function evolucoes()
    {
        return $this->hasMany(Evolucao::class);
    }
}

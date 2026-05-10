<?php

namespace App\Models;

use App\Enums\Especialidade;
use Illuminate\Database\Eloquent\Model;

class Evolucao extends Model
{
    protected $table = 'evolucoes';

    protected $fillable = [
        'prontuario_id', 'sessao_id', 'profissional_id', 'especialidade',
        'data_hora', 'descricao', 'objetivos_trabalhados',
        'resposta_paciente', 'proximos_objetivos', 'cids',
    ];

    protected function casts(): array
    {
        return [
            'especialidade' => Especialidade::class,
            'data_hora'     => 'datetime',
            'cids'          => 'array',
        ];
    }

    public function prontuario()
    {
        return $this->belongsTo(Prontuario::class);
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    public function sessao()
    {
        return $this->belongsTo(Sessao::class);
    }
}

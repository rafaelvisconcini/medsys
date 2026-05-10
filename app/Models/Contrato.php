<?php

namespace App\Models;

use App\Enums\Especialidade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrato extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id', 'profissional_id', 'especialidade', 'valor_mensal',
        'dia_vencimento', 'sessoes_por_semana', 'data_inicio', 'data_fim',
        'status', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'especialidade' => Especialidade::class,
            'data_inicio'   => 'date',
            'data_fim'      => 'date',
            'valor_mensal'  => 'decimal:2',
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

    public function contasReceber()
    {
        return $this->hasMany(ContaReceber::class);
    }
}

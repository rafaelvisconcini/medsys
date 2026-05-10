<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoTerapeutico extends Model
{
    protected $table = 'planos_terapeuticos';

    protected $fillable = [
        'paciente_id', 'titulo', 'periodo_inicio', 'periodo_fim', 'status', 'criado_por',
    ];

    protected function casts(): array
    {
        return [
            'periodo_inicio' => 'date',
            'periodo_fim'    => 'date',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function especialidades()
    {
        return $this->hasMany(PlanoTerapeuticoEspecialidade::class);
    }
}

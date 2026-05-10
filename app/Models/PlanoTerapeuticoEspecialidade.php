<?php

namespace App\Models;

use App\Enums\Especialidade;
use Illuminate\Database\Eloquent\Model;

class PlanoTerapeuticoEspecialidade extends Model
{
    protected $table = 'plano_terapeutico_especialidades';

    protected $fillable = [
        'plano_terapeutico_id', 'profissional_id', 'especialidade',
        'objetivos_gerais', 'objetivos_especificos', 'estrategias', 'atualizado_por',
    ];

    protected function casts(): array
    {
        return ['especialidade' => Especialidade::class];
    }

    public function plano()
    {
        return $this->belongsTo(PlanoTerapeutico::class, 'plano_terapeutico_id');
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }
}

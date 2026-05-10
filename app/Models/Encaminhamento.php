<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encaminhamento extends Model
{
    protected $fillable = [
        'prontuario_id', 'profissional_id', 'para_especialidade',
        'motivo', 'data', 'status', 'observacoes',
    ];

    protected function casts(): array
    {
        return ['data' => 'date'];
    }

    public function prontuario()
    {
        return $this->belongsTo(Prontuario::class);
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }
}

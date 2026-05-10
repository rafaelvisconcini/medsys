<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaBloqueio extends Model
{
    protected $table = 'agenda_bloqueios';

    protected $fillable = ['profissional_id', 'data_inicio', 'data_fim', 'motivo'];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim'    => 'datetime',
        ];
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }
}

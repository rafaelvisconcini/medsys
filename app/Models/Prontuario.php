<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prontuario extends Model
{
    protected $fillable = ['paciente_id', 'numero'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function evolucoes()
    {
        return $this->hasMany(Evolucao::class)->orderByDesc('data_hora');
    }

    public function encaminhamentos()
    {
        return $this->hasMany(Encaminhamento::class)->orderByDesc('data');
    }

    public function anexos()
    {
        return $this->hasMany(ProntuarioAnexo::class)->orderByDesc('created_at');
    }
}

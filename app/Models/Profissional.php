<?php

namespace App\Models;

use App\Enums\Especialidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    use HasFactory;

    protected $table = 'profissionais';

    protected $fillable = ['user_id', 'especialidade', 'registro_profissional', 'duracao_sessao_min', 'ativo'];

    protected function casts(): array
    {
        return [
            'especialidade' => Especialidade::class,
            'ativo'         => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sessoes()
    {
        return $this->hasMany(Sessao::class);
    }

    public function agendaConfiguracoes()
    {
        return $this->hasMany(AgendaConfiguracao::class);
    }
}

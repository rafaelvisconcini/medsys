<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Paciente extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome', 'ativo', 'diagnosticos'])
            ->logOnlyDirty();
    }

    protected $fillable = [
        'nome', 'data_nascimento', 'sexo', 'escola', 'serie_escolar',
        'diagnosticos', 'foto_path', 'observacoes',
        'responsavel_nome', 'responsavel_cpf', 'responsavel_parentesco',
        'responsavel_telefone', 'responsavel_celular', 'responsavel_email',
        'contato2_nome', 'contato2_telefone', 'contato2_parentesco',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'diagnosticos'    => 'array',
            'ativo'           => 'boolean',
        ];
    }

    public function getIdadeAttribute(): string
    {
        return $this->data_nascimento->age . ' anos';
    }

    public function sessoes()
    {
        return $this->hasMany(Sessao::class);
    }

    public function prontuario()
    {
        return $this->hasOne(Prontuario::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
}

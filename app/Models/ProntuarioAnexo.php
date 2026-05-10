<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProntuarioAnexo extends Model
{
    protected $table = 'prontuario_anexos';

    protected $fillable = [
        'prontuario_id', 'evolucao_id', 'tipo', 'nome_original',
        'path', 'mime_type', 'tamanho_bytes', 'descricao',
        'data_documento', 'uploaded_por',
    ];

    protected function casts(): array
    {
        return ['data_documento' => 'date'];
    }

    public function prontuario()
    {
        return $this->belongsTo(Prontuario::class);
    }

    public function evolucao()
    {
        return $this->belongsTo(Evolucao::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_por');
    }
}

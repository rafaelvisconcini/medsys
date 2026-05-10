<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    protected $fillable = [
        'conta_receber_id', 'numero_parcela', 'valor', 'data_vencimento',
        'data_pagamento', 'forma_pagamento', 'status', 'observacoes', 'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'data_vencimento' => 'date',
            'data_pagamento'  => 'date',
            'valor'           => 'decimal:2',
        ];
    }

    public function conta()
    {
        return $this->belongsTo(ContaReceber::class, 'conta_receber_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function estaVencida(): bool
    {
        return $this->status === 'pendente' && $this->data_vencimento->isPast();
    }

    public static function formasPagamento(): array
    {
        return [
            'dinheiro'        => 'Dinheiro',
            'pix'             => 'PIX',
            'cartao_debito'   => 'Cartão Débito',
            'cartao_credito'  => 'Cartão Crédito',
            'transferencia'   => 'Transferência',
            'outro'           => 'Outro',
        ];
    }
}

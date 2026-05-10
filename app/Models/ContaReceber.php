<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaReceber extends Model
{
    use SoftDeletes;

    protected $table = 'contas_receber';

    protected $fillable = [
        'paciente_id', 'sessao_id', 'contrato_id', 'descricao',
        'valor_total', 'tipo', 'status', 'data_vencimento',
        'data_liquidacao', 'referencia_mes', 'observacoes', 'criado_por',
    ];

    protected function casts(): array
    {
        return [
            'data_vencimento'  => 'date',
            'data_liquidacao'  => 'date',
            'referencia_mes'   => 'date',
            'valor_total'      => 'decimal:2',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function sessao()
    {
        return $this->belongsTo(Sessao::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'conta_receber_id')->orderBy('numero_parcela');
    }

    public function recalcularStatus(): void
    {
        $parcelas = $this->parcelas()->get();

        if ($parcelas->isEmpty()) {
            return;
        }

        $pagas = $parcelas->where('status', 'pago')->count();
        $total = $parcelas->count();

        $status = match(true) {
            $pagas === $total  => 'quitado',
            $pagas > 0         => 'parcial',
            default            => 'pendente',
        };

        if ($status === 'quitado') {
            $this->update([
                'status'           => 'quitado',
                'data_liquidacao'  => $parcelas->where('status', 'pago')->max('data_pagamento'),
            ]);
        } else {
            $this->update(['status' => $status]);
        }
    }
}

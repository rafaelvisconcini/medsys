@extends('layouts.app')
@section('title', 'Cobrança')
@section('page-title', 'Cobrança')
@section('header-actions')
    <a href="{{ route('financeiro.contas.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
    @if($conta->status !== 'quitado' && $conta->status !== 'cancelado')
    <a href="{{ route('financeiro.contas.edit', $conta) }}" class="btn btn-outline-primary btn-sm">Editar</a>
    @endif
@endsection

@section('content')
@php $badgeMap = ['pendente'=>'warning text-dark','parcial'=>'info','quitado'=>'success','cancelado'=>'secondary']; @endphp
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold text-secondary mb-3">Dados da Cobrança</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5">Paciente</dt>
                    <dd class="col-7">{{ $conta->paciente->nome }}</dd>
                    <dt class="col-5">Descrição</dt>
                    <dd class="col-7">{{ $conta->descricao }}</dd>
                    <dt class="col-5">Tipo</dt>
                    <dd class="col-7">{{ ucfirst($conta->tipo) }}</dd>
                    <dt class="col-5">Valor total</dt>
                    <dd class="col-7 fw-bold">R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</dd>
                    <dt class="col-5">Vencimento</dt>
                    <dd class="col-7">{{ $conta->data_vencimento->format('d/m/Y') }}</dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7"><span class="badge bg-{{ $badgeMap[$conta->status] ?? 'secondary' }}">{{ ucfirst($conta->status) }}</span></dd>
                    @if($conta->data_liquidacao)
                    <dt class="col-5">Liquidado em</dt>
                    <dd class="col-7">{{ $conta->data_liquidacao->format('d/m/Y') }}</dd>
                    @endif
                </dl>
                @if($conta->observacoes)
                <hr><p class="small text-muted mb-0">{{ $conta->observacoes }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Parcelas</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Parcela</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th>Forma</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conta->parcelas as $p)
                        <tr class="{{ $p->estaVencida() ? 'table-danger' : '' }}">
                            <td>{{ $p->numero_parcela }}/{{ $conta->parcelas->count() }}</td>
                            <td class="small">{{ $p->data_vencimento->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                            <td>
                                @if($p->status === 'pago')
                                    <span class="badge bg-success">Pago</span>
                                @elseif($p->estaVencida())
                                    <span class="badge bg-danger">Vencida</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendente</span>
                                @endif
                            </td>
                            <td class="small">{{ $p->data_pagamento?->format('d/m/Y') ?? '—' }}</td>
                            <td class="small">{{ \App\Models\Parcela::formasPagamento()[$p->forma_pagamento] ?? '—' }}</td>
                            <td>
                                @if($p->status === 'pendente' && $conta->status !== 'cancelado')
                                <a href="{{ route('financeiro.parcelas.pagar', [$conta, $p]) }}"
                                   class="btn btn-sm btn-success">Registrar</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

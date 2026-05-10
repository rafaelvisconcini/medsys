@extends('layouts.app')
@section('title', 'Contrato')
@section('page-title', 'Contrato')
@section('header-actions')
    <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Contratos</a>
    <a href="{{ route('financeiro.contratos.edit', $contrato) }}" class="btn btn-outline-primary btn-sm">Editar</a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold text-secondary mb-3">Dados do Contrato</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5">Paciente</dt>
                    <dd class="col-7">{{ $contrato->paciente->nome }}</dd>
                    <dt class="col-5">Profissional</dt>
                    <dd class="col-7">{{ $contrato->profissional->user->name }}</dd>
                    <dt class="col-5">Especialidade</dt>
                    <dd class="col-7">{{ $contrato->especialidade->label() }}</dd>
                    <dt class="col-5">Valor mensal</dt>
                    <dd class="col-7 fw-bold">R$ {{ number_format($contrato->valor_mensal, 2, ',', '.') }}</dd>
                    <dt class="col-5">Vencimento</dt>
                    <dd class="col-7">Dia {{ $contrato->dia_vencimento }}</dd>
                    <dt class="col-5">Sessões/sem.</dt>
                    <dd class="col-7">{{ $contrato->sessoes_por_semana }}x</dd>
                    <dt class="col-5">Início</dt>
                    <dd class="col-7">{{ $contrato->data_inicio->format('d/m/Y') }}</dd>
                    @if($contrato->data_fim)
                    <dt class="col-5">Encerramento</dt>
                    <dd class="col-7">{{ $contrato->data_fim->format('d/m/Y') }}</dd>
                    @endif
                    <dt class="col-5">Status</dt>
                    <dd class="col-7">
                        <span class="badge {{ $contrato->status === 'ativo' ? 'bg-success' : ($contrato->status === 'suspenso' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($contrato->status) }}
                        </span>
                    </dd>
                </dl>

                @if($contrato->observacoes)
                <hr><p class="small text-muted mb-0">{{ $contrato->observacoes }}</p>
                @endif
            </div>
        </div>

        {{-- Gerar cobrança manual --}}
        @if($contrato->status === 'ativo')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="fw-semibold text-secondary mb-2">Gerar Cobrança</h6>
                <form action="{{ route('financeiro.contratos.gerar-cobranca', $contrato) }}" method="POST">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input type="month" name="referencia_mes"
                               value="{{ today()->format('Y-m') }}"
                               class="form-control @error('referencia_mes') is-invalid @enderror">
                        <button type="submit" class="btn btn-outline-primary">Gerar</button>
                    </div>
                    @error('referencia_mes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Cobranças deste Contrato</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrato->contasReceber->sortByDesc('data_vencimento') as $c)
                        <tr>
                            <td class="small">{{ $c->descricao }}</td>
                            <td class="small">{{ $c->data_vencimento->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($c->valor_total, 2, ',', '.') }}</td>
                            <td>
                                @php $badgeMap = ['pendente'=>'warning text-dark','parcial'=>'info','quitado'=>'success','cancelado'=>'secondary']; @endphp
                                <span class="badge bg-{{ $badgeMap[$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span>
                            </td>
                            <td><a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Nenhuma cobrança gerada ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

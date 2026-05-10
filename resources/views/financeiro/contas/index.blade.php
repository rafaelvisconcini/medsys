@extends('layouts.app')
@section('title', 'Contas a Receber')
@section('page-title', 'Contas a Receber')
@section('header-actions')
    <a href="{{ route('financeiro.contas.create') }}" class="btn btn-primary btn-sm">+ Nova Cobrança</a>
@endsection

@section('content')
<form method="GET" class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar paciente..."
           class="form-control form-control-sm" style="max-width:200px;">
    <select name="status" class="form-select form-select-sm" style="max-width:150px;">
        <option value="">Todos</option>
        <option value="pendente"  @selected(request('status') === 'pendente')>Pendente</option>
        <option value="parcial"   @selected(request('status') === 'parcial')>Parcial</option>
        <option value="quitado"   @selected(request('status') === 'quitado')>Quitado</option>
        <option value="cancelado" @selected(request('status') === 'cancelado')>Cancelado</option>
    </select>
    <select name="tipo" class="form-select form-select-sm" style="max-width:150px;">
        <option value="">Todos os tipos</option>
        <option value="mensalidade" @selected(request('tipo') === 'mensalidade')>Mensalidade</option>
        <option value="avulso"      @selected(request('tipo') === 'avulso')>Avulso</option>
    </select>
    <input type="date" name="vencimento_de" value="{{ request('vencimento_de') }}"
           class="form-control form-control-sm" style="max-width:150px;" title="Vencimento de">
    <input type="date" name="vencimento_ate" value="{{ request('vencimento_ate') }}"
           class="form-control form-control-sm" style="max-width:150px;" title="Vencimento até">
    <button type="submit" class="btn btn-outline-secondary btn-sm">Filtrar</button>
    @if(request()->hasAny(['search','status','tipo','vencimento_de','vencimento_ate']))
    <a href="{{ route('financeiro.contas.index') }}" class="btn btn-link btn-sm text-muted">Limpar</a>
    @endif
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @php $badgeMap = ['pendente'=>'warning text-dark','parcial'=>'info','quitado'=>'success','cancelado'=>'secondary']; @endphp
                @forelse ($contas as $c)
                <tr class="{{ $c->status === 'pendente' && $c->data_vencimento->isPast() ? 'table-danger' : '' }}">
                    <td class="fw-semibold small">{{ $c->paciente->nome }}</td>
                    <td class="small text-muted">{{ Str::limit($c->descricao, 40) }}</td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($c->tipo) }}</span></td>
                    <td class="small">{{ $c->data_vencimento->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($c->valor_total, 2, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $badgeMap[$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhuma cobrança encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contas->hasPages())
    <div class="card-footer bg-white">{{ $contas->links() }}</div>
    @endif
</div>
@endsection

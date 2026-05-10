@extends('layouts.app')
@section('title', 'Contratos')
@section('page-title', 'Contratos')
@section('header-actions')
    <a href="{{ route('financeiro.contratos.create') }}" class="btn btn-primary btn-sm">+ Novo Contrato</a>
@endsection

@section('content')
{{-- Filtros --}}
<form method="GET" class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar paciente..."
           class="form-control form-control-sm" style="max-width:220px;">
    <select name="status" class="form-select form-select-sm" style="max-width:160px;">
        <option value="">Todos os status</option>
        <option value="ativo"      @selected(request('status') === 'ativo')>Ativo</option>
        <option value="suspenso"   @selected(request('status') === 'suspenso')>Suspenso</option>
        <option value="encerrado"  @selected(request('status') === 'encerrado')>Encerrado</option>
    </select>
    <button type="submit" class="btn btn-outline-secondary btn-sm">Filtrar</button>
    @if(request()->hasAny(['search','status']))
    <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-link btn-sm text-muted">Limpar</a>
    @endif
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Profissional</th>
                    <th>Especialidade</th>
                    <th>Valor/mês</th>
                    <th>Início</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contratos as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->paciente->nome }}</td>
                    <td class="small">{{ $c->profissional->user->name }}</td>
                    <td class="small">{{ $c->especialidade->label() }}</td>
                    <td>R$ {{ number_format($c->valor_mensal, 2, ',', '.') }}</td>
                    <td class="small text-muted">{{ $c->data_inicio->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $c->status === 'ativo' ? 'bg-success' : ($c->status === 'suspenso' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($c->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('financeiro.contratos.show', $c) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('financeiro.contratos.edit', $c) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum contrato encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contratos->hasPages())
    <div class="card-footer bg-white">{{ $contratos->links() }}</div>
    @endif
</div>
@endsection

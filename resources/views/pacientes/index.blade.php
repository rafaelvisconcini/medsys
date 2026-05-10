@extends('layouts.app')

@section('title', 'Pacientes')
@section('page-title', 'Pacientes')

@section('header-actions')
    @can('create', \App\Models\Paciente::class)
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary btn-sm">
        + Novo Paciente
    </a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="busca" value="{{ request('busca') }}"
                   class="form-control form-control-sm" style="max-width:280px;"
                   placeholder="Buscar por nome ou responsável...">

            <select name="status" class="form-select form-select-sm" style="max-width:150px;">
                <option value="">Todos</option>
                <option value="1" @selected(request('status') === '1')>Ativos</option>
                <option value="0" @selected(request('status') === '0')>Inativos</option>
            </select>

            <button type="submit" class="btn btn-outline-secondary btn-sm">Filtrar</button>
            @if(request()->hasAny(['busca', 'status']))
                <a href="{{ route('pacientes.index') }}" class="btn btn-link btn-sm text-muted">Limpar</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        @if($pacientes->isEmpty())
            <p class="text-muted text-center py-5 mb-0">Nenhum paciente encontrado.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px;"></th>
                        <th>Paciente</th>
                        <th>Idade</th>
                        <th>Responsável</th>
                        <th>Celular</th>
                        <th>Diagnósticos</th>
                        <th>Status</th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $paciente)
                    <tr>
                        <td>
                            @if($paciente->foto_path)
                                <img src="{{ asset('storage/' . $paciente->foto_path) }}"
                                     class="rounded-circle" width="36" height="36"
                                     style="object-fit:cover;" alt="">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;font-weight:600;color:#0d6efd;font-size:.85rem;">
                                    {{ strtoupper(substr($paciente->nome, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pacientes.show', $paciente) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $paciente->nome }}
                            </a>
                        </td>
                        <td class="text-muted small">{{ $paciente->data_nascimento->age }} anos</td>
                        <td class="small">{{ $paciente->responsavel_nome }}</td>
                        <td class="small text-muted">{{ $paciente->responsavel_celular }}</td>
                        <td>
                            @foreach($paciente->diagnosticos ?? [] as $cid)
                                <span class="badge bg-info text-dark">{{ $cid }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $paciente->ativo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $paciente->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            @can('update', $paciente)
                            <a href="{{ route('pacientes.edit', $paciente) }}"
                               class="btn btn-link btn-sm text-muted p-0">Editar</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @if($pacientes->hasPages())
    <div class="card-footer bg-white border-top-0">
        {{ $pacientes->links() }}
    </div>
    @endif
</div>
@endsection

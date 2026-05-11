@extends('layouts.app')
@section('title', 'Pacientes')
@section('page-title', 'Pacientes')

@section('header-actions')
    @can('create', \App\Models\Paciente::class)
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Novo Paciente
    </a>
    @endcan
@endsection

@section('content')
<div class="card">
    {{-- Filtros --}}
    <div class="card-header" style="background:#f9fafb;">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <div class="d-flex align-items-center gap-2" style="flex:1;min-width:220px;max-width:320px;">
                <i class="bi bi-search" style="color:#9ca3af;font-size:.875rem;"></i>
                <input type="text" name="busca" value="{{ request('busca') }}"
                       class="form-control form-control-sm" style="border-left:none;padding-left:.25rem;"
                       placeholder="Buscar por nome ou responsável...">
            </div>
            <select name="status" class="form-select form-select-sm" style="max-width:140px;">
                <option value="">Todos os status</option>
                <option value="1" @selected(request('status') === '1')>Ativos</option>
                <option value="0" @selected(request('status') === '0')>Inativos</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrar</button>
            @if(request()->hasAny(['busca', 'status']))
                <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-link" style="color:#6b7280;">Limpar</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        @if($pacientes->isEmpty())
            <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
                <i class="bi bi-people" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-3 mb-0" style="font-size:.875rem;">Nenhum paciente encontrado.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
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
                                     class="rounded-circle" width="34" height="34"
                                     style="object-fit:cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:34px;height:34px;background:#eff6ff;color:#2563eb;font-weight:700;font-size:.75rem;">
                                    {{ strtoupper(substr($paciente->nome, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pacientes.show', $paciente) }}"
                               style="font-weight:600;color:#111827;text-decoration:none;">
                                {{ $paciente->nome }}
                            </a>
                        </td>
                        <td style="color:#6b7280;">{{ $paciente->data_nascimento->age }} anos</td>
                        <td style="color:#374151;">{{ $paciente->responsavel_nome }}</td>
                        <td style="color:#6b7280;">{{ $paciente->responsavel_celular }}</td>
                        <td>
                            @foreach($paciente->diagnosticos ?? [] as $cid)
                                <span class="badge bg-info me-1">{{ $cid }}</span>
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
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
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
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span style="font-size:.8125rem;color:#6b7280;">
            {{ $pacientes->total() }} paciente{{ $pacientes->total() !== 1 ? 's' : '' }} encontrado{{ $pacientes->total() !== 1 ? 's' : '' }}
        </span>
        {{ $pacientes->links() }}
    </div>
    @endif
</div>
@endsection

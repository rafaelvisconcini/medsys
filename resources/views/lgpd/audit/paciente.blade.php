@extends('layouts.app')
@section('title', 'Auditoria — ' . $paciente->nome)
@section('page-title', 'Auditoria — ' . $paciente->nome)
@section('header-actions')
    <a href="{{ route('lgpd.audit.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Trilha Geral</a>
    <a href="{{ route('lgpd.export.paciente', $paciente) }}" class="btn btn-outline-primary btn-sm">
        Exportar Dados (LGPD)
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Evento</th>
                    <th>Campos alterados</th>
                    <th>Valores anteriores</th>
                    <th>Novos valores</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td class="text-nowrap text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->causer?->name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $log->event === 'created' ? 'bg-success' : ($log->event === 'deleted' ? 'bg-danger' : 'bg-primary') }}">
                            {{ ucfirst($log->event) }}
                        </span>
                    </td>
                    <td>
                        @foreach(array_keys($log->properties['attributes'] ?? []) as $campo)
                            <div>{{ $campo }}</div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($log->properties['old'] ?? [] as $campo => $valor)
                            <div class="text-danger">{{ is_array($valor) ? json_encode($valor) : $valor }}</div>
                        @endforeach
                    </td>
                    <td>
                        @foreach($log->properties['attributes'] ?? [] as $campo => $valor)
                            <div class="text-success">{{ is_array($valor) ? json_encode($valor) : $valor }}</div>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum registro de alteração para este paciente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection

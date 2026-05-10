@extends('layouts.app')
@section('title', 'Trilha de Auditoria')
@section('page-title', 'Trilha de Auditoria — LGPD')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Evento</th>
                    <th>Objeto</th>
                    <th>Alterações</th>
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
                    <td>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                    <td>
                        @if($log->properties->has('attributes'))
                            @foreach($log->properties['attributes'] as $campo => $valor)
                                <span class="badge bg-light text-dark border me-1">
                                    {{ $campo }}: {{ is_array($valor) ? json_encode($valor) : $valor }}
                                </span>
                            @endforeach
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum registro de auditoria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection

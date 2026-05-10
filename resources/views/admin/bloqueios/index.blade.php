@extends('layouts.app')

@section('title', 'Bloqueios de Agenda')
@section('page-title', 'Bloqueios de Agenda')

@section('header-actions')
    <a href="{{ route('admin.agenda-config.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Configuração de Agenda</a>
@endsection

@section('content')
<div class="row g-4">
    {{-- Formulário de novo bloqueio --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Novo Bloqueio</h6>

                <form action="{{ route('admin.bloqueios.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Profissional</label>
                        <select name="profissional_id" class="form-select form-select-sm" required>
                            <option value="">Selecione...</option>
                            @foreach ($profissionais as $p)
                                <option value="{{ $p->id }}" @selected(old('profissional_id') == $p->id)>{{ $p->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Data Início</label>
                        <input type="date" name="data_inicio" value="{{ old('data_inicio') }}"
                               class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Data Fim</label>
                        <input type="date" name="data_fim" value="{{ old('data_fim') }}"
                               class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Motivo (opcional)</label>
                        <input type="text" name="motivo" value="{{ old('motivo') }}"
                               class="form-control form-control-sm" maxlength="255">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Registrar Bloqueio</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Lista de bloqueios --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Profissional</th>
                            <th>De</th>
                            <th>Até</th>
                            <th>Motivo</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bloqueios as $b)
                        <tr>
                            <td>{{ $b->profissional->user->name }}</td>
                            <td>{{ $b->data_inicio->format('d/m/Y') }}</td>
                            <td>{{ $b->data_fim->format('d/m/Y') }}</td>
                            <td class="text-secondary small">{{ $b->motivo ?? '—' }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.bloqueios.destroy', $b) }}" method="POST"
                                      onsubmit="return confirm('Remover bloqueio?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Nenhum bloqueio registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($bloqueios->hasPages())
            <div class="card-footer bg-white">{{ $bloqueios->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

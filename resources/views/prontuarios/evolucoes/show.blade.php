@extends('layouts.app')
@section('title', 'Evolução')
@section('page-title', 'Evolução — ' . $evolucao->prontuario->paciente->nome)
@section('header-actions')
    <a href="{{ route('prontuarios.show', $evolucao->prontuario->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
    @can('update', $evolucao)
    <a href="{{ route('evolucoes.edit', $evolucao) }}" class="btn btn-outline-primary btn-sm">Editar</a>
    @endcan
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="badge me-2" style="background:#1d4ed8;">{{ $evolucao->especialidade->label() }}</span>
                    <span class="fw-semibold">{{ $evolucao->profissional->user->name }}</span>
                </div>
                <span class="text-muted small">{{ $evolucao->data_hora->format('d/m/Y \à\s H:i') }}</span>
            </div>
            <div class="card-body p-4">
                <h6 class="fw-semibold text-secondary mb-2">Descrição da sessão</h6>
                <p>{{ $evolucao->descricao }}</p>

                @if($evolucao->objetivos_trabalhados)
                <h6 class="fw-semibold text-secondary mb-2 mt-4">Objetivos trabalhados</h6>
                <p>{{ $evolucao->objetivos_trabalhados }}</p>
                @endif

                @if($evolucao->resposta_paciente)
                <h6 class="fw-semibold text-secondary mb-2 mt-4">Resposta do paciente</h6>
                <p>{{ $evolucao->resposta_paciente }}</p>
                @endif

                @if($evolucao->proximos_objetivos)
                <h6 class="fw-semibold text-secondary mb-2 mt-4">Próximos objetivos</h6>
                <p>{{ $evolucao->proximos_objetivos }}</p>
                @endif

                @if($evolucao->cids)
                <div class="mt-4">
                    <span class="fw-semibold text-secondary small me-2">CIDs:</span>
                    @foreach($evolucao->cids as $cid)
                        <span class="badge bg-light text-dark border me-1">{{ $cid }}</span>
                    @endforeach
                </div>
                @endif

                @if($evolucao->sessao)
                <div class="mt-4 p-3 bg-light rounded small text-muted">
                    Vinculada à sessão de {{ $evolucao->sessao->data_hora->format('d/m/Y H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

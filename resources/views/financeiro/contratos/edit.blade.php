@extends('layouts.app')
@section('title', 'Editar Contrato')
@section('page-title', 'Editar Contrato')
@section('header-actions')
    <a href="{{ route('financeiro.contratos.show', $contrato) }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <p class="text-muted mb-3 small">
                    Paciente: <strong>{{ $contrato->paciente->nome }}</strong> —
                    Profissional: <strong>{{ $contrato->profissional->user->name }}</strong>
                </p>
                <form action="{{ route('financeiro.contratos.update', $contrato) }}" method="POST">
                    @csrf @method('PUT')
                    @include('financeiro.contratos._form', ['editando' => true])
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('financeiro.contratos.show', $contrato) }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

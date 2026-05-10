@extends('layouts.app')

@section('title', $paciente->nome)
@section('page-title', $paciente->nome)

@section('header-actions')
    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary btn-sm">← Voltar</a>
@endsection

@section('content')
{{-- Vista restrita para perfil financeiro: apenas dados de cobrança --}}
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Dados para cobrança</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Paciente</dt>
                    <dd class="col-sm-8">{{ $paciente->nome }}</dd>

                    <dt class="col-sm-4">Responsável</dt>
                    <dd class="col-sm-8">{{ $paciente->responsavel_nome }} ({{ $paciente->responsavel_parentesco }})</dd>

                    <dt class="col-sm-4">Celular</dt>
                    <dd class="col-sm-8">{{ $paciente->responsavel_celular }}</dd>

                    @if($paciente->responsavel_email)
                    <dt class="col-sm-4">E-mail</dt>
                    <dd class="col-sm-8">{{ $paciente->responsavel_email }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Novo Contrato')
@section('page-title', 'Novo Contrato')
@section('header-actions')
    <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('financeiro.contratos.store') }}" method="POST">
                    @csrf
                    @include('financeiro.contratos._form')
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Criar Contrato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

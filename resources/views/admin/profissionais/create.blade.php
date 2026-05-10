@extends('layouts.app')
@section('title', 'Novo Profissional')
@section('page-title', 'Novo Profissional')
@section('header-actions')
    <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('profissionais.store') }}" method="POST">
                    @csrf
                    @include('admin.profissionais._form')
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Cadastrar Profissional</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

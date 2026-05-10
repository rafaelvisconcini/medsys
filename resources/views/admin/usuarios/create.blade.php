@extends('layouts.app')
@section('title', 'Novo Usuário')
@section('page-title', 'Novo Usuário')
@section('header-actions')
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    @include('admin.usuarios._form')
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

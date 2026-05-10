@extends('layouts.app')
@section('title', 'Editar Usuário')
@section('page-title', 'Editar — ' . $usuario->name)
@section('header-actions')
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                    @csrf @method('PUT')
                    @include('admin.usuarios._form', ['editando' => true])
                    <div class="d-flex justify-content-between mt-4">
                        @if(auth()->id() !== $usuario->id)
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                              onsubmit="return confirm('Desativar este usuário?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Desativar</button>
                        </form>
                        @else
                        <div></div>
                        @endif
                        <div class="d-flex gap-2">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

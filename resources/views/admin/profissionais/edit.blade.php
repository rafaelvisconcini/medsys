@extends('layouts.app')
@section('title', 'Editar Profissional')
@section('page-title', 'Editar — ' . $profissional->user->name)
@section('header-actions')
    <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('profissionais.update', $profissional) }}" method="POST">
                    @csrf @method('PUT')
                    @include('admin.profissionais._form', ['editando' => true])
                    <div class="d-flex justify-content-between mt-4">
                        <form action="{{ route('profissionais.destroy', $profissional) }}" method="POST"
                              onsubmit="return confirm('Desativar este profissional?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Desativar</button>
                        </form>
                        <div class="d-flex gap-2">
                            <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

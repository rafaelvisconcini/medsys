@extends('layouts.app')

@section('title', 'Editar — ' . $paciente->nome)
@section('page-title', 'Editar Paciente')

@section('header-actions')
    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary btn-sm">← Voltar</a>
@endsection

@section('content')
<form method="POST" action="{{ route('pacientes.update', $paciente) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('pacientes._form')
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </div>
</form>
@endsection

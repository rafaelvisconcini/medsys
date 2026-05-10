@extends('layouts.app')

@section('title', 'Novo Paciente')
@section('page-title', 'Novo Paciente')

@section('header-actions')
    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary btn-sm">← Voltar</a>
@endsection

@section('content')
<form method="POST" action="{{ route('pacientes.store') }}" enctype="multipart/form-data">
    @csrf
    @include('pacientes._form')
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Salvar Paciente</button>
    </div>
</form>
@endsection

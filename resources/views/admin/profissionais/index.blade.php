@extends('layouts.app')
@section('title', 'Profissionais')
@section('page-title', 'Profissionais')
@section('header-actions')
    <a href="{{ route('profissionais.create') }}" class="btn btn-primary btn-sm">+ Novo Profissional</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Especialidade</th>
                    <th>Registro</th>
                    <th>Duração (min)</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($profissionais as $prof)
                <tr>
                    <td class="fw-semibold">{{ $prof->user->name }}</td>
                    <td>{{ $prof->especialidade->label() }}</td>
                    <td class="text-secondary small">{{ $prof->registro_profissional }}</td>
                    <td>{{ $prof->duracao_sessao_min }}</td>
                    <td>
                        @if($prof->ativo)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('profissionais.edit', $prof) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Nenhum profissional cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($profissionais->hasPages())
    <div class="card-footer bg-white">{{ $profissionais->links() }}</div>
    @endif
</div>
@endsection

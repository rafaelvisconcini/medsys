@extends('layouts.app')
@section('title', 'Usuários')
@section('page-title', 'Usuários')
@section('header-actions')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">+ Novo Usuário</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td class="text-secondary small">{{ $user->email }}</td>
                    <td>{{ $user->perfil->label() }}</td>
                    <td>
                        @if($user->ativo && !$user->trashed())
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if(!$user->trashed())
                        <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Nenhum usuário encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())
    <div class="card-footer bg-white">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection

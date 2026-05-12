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
                        <div class="d-flex gap-1 justify-content-end">
                            @if(!$user->trashed())
                            @if(!$user->isProprietario())
                            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            @endif
                            @if($user->id !== auth()->id() && !$user->isProprietario())
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                    data-action="{{ route('usuarios.destroy', $user) }}"
                                    data-name="{{ $user->name }}"
                                    title="Desativar">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                            @endif
                        </div>
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

<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content" style="border-radius:.75rem;border:none;">
            <div class="modal-body p-4 text-center">
                <div style="width:52px;height:52px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .875rem;">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:1.25rem;"></i>
                </div>
                <h6 class="fw-semibold mb-1">Desativar usuário?</h6>
                <p class="text-muted mb-0" style="font-size:.875rem;">
                    <strong id="deleteNome"></strong> perderá o acesso ao sistema.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sim, desativar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('deleteNome').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    });
});
</script>
@endpush
@endsection

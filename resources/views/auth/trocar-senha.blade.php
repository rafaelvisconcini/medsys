@extends('layouts.auth')

@section('title', 'Defina sua senha')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h5 class="card-title mb-1 fw-semibold">Defina sua nova senha</h5>
        <p class="text-muted small mb-4">Por segurança, você precisa criar uma senha pessoal antes de continuar.</p>

        @if(session('aviso'))
            <div class="alert alert-warning">{{ session('aviso') }}</div>
        @endif

        <form method="POST" action="{{ route('senha.atualizar') }}">
            @csrf

            <div class="mb-3">
                <label for="password" class="form-label">Nova senha</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password">
                <div class="form-text">Mínimo 8 caracteres, com letra maiúscula, número e símbolo.</div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                <input id="password_confirmation" type="password"
                       class="form-control"
                       name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Salvar senha</button>
        </form>
    </div>
</div>
@endsection

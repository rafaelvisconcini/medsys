@extends('layouts.auth')
@section('title', 'Entrar')

@section('content')
<h2 class="auth-form-title">Bem-vindo de volta</h2>
<p class="auth-form-subtitle">Acesse o sistema com suas credenciais</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-4">
        <label for="email" class="form-label">E-mail</label>
        <input id="email" type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" required autocomplete="email" autofocus
               placeholder="seu@email.com">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-5">
        <label for="password" class="form-label">Senha</label>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               required autocomplete="current-password" placeholder="••••••••">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex align-items-center justify-content-between mb-5">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember" style="font-size:.875rem;color:#4b5563;">Lembrar-me</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100" style="justify-content:center;padding:.625rem;">
        Entrar <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>
@endsection

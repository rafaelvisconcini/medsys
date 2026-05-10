@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Nome completo <span class="text-danger">*</span></label>
        <input type="text" name="name"
               value="{{ old('name', $usuario->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-7">
        <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
        <input type="email" name="email"
               value="{{ old('email', $usuario->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-5">
        <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
        <select name="perfil" class="form-select @error('perfil') is-invalid @enderror" required>
            @foreach ($perfis as $p)
                <option value="{{ $p->value }}" @selected(old('perfil', $usuario->perfil->value ?? '') === $p->value)>
                    {{ $p->label() }}
                </option>
            @endforeach
        </select>
        @error('perfil') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if(isset($editando))
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="ativo" id="ativo" class="form-check-input"
                   @checked(old('ativo', $usuario->ativo ?? true))>
            <label for="ativo" class="form-check-label">Usuário ativo</label>
        </div>
    </div>
    @else
    <div class="col-12">
        <div class="alert alert-info py-2 mb-0">
            <strong>Senha inicial:</strong> <code>Trocar@Primeiro1</code> — troca obrigatória no primeiro acesso.
        </div>
    </div>
    @endif
</div>

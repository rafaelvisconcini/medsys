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
               value="{{ old('name', $profissional->user->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
        <input type="email" name="email"
               value="{{ old('email', $profissional->user->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
        <select name="especialidade" class="form-select @error('especialidade') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach ($especialidades as $esp)
                <option value="{{ $esp->value }}"
                    @selected(old('especialidade', $profissional->especialidade->value ?? '') === $esp->value)>
                    {{ $esp->label() }}
                </option>
            @endforeach
        </select>
        @error('especialidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Registro profissional <span class="text-danger">*</span></label>
        <input type="text" name="registro_profissional"
               value="{{ old('registro_profissional', $profissional->registro_profissional ?? '') }}"
               class="form-control @error('registro_profissional') is-invalid @enderror"
               placeholder="Ex: CRFa 12345-RS" required>
        @error('registro_profissional') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Duração padrão da sessão (min) <span class="text-danger">*</span></label>
        <input type="number" name="duracao_sessao_min"
               value="{{ old('duracao_sessao_min', $profissional->duracao_sessao_min ?? 50) }}"
               min="15" max="240" step="5"
               class="form-control @error('duracao_sessao_min') is-invalid @enderror" required>
        @error('duracao_sessao_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if(isset($editando))
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="ativo" id="ativo" class="form-check-input"
                   @checked(old('ativo', $profissional->ativo ?? true))>
            <label for="ativo" class="form-check-label">Profissional ativo</label>
        </div>
    </div>
    @endif

    @if(!isset($editando))
    <div class="col-12">
        <div class="alert alert-info py-2 mb-0">
            <strong>Senha inicial:</strong> <code>Trocar@Primeiro1</code> — o profissional será obrigado a trocar no primeiro acesso.
        </div>
    </div>
    @endif
</div>

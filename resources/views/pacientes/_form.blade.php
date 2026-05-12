{{-- Partial compartilhado entre create e edit --}}

{{-- DADOS DA CRIANÇA --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Dados do Paciente</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                       value="{{ old('nome', $paciente->nome ?? '') }}" required>
                @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Data de nascimento <span class="text-danger">*</span></label>
                <input type="date" name="data_nascimento"
                       class="form-control @error('data_nascimento') is-invalid @enderror"
                       value="{{ old('data_nascimento', isset($paciente) ? $paciente->data_nascimento?->format('Y-m-d') : '') }}" required>
                @error('data_nascimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-select">
                    <option value="">Não informado</option>
                    <option value="M" @selected(old('sexo', $paciente->sexo ?? '') === 'M')>Masculino</option>
                    <option value="F" @selected(old('sexo', $paciente->sexo ?? '') === 'F')>Feminino</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Escola</label>
                <input type="text" name="escola" class="form-control"
                       value="{{ old('escola', $paciente->escola ?? '') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Série / Ano</label>
                <input type="text" name="serie_escolar" class="form-control"
                       value="{{ old('serie_escolar', $paciente->serie_escolar ?? '') }}"
                       placeholder="Ex: 3º ano EF">
            </div>

            <div class="col-md-3">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                       accept="image/*">
                @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if(isset($paciente) && $paciente->foto_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $paciente->foto_path) }}"
                             class="rounded" width="64" height="64" style="object-fit:cover;">
                        <span class="text-muted small ms-1">Foto atual</span>
                    </div>
                @endif
            </div>

            <div class="col-12">
                <label class="form-label">Diagnósticos</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach(['F84.0 — TEA', 'F90.0 — TDAH', 'F91.3 — TOD', 'F80 — Atraso de fala', 'F82 — Motor', 'F81 — Aprendizagem'] as $diag)
                        @php [$cid, $label] = explode(' — ', $diag) @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="diagnosticos[]"
                                   id="diag_{{ $cid }}" value="{{ $cid }}"
                                   @checked(in_array($cid, old('diagnosticos', $paciente->diagnosticos ?? [])))>
                            <label class="form-check-label" for="diag_{{ $cid }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Observações clínicas</label>
                <textarea name="observacoes" class="form-control" rows="3"
                          placeholder="Informações relevantes sobre o histórico do paciente...">{{ old('observacoes', $paciente->observacoes ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- RESPONSÁVEL --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Responsável <span class="text-danger">*</span></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="responsavel_nome"
                       class="form-control @error('responsavel_nome') is-invalid @enderror"
                       value="{{ old('responsavel_nome', $paciente->responsavel_nome ?? '') }}" required>
                @error('responsavel_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Parentesco <span class="text-danger">*</span></label>
                <select name="responsavel_parentesco"
                        class="form-select @error('responsavel_parentesco') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach(['Mãe', 'Pai', 'Avó', 'Avô', 'Tio(a)', 'Tutor(a)', 'Outro'] as $p)
                        <option value="{{ $p }}"
                            @selected(old('responsavel_parentesco', $paciente->responsavel_parentesco ?? '') === $p)>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
                @error('responsavel_parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">CPF</label>
                <input type="text" name="responsavel_cpf" class="form-control"
                       value="{{ old('responsavel_cpf', $paciente->responsavel_cpf ?? '') }}"
                       maxlength="11" placeholder="Somente números">
            </div>

            <div class="col-md-4">
                <label class="form-label">Celular <span class="text-danger">*</span></label>
                <input type="text" name="responsavel_celular"
                       class="form-control @error('responsavel_celular') is-invalid @enderror"
                       value="{{ old('responsavel_celular', $paciente->responsavel_celular ?? '') }}" required>
                @error('responsavel_celular') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Telefone fixo</label>
                <input type="text" name="responsavel_telefone"
                       class="form-control @error('responsavel_telefone') is-invalid @enderror"
                       value="{{ old('responsavel_telefone', $paciente->responsavel_telefone ?? '') }}">
                @error('responsavel_telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">E-mail</label>
                <input type="email" name="responsavel_email"
                       class="form-control @error('responsavel_email') is-invalid @enderror"
                       value="{{ old('responsavel_email', $paciente->responsavel_email ?? '') }}">
                @error('responsavel_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- CONTATO SECUNDÁRIO --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Contato Secundário <span class="text-muted fw-normal small">(opcional)</span></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Nome</label>
                <input type="text" name="contato2_nome"
                       class="form-control @error('contato2_nome') is-invalid @enderror"
                       value="{{ old('contato2_nome', $paciente->contato2_nome ?? '') }}">
                @error('contato2_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone</label>
                <input type="text" name="contato2_telefone"
                       class="form-control @error('contato2_telefone') is-invalid @enderror"
                       value="{{ old('contato2_telefone', $paciente->contato2_telefone ?? '') }}">
                @error('contato2_telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Parentesco</label>
                <input type="text" name="contato2_parentesco"
                       class="form-control @error('contato2_parentesco') is-invalid @enderror"
                       value="{{ old('contato2_parentesco', $paciente->contato2_parentesco ?? '') }}">
                @error('contato2_parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ENDEREÇO --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Endereço <span class="text-muted fw-normal small">(opcional)</span></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" id="cep" class="form-control"
                       value="{{ old('cep', $paciente->cep ?? '') }}" maxlength="8"
                       placeholder="Somente números">
            </div>
            <div class="col-md-6">
                <label class="form-label">Logradouro</label>
                <input type="text" name="logradouro" id="logradouro" class="form-control"
                       value="{{ old('logradouro', $paciente->logradouro ?? '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control"
                       value="{{ old('numero', $paciente->numero ?? '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Complemento</label>
                <input type="text" name="complemento" class="form-control"
                       value="{{ old('complemento', $paciente->complemento ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control"
                       value="{{ old('bairro', $paciente->bairro ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-control"
                       value="{{ old('cidade', $paciente->cidade ?? '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">UF</label>
                <input type="text" name="uf" id="uf" class="form-control"
                       value="{{ old('uf', $paciente->uf ?? '') }}" maxlength="2">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('cep')?.addEventListener('blur', function () {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(r => r.json())
        .then(d => {
            if (d.erro) return;
            document.getElementById('logradouro').value = d.logradouro || '';
            document.getElementById('bairro').value = d.bairro || '';
            document.getElementById('cidade').value = d.localidade || '';
            document.getElementById('uf').value = d.uf || '';
        });
});
</script>
@endpush

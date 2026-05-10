@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    @if(!isset($editando))
    <div class="col-12">
        <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
        <select name="paciente_id" class="form-select @error('paciente_id') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach($pacientes as $p)
            <option value="{{ $p->id }}" @selected(old('paciente_id', $contrato->paciente_id ?? '') == $p->id)>{{ $p->nome }}</option>
            @endforeach
        </select>
        @error('paciente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Profissional <span class="text-danger">*</span></label>
        <select name="profissional_id" id="sel-prof" class="form-select @error('profissional_id') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach($profissionais as $prof)
            <option value="{{ $prof->id }}" data-esp="{{ $prof->especialidade->value }}"
                @selected(old('profissional_id', $contrato->profissional_id ?? '') == $prof->id)>
                {{ $prof->user->name }}
            </option>
            @endforeach
        </select>
        @error('profissional_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
        <select name="especialidade" id="sel-esp" class="form-select @error('especialidade') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach($especialidades as $e)
            <option value="{{ $e->value }}" @selected(old('especialidade', $contrato->especialidade->value ?? '') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
        @error('especialidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    @endif

    <div class="col-md-4">
        <label class="form-label fw-semibold">Valor Mensal (R$) <span class="text-danger">*</span></label>
        <input type="number" name="valor_mensal" step="0.01" min="1"
               value="{{ old('valor_mensal', $contrato->valor_mensal ?? '') }}"
               class="form-control @error('valor_mensal') is-invalid @enderror" required>
        @error('valor_mensal') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Dia de vencimento <span class="text-danger">*</span></label>
        <input type="number" name="dia_vencimento" min="1" max="28"
               value="{{ old('dia_vencimento', $contrato->dia_vencimento ?? 10) }}"
               class="form-control @error('dia_vencimento') is-invalid @enderror" required>
        @error('dia_vencimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Sessões por semana <span class="text-danger">*</span></label>
        <input type="number" name="sessoes_por_semana" min="1" max="7"
               value="{{ old('sessoes_por_semana', $contrato->sessoes_por_semana ?? 2) }}"
               class="form-control @error('sessoes_por_semana') is-invalid @enderror" required>
        @error('sessoes_por_semana') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @if(!isset($editando))
    <div class="col-md-6">
        <label class="form-label fw-semibold">Data de início <span class="text-danger">*</span></label>
        <input type="date" name="data_inicio"
               value="{{ old('data_inicio', today()->format('Y-m-d')) }}"
               class="form-control @error('data_inicio') is-invalid @enderror" required>
        @error('data_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    @endif
    <div class="col-md-6">
        <label class="form-label fw-semibold">Data de encerramento <span class="text-muted fw-normal">(opcional)</span></label>
        <input type="date" name="data_fim"
               value="{{ old('data_fim', $contrato->data_fim?->format('Y-m-d') ?? '') }}"
               class="form-control">
    </div>

    @if(isset($editando))
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="ativo"     @selected(old('status', $contrato->status) === 'ativo')>Ativo</option>
            <option value="suspenso"  @selected(old('status', $contrato->status) === 'suspenso')>Suspenso</option>
            <option value="encerrado" @selected(old('status', $contrato->status) === 'encerrado')>Encerrado</option>
        </select>
    </div>
    @endif

    <div class="col-12">
        <label class="form-label fw-semibold">Observações</label>
        <textarea name="observacoes" rows="2" class="form-control" maxlength="1000">{{ old('observacoes', $contrato->observacoes ?? '') }}</textarea>
    </div>

    @if(!isset($editando))
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="gerar_primeira_cobranca" id="gpc" class="form-check-input" checked>
            <label for="gpc" class="form-check-label">Gerar primeira cobrança para o mês de início</label>
        </div>
    </div>
    @endif
</div>

@if(!isset($editando))
@push('scripts')
<script>
document.getElementById('sel-prof').addEventListener('change', function () {
    const esp = this.options[this.selectedIndex].dataset.esp;
    if (!esp) return;
    for (let o of document.getElementById('sel-esp').options) {
        if (o.value === esp) { o.selected = true; break; }
    }
});
</script>
@endpush
@endif

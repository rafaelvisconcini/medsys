@extends('layouts.app')

@section('title', 'Editar Sessão')
@section('page-title', 'Editar Sessão')

@section('header-actions')
    <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar à Agenda</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                {{-- Status rápido --}}
                @if ($sessao->status !== 'cancelada')
                <div class="d-flex gap-2 align-items-center mb-4 p-3 bg-light rounded">
                    <span class="fw-semibold text-secondary small me-2">Status:</span>
                    @foreach (['confirmada','realizada','faltou','cancelada'] as $st)
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-status"
                            data-status="{{ $st }}" data-id="{{ $sessao->id }}">
                        {{ ucfirst($st) }}
                    </button>
                    @endforeach
                    <span class="ms-auto badge fs-6"
                          style="background:{{ ['agendada'=>'#3b82f6','confirmada'=>'#10b981','realizada'=>'#6b7280','cancelada'=>'#ef4444','faltou'=>'#f59e0b','reposicao'=>'#8b5cf6'][$sessao->status] ?? '#3b82f6' }}">
                        {{ ucfirst($sessao->status) }}
                    </span>
                </div>
                @endif

                <form action="{{ route('sessoes.update', $sessao) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                            <select name="paciente_id" class="form-select @error('paciente_id') is-invalid @enderror" required>
                                @foreach ($pacientes as $pac)
                                    <option value="{{ $pac->id }}" @selected(old('paciente_id', $sessao->paciente_id) == $pac->id)>{{ $pac->nome }}</option>
                                @endforeach
                            </select>
                            @error('paciente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Profissional <span class="text-danger">*</span></label>
                            <select name="profissional_id" id="sel-profissional"
                                    class="form-select @error('profissional_id') is-invalid @enderror" required>
                                @foreach ($profissionais as $prof)
                                    <option value="{{ $prof->id }}"
                                            data-especialidade="{{ $prof->especialidade->value }}"
                                            data-duracao="{{ $prof->duracao_sessao_min }}"
                                            @selected(old('profissional_id', $sessao->profissional_id) == $prof->id)>
                                        {{ $prof->user->name }} ({{ $prof->especialidade->label() }})
                                    </option>
                                @endforeach
                            </select>
                            @error('profissional_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
                            <select name="especialidade" id="sel-especialidade"
                                    class="form-select @error('especialidade') is-invalid @enderror" required>
                                @foreach ($especialidades as $esp)
                                    <option value="{{ $esp->value }}" @selected(old('especialidade', $sessao->especialidade->value) == $esp->value)>
                                        {{ $esp->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('especialidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data e Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="data_hora"
                                   value="{{ old('data_hora', $sessao->data_hora->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('data_hora') is-invalid @enderror" required>
                            @error('data_hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Duração (minutos) <span class="text-danger">*</span></label>
                            <input type="number" name="duracao_min"
                                   value="{{ old('duracao_min', $sessao->duracao_min) }}"
                                   min="15" max="240" step="5"
                                   class="form-control @error('duracao_min') is-invalid @enderror" required>
                            @error('duracao_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="avulsa"    @selected(old('tipo',$sessao->tipo) === 'avulsa')>Avulsa</option>
                                <option value="plano"     @selected(old('tipo',$sessao->tipo) === 'plano')>Plano</option>
                                <option value="reposicao" @selected(old('tipo',$sessao->tipo) === 'reposicao')>Reposição</option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea name="observacoes" rows="2"
                                      class="form-control @error('observacoes') is-invalid @enderror"
                                      maxlength="1000">{{ old('observacoes', $sessao->observacoes) }}</textarea>
                            @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 d-flex gap-2 justify-content-between">
                            @can('delete', $sessao)
                            <form action="{{ route('sessoes.destroy', $sessao) }}" method="POST"
                                  onsubmit="return confirm('Remover esta sessão?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                            </form>
                            @endcan
                            <div class="d-flex gap-2 ms-auto">
                                <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-status').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!confirm(`Marcar como "${this.dataset.status}"?`)) return;
        fetch(`/sessoes/${this.dataset.id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status: this.dataset.status }),
        })
        .then(r => r.json())
        .then(() => window.location.reload());
    });
});
</script>
@endpush

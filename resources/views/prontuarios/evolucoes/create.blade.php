@extends('layouts.app')
@section('title', 'Nova Evolução')
@section('page-title', 'Nova Evolução — ' . $prontuario->paciente->nome)
@section('header-actions')
    <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('prontuarios.evolucoes.store', $prontuario) }}" method="POST">
                    @csrf
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        {{-- Especialidade e Data --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
                            <select name="especialidade" class="form-select @error('especialidade') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                @foreach ($especialidades as $esp)
                                    <option value="{{ $esp->value }}"
                                        @selected(old('especialidade') === $esp->value || auth()->user()->profissional?->especialidade->value === $esp->value)>
                                        {{ $esp->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('especialidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data e hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="data_hora"
                                   value="{{ old('data_hora', now()->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('data_hora') is-invalid @enderror" required>
                            @error('data_hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Sessão vinculada (opcional) --}}
                        @if($sessoesDisponiveis->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label fw-semibold">Vincular à sessão <span class="text-muted fw-normal">(opcional)</span></label>
                            <select name="sessao_id" class="form-select">
                                <option value="">Sem vínculo</option>
                                @foreach ($sessoesDisponiveis as $s)
                                <option value="{{ $s->id }}" @selected(old('sessao_id') == $s->id)>
                                    {{ $s->data_hora->format('d/m/Y H:i') }} — {{ $s->especialidade->label() }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Descrição --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição da sessão <span class="text-danger">*</span></label>
                            <textarea name="descricao" rows="5"
                                      class="form-control @error('descricao') is-invalid @enderror"
                                      placeholder="Descreva o que foi trabalhado na sessão..." required>{{ old('descricao') }}</textarea>
                            @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Objetivos trabalhados</label>
                            <textarea name="objetivos_trabalhados" rows="3"
                                      class="form-control">{{ old('objetivos_trabalhados') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Resposta do paciente</label>
                            <textarea name="resposta_paciente" rows="3"
                                      class="form-control">{{ old('resposta_paciente') }}</textarea>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Próximos objetivos</label>
                            <textarea name="proximos_objetivos" rows="2"
                                      class="form-control">{{ old('proximos_objetivos') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                CIDs
                                <span class="text-muted fw-normal small">(separados por vírgula)</span>
                            </label>
                            <input type="text" name="cids"
                                   value="{{ old('cids') }}"
                                   class="form-control"
                                   placeholder="Ex: F84.0, F90.0">
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Evolução</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

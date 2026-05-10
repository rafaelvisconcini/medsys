@extends('layouts.app')
@section('title', 'Editar Evolução')
@section('page-title', 'Editar Evolução')
@section('header-actions')
    <a href="{{ route('prontuarios.show', $evolucao->prontuario->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('evolucoes.update', $evolucao) }}" method="POST">
                    @csrf @method('PUT')

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
                            <select name="especialidade" class="form-select @error('especialidade') is-invalid @enderror" required>
                                @foreach ($especialidades as $esp)
                                    <option value="{{ $esp->value }}" @selected(old('especialidade', $evolucao->especialidade->value) === $esp->value)>
                                        {{ $esp->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data e hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="data_hora"
                                   value="{{ old('data_hora', $evolucao->data_hora->format('Y-m-d\TH:i')) }}"
                                   class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
                            <textarea name="descricao" rows="5"
                                      class="form-control @error('descricao') is-invalid @enderror" required>{{ old('descricao', $evolucao->descricao) }}</textarea>
                            @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Objetivos trabalhados</label>
                            <textarea name="objetivos_trabalhados" rows="3"
                                      class="form-control">{{ old('objetivos_trabalhados', $evolucao->objetivos_trabalhados) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Resposta do paciente</label>
                            <textarea name="resposta_paciente" rows="3"
                                      class="form-control">{{ old('resposta_paciente', $evolucao->resposta_paciente) }}</textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Próximos objetivos</label>
                            <textarea name="proximos_objetivos" rows="2"
                                      class="form-control">{{ old('proximos_objetivos', $evolucao->proximos_objetivos) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">CIDs <span class="text-muted fw-normal small">(separados por vírgula)</span></label>
                            <input type="text" name="cids"
                                   value="{{ old('cids', implode(', ', $evolucao->cids ?? [])) }}"
                                   class="form-control">
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                            @can('delete', $evolucao)
                            <form action="{{ route('evolucoes.destroy', $evolucao) }}" method="POST"
                                  onsubmit="return confirm('Remover esta evolução permanentemente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                            </form>
                            @endcan
                            <div class="d-flex gap-2 ms-auto">
                                <a href="{{ route('prontuarios.show', $evolucao->prontuario->paciente_id) }}" class="btn btn-outline-secondary">Cancelar</a>
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

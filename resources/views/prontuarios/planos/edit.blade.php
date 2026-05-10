@extends('layouts.app')
@section('title', 'Editar Plano Terapêutico')
@section('page-title', 'Editar Plano — ' . $plano->paciente->nome)
@section('header-actions')
    <a href="{{ route('prontuarios.show', $plano->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('prontuarios.planos.update', $plano) }}" method="POST">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            {{-- Dados gerais do plano --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Dados do Plano</div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo"
                                   value="{{ old('titulo', $plano->titulo) }}"
                                   class="form-control @error('titulo') is-invalid @enderror"
                                   maxlength="150" required>
                            @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Início <span class="text-danger">*</span></label>
                            <input type="date" name="periodo_inicio"
                                   value="{{ old('periodo_inicio', $plano->periodo_inicio->format('Y-m-d')) }}"
                                   class="form-control @error('periodo_inicio') is-invalid @enderror" required>
                            @error('periodo_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Término <span class="text-muted fw-normal">(opcional)</span></label>
                            <input type="date" name="periodo_fim"
                                   value="{{ old('periodo_fim', $plano->periodo_fim?->format('Y-m-d')) }}"
                                   class="form-control @error('periodo_fim') is-invalid @enderror">
                            @error('periodo_fim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="ativo"      @selected(old('status', $plano->status) === 'ativo')>Ativo</option>
                                <option value="suspenso"   @selected(old('status', $plano->status) === 'suspenso')>Suspenso</option>
                                <option value="finalizado" @selected(old('status', $plano->status) === 'finalizado')>Finalizado</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Especialidades --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Especialidades envolvidas</span>
                    <button type="button" id="btn-add-esp" class="btn btn-outline-primary btn-sm">
                        + Adicionar especialidade
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="especialidades-container">
                        @php $espList = old('especialidades') ?? $plano->especialidades->map(fn($e) => [
                            'profissional_id'       => $e->profissional_id,
                            'especialidade'         => $e->especialidade->value,
                            'objetivos_gerais'      => $e->objetivos_gerais,
                            'objetivos_especificos' => $e->objetivos_especificos,
                            'estrategias'           => $e->estrategias,
                        ])->toArray(); @endphp

                        @forelse($espList as $i => $esp)
                            @include('prontuarios.planos._especialidade-row', [
                                'index'         => $i,
                                'profissionais' => $profissionais,
                                'especialidades'=> $especialidades,
                                'valores'       => $esp,
                            ])
                        @empty
                            <p class="text-muted text-center py-3 mb-0" id="empty-msg">
                                Nenhuma especialidade adicionada. Use o botão acima para incluir.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('prontuarios.show', $plano->paciente_id) }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<template id="esp-row-template">
    @include('prontuarios.planos._especialidade-row', [
        'index'         => '__INDEX__',
        'profissionais' => $profissionais,
        'especialidades'=> $especialidades,
        'valores'       => [],
    ])
</template>

@push('scripts')
<script>
(function () {
    let index = {{ count($espList) }};

    const container = document.getElementById('especialidades-container');
    const template  = document.getElementById('esp-row-template');
    const btnAdd    = document.getElementById('btn-add-esp');

    function addRow() {
        const html = template.innerHTML.replaceAll('__INDEX__', index++);
        const tmp  = document.createElement('div');
        tmp.innerHTML = html;
        const row = tmp.firstElementChild;
        container.appendChild(row);
        row.querySelector('.btn-remove-esp').addEventListener('click', () => removeRow(row));

        const emptyMsg = container.querySelector('p.text-muted');
        if (emptyMsg) emptyMsg.remove();
    }

    function removeRow(row) {
        row.remove();
        if (container.querySelectorAll('.esp-row').length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3 mb-0">Nenhuma especialidade adicionada. Use o botão acima para incluir.</p>';
        }
    }

    btnAdd.addEventListener('click', addRow);

    container.querySelectorAll('.btn-remove-esp').forEach(btn => {
        btn.addEventListener('click', () => removeRow(btn.closest('.esp-row')));
    });
})();
</script>
@endpush
@endsection

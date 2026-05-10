@extends('layouts.app')
@section('title', 'Novo Encaminhamento')
@section('page-title', 'Novo Encaminhamento')
@section('header-actions')
    <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Paciente: <strong>{{ $prontuario->paciente->nome }}</strong>
                </p>
                <form action="{{ route('prontuarios.encaminhamentos.store', $prontuario) }}" method="POST">
                    @csrf
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Encaminhar para <span class="text-danger">*</span></label>
                            <input type="text" name="para_especialidade"
                                   value="{{ old('para_especialidade') }}"
                                   class="form-control @error('para_especialidade') is-invalid @enderror"
                                   placeholder="Ex: Neuropediatra, Psiquiatria Infantil"
                                   required>
                            @error('para_especialidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
                            <input type="date" name="data"
                                   value="{{ old('data', today()->format('Y-m-d')) }}"
                                   class="form-control @error('data') is-invalid @enderror" required>
                            @error('data') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motivo do encaminhamento <span class="text-danger">*</span></label>
                            <textarea name="motivo" rows="4"
                                      class="form-control @error('motivo') is-invalid @enderror"
                                      required>{{ old('motivo') }}</textarea>
                            @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações adicionais</label>
                            <textarea name="observacoes" rows="2"
                                      class="form-control">{{ old('observacoes') }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Encaminhamento</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

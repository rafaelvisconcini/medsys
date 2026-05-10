@extends('layouts.app')
@section('title', 'Nova Cobrança')
@section('page-title', 'Nova Cobrança Manual')
@section('header-actions')
    <a href="{{ route('financeiro.contas.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('financeiro.contas.store') }}" method="POST">
                    @csrf
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                            <select name="paciente_id" class="form-select @error('paciente_id') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                @foreach($pacientes as $p)
                                <option value="{{ $p->id }}" @selected(old('paciente_id') == $p->id)>{{ $p->nome }}</option>
                                @endforeach
                            </select>
                            @error('paciente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" value="{{ old('descricao') }}"
                                   class="form-control @error('descricao') is-invalid @enderror"
                                   maxlength="200" required>
                            @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor Total (R$) <span class="text-danger">*</span></label>
                            <input type="number" name="valor_total" step="0.01" min="0.01"
                                   value="{{ old('valor_total') }}"
                                   class="form-control @error('valor_total') is-invalid @enderror" required>
                            @error('valor_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nº de Parcelas <span class="text-danger">*</span></label>
                            <input type="number" name="num_parcelas" min="1" max="12"
                                   value="{{ old('num_parcelas', 1) }}"
                                   class="form-control @error('num_parcelas') is-invalid @enderror" required>
                            @error('num_parcelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select">
                                <option value="avulso"      @selected(old('tipo','avulso') === 'avulso')>Avulso</option>
                                <option value="mensalidade" @selected(old('tipo') === 'mensalidade')>Mensalidade</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vencimento (1ª parcela) <span class="text-danger">*</span></label>
                            <input type="date" name="data_vencimento"
                                   value="{{ old('data_vencimento', today()->format('Y-m-d')) }}"
                                   class="form-control @error('data_vencimento') is-invalid @enderror" required>
                            @error('data_vencimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea name="observacoes" rows="2" class="form-control">{{ old('observacoes') }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('financeiro.contas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Criar Cobrança</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

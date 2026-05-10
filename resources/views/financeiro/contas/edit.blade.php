@extends('layouts.app')
@section('title', 'Editar Cobrança')
@section('page-title', 'Editar Cobrança')
@section('header-actions')
    <a href="{{ route('financeiro.contas.show', $conta) }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Paciente: <strong>{{ $conta->paciente->nome }}</strong></p>
                <form action="{{ route('financeiro.contas.update', $conta) }}" method="POST">
                    @csrf @method('PUT')
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" value="{{ old('descricao', $conta->descricao) }}"
                                   class="form-control" maxlength="200" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vencimento <span class="text-danger">*</span></label>
                            <input type="date" name="data_vencimento"
                                   value="{{ old('data_vencimento', $conta->data_vencimento->format('Y-m-d')) }}"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pendente','parcial','quitado','cancelado'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $conta->status) === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea name="observacoes" rows="2" class="form-control">{{ old('observacoes', $conta->observacoes) }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('financeiro.contas.show', $conta) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

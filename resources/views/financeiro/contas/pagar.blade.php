@extends('layouts.app')
@section('title', 'Registrar Pagamento')
@section('page-title', 'Registrar Pagamento')
@section('header-actions')
    <a href="{{ route('financeiro.contas.show', $conta) }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="bg-light rounded p-3 mb-4 small">
                    <div><strong>Paciente:</strong> {{ $conta->paciente->nome }}</div>
                    <div><strong>Cobrança:</strong> {{ $conta->descricao }}</div>
                    <div><strong>Parcela {{ $parcela->numero_parcela }}:</strong>
                        R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                        — venc. {{ $parcela->data_vencimento->format('d/m/Y') }}
                    </div>
                </div>

                <form action="{{ route('financeiro.parcelas.pagar', [$conta, $parcela]) }}" method="POST">
                    @csrf
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data do pagamento <span class="text-danger">*</span></label>
                            <input type="date" name="data_pagamento"
                                   value="{{ old('data_pagamento', today()->format('Y-m-d')) }}"
                                   max="{{ today()->format('Y-m-d') }}"
                                   class="form-control @error('data_pagamento') is-invalid @enderror" required>
                            @error('data_pagamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Forma de pagamento <span class="text-danger">*</span></label>
                            <select name="forma_pagamento" class="form-select @error('forma_pagamento') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                @foreach(\App\Models\Parcela::formasPagamento() as $val => $label)
                                <option value="{{ $val }}" @selected(old('forma_pagamento') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('forma_pagamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações <span class="text-muted fw-normal">(opcional)</span></label>
                            <input type="text" name="observacoes" value="{{ old('observacoes') }}"
                                   class="form-control" maxlength="200">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('financeiro.contas.show', $conta) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

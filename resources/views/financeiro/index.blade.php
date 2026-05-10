@extends('layouts.app')
@section('title', 'Financeiro')
@section('page-title', 'Financeiro')

@section('header-actions')
    <a href="{{ route('financeiro.contas.create') }}" class="btn btn-outline-primary btn-sm">+ Cobrança Manual</a>
    <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-primary btn-sm">Contratos</a>
@endsection

@section('content')

{{-- Cards de resumo --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#198754" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Recebido este mês</div>
                    <div class="fs-5 fw-bold text-success">R$ {{ number_format($recebidoMes, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#ffc107" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Total a receber</div>
                    <div class="fs-5 fw-bold">R$ {{ number_format($pendenteTotal, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#dc3545" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Em atraso</div>
                    <div class="fs-5 fw-bold text-danger">{{ $atrasadasCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#0d6efd" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Vencem em 7 dias</div>
                    <div class="fs-5 fw-bold text-primary">{{ $vencendoSemana }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Contas em atraso --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span class="text-danger">Em Atraso</span>
                <a href="{{ route('financeiro.contas.index', ['status' => 'pendente']) }}" class="btn btn-sm btn-link text-muted p-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @forelse ($atrasadas as $c)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $c->paciente->nome }}</div>
                        <div class="text-muted" style="font-size:.8rem;">{{ $c->descricao }}</div>
                    </div>
                    <div class="text-end ms-3">
                        <div class="fw-bold text-danger small">R$ {{ number_format($c->valor_total, 2, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $c->data_vencimento->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-danger ms-2">Pagar</a>
                </div>
                @empty
                <p class="text-center text-muted py-4 mb-0">Nenhuma conta em atraso.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Próximos vencimentos --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Vencendo em 7 dias</span>
                <a href="{{ route('financeiro.contas.index') }}" class="btn btn-sm btn-link text-muted p-0">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @forelse ($proximasVencer as $c)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $c->paciente->nome }}</div>
                        <div class="text-muted" style="font-size:.8rem;">{{ $c->descricao }}</div>
                    </div>
                    <div class="text-end ms-3">
                        <div class="fw-bold small">R$ {{ number_format($c->valor_total, 2, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $c->data_vencimento->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-primary ms-2">Ver</a>
                </div>
                @empty
                <p class="text-center text-muted py-4 mb-0">Nenhum vencimento nos próximos 7 dias.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimos pagamentos --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Últimos Pagamentos</div>
            <div class="card-body p-0">
                @forelse ($ultimosPagamentos as $p)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <span class="badge bg-success me-3">Pago</span>
                    <div class="flex-grow-1">
                        <div class="small fw-semibold">{{ $p->conta->paciente->nome }}</div>
                        <div class="text-muted" style="font-size:.8rem;">{{ $p->conta->descricao }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold small text-success">R$ {{ number_format($p->valor, 2, ',', '.') }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $p->data_pagamento->format('d/m/Y') }}
                            — {{ \App\Models\Parcela::formasPagamento()[$p->forma_pagamento] ?? $p->forma_pagamento }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-4 mb-0">Nenhum pagamento registrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

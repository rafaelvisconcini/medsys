@extends('layouts.app')
@section('title', 'Financeiro')
@section('page-title', 'Financeiro')

@section('header-actions')
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalExtrato">
        <i class="bi bi-file-earmark-pdf"></i> Extrato PDF
    </button>
    <a href="{{ route('financeiro.relatorios.inadimplencia') }}" class="btn btn-sm btn-outline-danger" target="_blank">
        <i class="bi bi-exclamation-triangle"></i> Inadimplência
    </a>
    <a href="{{ route('financeiro.contas.create') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-plus-lg"></i> Cobrança Manual
    </a>
    <a href="{{ route('financeiro.contratos.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-file-earmark-text"></i> Contratos
    </a>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdf4;">
                    <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Recebido este mês</div>
                    <div style="font-size:1.375rem;font-weight:700;color:#16a34a;line-height:1.2;">
                        R$ {{ number_format($recebidoMes, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fffbeb;">
                    <i class="bi bi-hourglass-split" style="color:#d97706;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Total a receber</div>
                    <div style="font-size:1.375rem;font-weight:700;color:#111827;line-height:1.2;">
                        R$ {{ number_format($pendenteTotal, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff1f2;">
                    <i class="bi bi-exclamation-circle-fill" style="color:#dc2626;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Em atraso</div>
                    <div style="font-size:1.375rem;font-weight:700;color:#dc2626;line-height:1.2;">{{ $atrasadasCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#eff6ff;">
                    <i class="bi bi-calendar-event-fill" style="color:#2563eb;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Vencem em 7 dias</div>
                    <div style="font-size:1.375rem;font-weight:700;color:#2563eb;line-height:1.2;">{{ $vencendoSemana }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Em atraso --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span style="color:#dc2626;"><i class="bi bi-exclamation-circle me-2"></i>Em Atraso</span>
                <a href="{{ route('financeiro.contas.index', ['status' => 'pendente']) }}"
                   class="btn btn-sm btn-link" style="font-size:.8125rem;">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @forelse ($atrasadas as $c)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:.875rem;color:#111827;">{{ $c->paciente->nome }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $c->descricao }}</div>
                    </div>
                    <div class="text-end me-3">
                        <div style="font-weight:700;font-size:.875rem;color:#dc2626;">
                            R$ {{ number_format($c->valor_total, 2, ',', '.') }}
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $c->data_vencimento->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-danger">
                        Pagar
                    </a>
                </div>
                @empty
                <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
                    <i class="bi bi-check-circle" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:.875rem;">Nenhuma conta em atraso.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Próximos vencimentos --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2" style="color:#6b7280;"></i>Vencendo em 7 dias</span>
                <a href="{{ route('financeiro.contas.index') }}" class="btn btn-sm btn-link" style="font-size:.8125rem;">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @forelse ($proximasVencer as $c)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:.875rem;color:#111827;">{{ $c->paciente->nome }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $c->descricao }}</div>
                    </div>
                    <div class="text-end me-3">
                        <div style="font-weight:700;font-size:.875rem;color:#111827;">
                            R$ {{ number_format($c->valor_total, 2, ',', '.') }}
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $c->data_vencimento->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('financeiro.contas.show', $c) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                </div>
                @empty
                <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
                    <i class="bi bi-calendar-check" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:.875rem;">Nenhum vencimento nos próximos 7 dias.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimos pagamentos --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2" style="color:#6b7280;"></i>Últimos Pagamentos
            </div>
            <div class="card-body p-0">
                @forelse ($ultimosPagamentos as $p)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <span class="badge bg-success me-3">Pago</span>
                    <div style="flex:1;">
                        <div style="font-size:.875rem;font-weight:600;color:#111827;">{{ $p->conta->paciente->nome }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $p->conta->descricao }}</div>
                    </div>
                    <div class="text-end">
                        <div style="font-weight:700;font-size:.875rem;color:#16a34a;">
                            R$ {{ number_format($p->valor, 2, ',', '.') }}
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">
                            {{ $p->data_pagamento->format('d/m/Y') }}
                            &middot; {{ \App\Models\Parcela::formasPagamento()[$p->forma_pagamento] ?? $p->forma_pagamento }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
                    <i class="bi bi-cash" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:.875rem;">Nenhum pagamento registrado.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modal extrato --}}
<div class="modal fade" id="modalExtrato" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:.75rem;border:1px solid #e5e7eb;">
            <div class="modal-header" style="border-bottom:1px solid #e5e7eb;padding:1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-size:1rem;font-weight:600;">Gerar Extrato de Recebimentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('financeiro.relatorios.extrato') }}" method="GET" target="_blank">
                <div class="modal-body" style="padding:1.5rem;">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">De</label>
                            <input type="date" name="de" class="form-control"
                                   value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Até</label>
                            <input type="date" name="ate" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb;padding:1rem 1.5rem;gap:.5rem;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Gerar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

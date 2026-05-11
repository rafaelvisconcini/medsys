@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#eff6ff;">
                    <i class="bi bi-people-fill" style="color:#2563eb;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Pacientes Ativos</div>
                    <div style="font-size:1.75rem;font-weight:700;color:#111827;line-height:1.2;">{{ $totalPacientes }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdf4;">
                    <i class="bi bi-calendar-check-fill" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Sessões Hoje</div>
                    <div style="font-size:1.75rem;font-weight:700;color:#111827;line-height:1.2;">{{ $sessoesHoje }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fffbeb;">
                    <i class="bi bi-calendar-week-fill" style="color:#d97706;"></i>
                </div>
                <div>
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Esta Semana</div>
                    <div style="font-size:1.75rem;font-weight:700;color:#111827;line-height:1.2;">{{ $sessoesSemana }}</div>
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
                    <div style="font-size:.75rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:.04em;">Contas em Aberto</div>
                    <div style="font-size:1.75rem;font-weight:700;color:#111827;line-height:1.2;">{{ $contasAbertas }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Sessões de hoje --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Sessões de Hoje</span>
                @can('agendar')
                <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;">
                    Ver agenda <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                @if($proximasSessoes->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                        <i class="bi bi-calendar3" style="font-size:2rem;opacity:.3;"></i>
                        <span class="mt-2" style="font-size:.875rem;">Nenhuma sessão agendada para hoje.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Horário</th>
                                    <th>Paciente</th>
                                    <th>Especialidade</th>
                                    <th>Profissional</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proximasSessoes as $sessao)
                                <tr>
                                    <td style="font-weight:600;">{{ \Carbon\Carbon::parse($sessao->data_hora)->format('H:i') }}</td>
                                    <td>{{ $sessao->paciente->nome }}</td>
                                    <td style="color:#6b7280;">{{ $sessao->profissional->especialidade->label() }}</td>
                                    <td style="color:#6b7280;">{{ $sessao->profissional->user->name }}</td>
                                    <td>
                                        @php
                                            $statusStyles = [
                                                'realizada'  => 'background:#dcfce7;color:#166534;',
                                                'cancelada'  => 'background:#fee2e2;color:#991b1b;',
                                                'faltou'     => 'background:#fee2e2;color:#991b1b;',
                                            ];
                                            $style = $statusStyles[$sessao->status] ?? 'background:#fef9c3;color:#854d0e;';
                                        @endphp
                                        <span class="badge" style="{{ $style }}">{{ ucfirst($sessao->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Especialidades --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">Especialidades Ativas</div>
            <div class="card-body">
                @forelse($especialidades as $esp)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div style="font-size:.875rem;font-weight:500;color:#111827;">{{ $esp['label'] }}</div>
                    </div>
                    <span class="badge bg-primary" style="border-radius:999px;">
                        {{ $esp['total'] }} paciente{{ $esp['total'] !== 1 ? 's' : '' }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.875rem;">
                    Nenhuma especialidade ativa.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
